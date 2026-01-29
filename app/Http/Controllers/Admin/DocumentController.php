<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetVendor;
use App\Models\ContractGroup;
use App\Models\ContractTerms;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Location;
use App\Support\MenuAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    private const ALLOWED_TYPES = [
        'Contract',
        'Quotation',
        'PO',
        'Invoice',
        'Payment',
        'Subscription',
        'Renewal',
        'Addendum',
        'NDA',
        'Other',
    ];

    private const ALLOWED_CONFIDENTIALITY = ['Internal', 'Confidential', 'Restricted'];

    private const ALLOWED_STATUS = ['Draft', 'Active', 'Expired', 'Terminated', 'Archived'];

    private function canSeeRestricted(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if (($user->role?->role_name ?? null) === 'Super Admin') {
            return true;
        }

        return MenuAccess::can($user, 'documents_restricted', 'read');
    }

    private function assertCanAccessDocument(Document $document): void
    {
        if ($document->confidentiality_level === 'Restricted' && !$this->canSeeRestricted()) {
            abort(403);
        }
    }

    public function dashboard(Request $request)
    {
        $canSeeRestricted = $this->canSeeRestricted();

        $today = now()->toDateString();
        $plus90 = now()->addDays(90)->toDateString();

        $expiring = Document::query()
            ->visibleTo(Auth::user(), $canSeeRestricted)
            ->with(['vendor', 'contractTerms'])
            ->whereHas('contractTerms', function ($q) use ($today, $plus90) {
                $q->whereNotNull('end_date')
                    ->whereBetween('end_date', [$today, $plus90]);
            })
            ->whereIn('status', ['Active', 'Draft'])
            ->orderBy('status')
            ->limit(20)
            ->get();

        $latest = Document::query()
            ->visibleTo(Auth::user(), $canSeeRestricted)
            ->with(['vendor'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $activeByMonth = ContractTerms::query()
            ->join('m_igi_documents as d', 'd.document_id', '=', 'm_igi_contract_terms.document_id')
            ->whereNull('d.deleted_at')
            ->when(!$canSeeRestricted, fn($q) => $q->where('d.confidentiality_level', '!=', 'Restricted'))
            ->whereIn('d.document_type', ['Subscription', 'Contract'])
            ->where('d.status', 'Active')
            ->whereNotNull('m_igi_contract_terms.end_date')
            ->selectRaw("DATE_FORMAT(m_igi_contract_terms.end_date, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->limit(12)
            ->get();

        return view('pages.admin.documents.documents_dashboard', compact('expiring', 'latest', 'activeByMonth'));
    }

    public function index(Request $request)
    {
        $canSeeRestricted = $this->canSeeRestricted();

        $trashedMode = $request->string('trashed')->toString();
        if ($trashedMode === 'only' && !MenuAccess::can(Auth::user(), 'documents_archive', 'update')) {
            abort(403);
        }

        $vendors = AssetVendor::query()->where('is_active', true)->orderBy('name')->get();
        $locations = Location::query()->active()->orderBy('plant_site')->orderBy('name')->get();
        if ($locations->isEmpty()) {
            $locations = Location::query()->orderBy('plant_site')->orderBy('name')->get();
        }

        $q = Document::query()
            ->visibleTo(Auth::user(), $canSeeRestricted)
            ->with(['vendor', 'contractTerms', 'sites']);

        if ($trashedMode === 'only') {
            $q->onlyTrashed();
        }

        if ($request->filled('vendor_id')) {
            $q->where('vendor_id', (int) $request->input('vendor_id'));
        }
        if ($request->filled('document_type')) {
            $q->where('document_type', $request->string('document_type')->toString());
        }
        if ($request->filled('status')) {
            $q->where('status', $request->string('status')->toString());
        }
        if ($request->filled('location_id')) {
            $locId = (int) $request->input('location_id');
            $q->whereHas('sites', fn($qq) => $qq->where('m_igi_locations.id', $locId));
        }
        if ($request->filled('contract_end_from') || $request->filled('contract_end_to')) {
            $from = $request->input('contract_end_from');
            $to = $request->input('contract_end_to');
            $q->whereHas('contractTerms', function ($qq) use ($from, $to) {
                if (!empty($from)) {
                    $qq->where('end_date', '>=', $from);
                }
                if (!empty($to)) {
                    $qq->where('end_date', '<=', $to);
                }
            });
        }
        if ($request->filled('min_value') || $request->filled('max_value')) {
            $min = $request->filled('min_value') ? (float) $request->input('min_value') : null;
            $max = $request->filled('max_value') ? (float) $request->input('max_value') : null;
            $q->whereHas('contractTerms', function ($qq) use ($min, $max) {
                if ($min !== null) {
                    $qq->where('contract_value', '>=', $min);
                }
                if ($max !== null) {
                    $qq->where('contract_value', '<=', $max);
                }
            });
        }
        if ($request->filled('tag')) {
            $q->whereJsonContains('tags', $request->string('tag')->toString());
        }
        if ($request->filled('q')) {
            $kw = $request->string('q')->toString();
            $q->where(function ($qq) use ($kw) {
                $qq->where('document_title', 'like', '%' . $kw . '%')
                    ->orWhere('document_number', 'like', '%' . $kw . '%')
                    ->orWhere('description', 'like', '%' . $kw . '%')
                    ->orWhereHas('vendor', fn($vv) => $vv->where('name', 'like', '%' . $kw . '%'));
            });
        }

        $documents = $q->orderByDesc('updated_at')->limit(500)->get();

        $documentTypes = self::ALLOWED_TYPES;
        $statuses = self::ALLOWED_STATUS;

        return view('pages.admin.documents.documents_index', compact('documents', 'vendors', 'locations', 'documentTypes', 'statuses'));
    }

    public function create(Request $request)
    {
        $vendors = AssetVendor::query()->where('is_active', true)->orderBy('name')->get();
        $locations = Location::query()->active()->orderBy('plant_site')->orderBy('name')->get();
        if ($locations->isEmpty()) {
            $locations = Location::query()->orderBy('plant_site')->orderBy('name')->get();
        }
        $departments = Department::query()->orderBy('name')->get();
        $assets = Asset::query()->orderByDesc('id')->limit(500)->get();

        $documentTypes = self::ALLOWED_TYPES;
        $confidentialities = self::ALLOWED_CONFIDENTIALITY;

        return view('pages.admin.documents.documents_create', compact('vendors', 'locations', 'departments', 'assets', 'documentTypes', 'confidentialities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'document_title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:50'],
            'document_type' => ['required', Rule::in(self::ALLOWED_TYPES)],
            'vendor_id' => ['required', 'integer', 'exists:m_igi_asset_vendors,id'],
            'contract_group_id' => ['nullable', 'uuid', 'exists:m_igi_contract_groups,id'],
            'create_contract_group' => ['nullable', 'boolean'],
            'department_owner_id' => ['nullable', 'integer', 'exists:m_igi_departments,id'],
            'pic_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', Rule::in(self::ALLOWED_STATUS)],
            'confidentiality_level' => ['required', Rule::in(self::ALLOWED_CONFIDENTIALITY)],
            'tags' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'location_ids' => ['nullable', 'array'],
            'location_ids.*' => ['integer', 'exists:m_igi_locations,id'],
            'new_locations' => ['nullable', 'string', 'max:1000'],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['integer', 'exists:m_igi_asset,id'],

            // Contract terms
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'renewal_type' => ['nullable', 'string', 'max:20'],
            'notice_period_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'billing_cycle' => ['nullable', 'string', 'max:20'],
            'contract_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'payment_terms' => ['nullable', 'string', 'max:200'],
            'scope_service' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],

            // Initial file
            'file' => ['required', 'file', 'max:20480', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png'],
        ]);

        if (($validated['confidentiality_level'] ?? 'Internal') === 'Restricted' && !$this->canSeeRestricted()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membuat dokumen Restricted.')->withInput();
        }

        $tags = [];
        if (!empty($validated['tags'])) {
            $tags = collect(explode(',', (string) $validated['tags']))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $created = DB::transaction(function () use ($validated, $tags, $user, $request) {
            $vendor = AssetVendor::query()->findOrFail((int) $validated['vendor_id']);

            $groupId = $validated['contract_group_id'] ?? null;
            $createGroup = (bool) ($validated['create_contract_group'] ?? false);
            if ($createGroup || empty($groupId)) {
                $group = ContractGroup::query()->create([
                    'title' => $vendor->name,
                    'vendor_id' => (int) $vendor->id,
                ]);
                $groupId = $group->id;
            }

            $docNumber = $validated['document_number'] ?? null;
            if (empty($docNumber)) {
                $docNumber = $this->generateDocumentNumber();
            }

            $doc = Document::query()->create([
                'document_number' => $docNumber,
                'document_title' => $validated['document_title'],
                'document_type' => $validated['document_type'],
                'vendor_id' => (int) $validated['vendor_id'],
                'contract_group_id' => $groupId,
                'department_owner_id' => $validated['department_owner_id'] ?? null,
                'pic_user_id' => $validated['pic_user_id'] ?? null,
                'status' => $validated['status'],
                'confidentiality_level' => $validated['confidentiality_level'],
                'tags' => $tags,
                'description' => $validated['description'] ?? null,
                'created_by' => $user?->id,
                'updated_by' => $user?->id,
            ]);

            $locationIds = $validated['location_ids'] ?? [];
            if (!empty($validated['new_locations'])) {
                $newIds = collect(explode(',', (string) $validated['new_locations']))
                    ->map(fn($v) => trim($v))
                    ->filter()
                    ->unique()
                    ->map(function (string $label) {
                        $loc = Location::query()->firstOrCreate(
                            ['plant_site' => $label, 'name' => $label],
                            ['is_active' => true]
                        );
                        return (int) $loc->id;
                    })
                    ->values()
                    ->all();

                $locationIds = array_values(array_unique(array_merge($locationIds, $newIds)));
            }

            $doc->sites()->sync($locationIds);
            $doc->assets()->sync($validated['asset_ids'] ?? []);

            $hasTerms = !empty($validated['start_date'])
                || !empty($validated['end_date'])
                || !empty($validated['contract_value'])
                || !empty($validated['billing_cycle'])
                || !empty($validated['renewal_type']);

            if ($hasTerms) {
                ContractTerms::query()->create([
                    'document_id' => $doc->document_id,
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'renewal_type' => $validated['renewal_type'] ?? null,
                    'notice_period_days' => $validated['notice_period_days'] ?? null,
                    'billing_cycle' => $validated['billing_cycle'] ?? null,
                    'contract_value' => $validated['contract_value'] ?? null,
                    'currency' => $validated['currency'] ?? null,
                    'payment_terms' => $validated['payment_terms'] ?? null,
                    'scope_service' => $validated['scope_service'] ?? null,
                    'remarks' => $validated['remarks'] ?? null,
                ]);
            }

            $this->storeNewFileVersion($request, $doc);

            return $doc;
        });

        return redirect()->route('admin.documents.show', $created->document_id)->with('success', 'Dokumen berhasil disimpan.');
    }

    public function show(Request $request, string $id)
    {
        $doc = Document::withTrashed()
            ->with([
                'vendor',
                'contractGroup',
                'departmentOwner',
                'picUser',
                'creator',
                'updater',
                'sites',
                'assets',
                'contractTerms',
                'files' => fn($q) => $q->orderByDesc('version_number'),
            ])
            ->findOrFail($id);

        $this->assertCanAccessDocument($doc);

        if ($doc->trashed() && !MenuAccess::can(Auth::user(), 'documents_archive', 'update')) {
            abort(404);
        }

        $related = collect();
        if (!empty($doc->contract_group_id)) {
            $related = Document::query()
                ->with(['vendor', 'contractTerms'])
                ->where('contract_group_id', $doc->contract_group_id)
                ->where('document_id', '!=', $doc->document_id)
                ->orderByDesc('updated_at')
                ->limit(50)
                ->get();

            if (!$this->canSeeRestricted()) {
                $related = $related->where('confidentiality_level', '!=', 'Restricted')->values();
            }
        }

        $downloadLinks = [];
        foreach ($doc->files as $f) {
            $downloadLinks[$f->file_id] = URL::temporarySignedRoute(
                'admin.documents.files.download',
                now()->addMinutes(10),
                ['document' => $doc->document_id, 'file' => $f->file_id]
            );
        }

        $locations = Location::query()->active()->orderBy('plant_site')->orderBy('name')->get();
        if ($locations->isEmpty()) {
            $locations = Location::query()->orderBy('plant_site')->orderBy('name')->get();
        }

        return view('pages.admin.documents.documents_show', compact('doc', 'related', 'downloadLinks', 'locations'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $doc = Document::query()->with(['contractTerms'])->findOrFail($id);
        $this->assertCanAccessDocument($doc);

        $validated = $request->validate([
            'document_title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:50', Rule::unique('m_igi_documents', 'document_number')->ignore($doc->document_id, 'document_id')],
            'document_type' => ['required', Rule::in(self::ALLOWED_TYPES)],
            'vendor_id' => ['required', 'integer', 'exists:m_igi_asset_vendors,id'],
            'department_owner_id' => ['nullable', 'integer', 'exists:m_igi_departments,id'],
            'pic_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', Rule::in(self::ALLOWED_STATUS)],
            'confidentiality_level' => ['required', Rule::in(self::ALLOWED_CONFIDENTIALITY)],
            'tags' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'location_ids' => ['nullable', 'array'],
            'location_ids.*' => ['integer', 'exists:m_igi_locations,id'],
            'new_locations' => ['nullable', 'string', 'max:1000'],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['integer', 'exists:m_igi_asset,id'],

            // terms
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'renewal_type' => ['nullable', 'string', 'max:20'],
            'notice_period_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'billing_cycle' => ['nullable', 'string', 'max:20'],
            'contract_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'payment_terms' => ['nullable', 'string', 'max:200'],
            'scope_service' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
        ]);

        if (($validated['confidentiality_level'] ?? 'Internal') === 'Restricted' && !$this->canSeeRestricted()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah dokumen menjadi Restricted.')->withInput();
        }

        $tags = [];
        if (!empty($validated['tags'])) {
            $tags = collect(explode(',', (string) $validated['tags']))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        DB::transaction(function () use ($doc, $validated, $tags) {
            $doc->update([
                'document_title' => $validated['document_title'],
                'document_number' => $validated['document_number'] ?? $doc->document_number,
                'document_type' => $validated['document_type'],
                'vendor_id' => (int) $validated['vendor_id'],
                'department_owner_id' => $validated['department_owner_id'] ?? null,
                'pic_user_id' => $validated['pic_user_id'] ?? null,
                'status' => $validated['status'],
                'confidentiality_level' => $validated['confidentiality_level'],
                'tags' => $tags,
                'description' => $validated['description'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            $locationIds = $validated['location_ids'] ?? [];
            if (!empty($validated['new_locations'])) {
                $newIds = collect(explode(',', (string) $validated['new_locations']))
                    ->map(fn($v) => trim($v))
                    ->filter()
                    ->unique()
                    ->map(function (string $label) {
                        $loc = Location::query()->firstOrCreate(
                            ['plant_site' => $label, 'name' => $label],
                            ['is_active' => true]
                        );
                        return (int) $loc->id;
                    })
                    ->values()
                    ->all();

                $locationIds = array_values(array_unique(array_merge($locationIds, $newIds)));
            }

            $doc->sites()->sync($locationIds);
            $doc->assets()->sync($validated['asset_ids'] ?? []);

            $hasAny = !empty($validated['start_date'])
                || !empty($validated['end_date'])
                || !empty($validated['contract_value'])
                || !empty($validated['billing_cycle'])
                || !empty($validated['renewal_type'])
                || !empty($validated['payment_terms'])
                || !empty($validated['scope_service'])
                || !empty($validated['remarks']);

            if ($hasAny) {
                ContractTerms::query()->updateOrCreate(
                    ['document_id' => $doc->document_id],
                    [
                        'start_date' => $validated['start_date'] ?? null,
                        'end_date' => $validated['end_date'] ?? null,
                        'renewal_type' => $validated['renewal_type'] ?? null,
                        'notice_period_days' => $validated['notice_period_days'] ?? null,
                        'billing_cycle' => $validated['billing_cycle'] ?? null,
                        'contract_value' => $validated['contract_value'] ?? null,
                        'currency' => $validated['currency'] ?? null,
                        'payment_terms' => $validated['payment_terms'] ?? null,
                        'scope_service' => $validated['scope_service'] ?? null,
                        'remarks' => $validated['remarks'] ?? null,
                    ]
                );
            } else {
                ContractTerms::query()->where('document_id', $doc->document_id)->delete();
            }
        });

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $doc = Document::query()->findOrFail($id);
        $this->assertCanAccessDocument($doc);

        $doc->delete();

        return redirect()->route('admin.documents.index')->with('success', 'Dokumen dihapus (soft delete).');
    }

    public function restore(Request $request, string $id): RedirectResponse
    {
        $doc = Document::withTrashed()->findOrFail($id);
        $this->assertCanAccessDocument($doc);

        $doc->restore();

        return back()->with('success', 'Dokumen berhasil direstore.');
    }

    public function uploadFile(Request $request, string $id): RedirectResponse
    {
        $doc = Document::query()->findOrFail($id);
        $this->assertCanAccessDocument($doc);

        $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png'],
        ]);

        DB::transaction(function () use ($request, $doc) {
            $this->storeNewFileVersion($request, $doc);
        });

        return back()->with('success', 'File versi baru berhasil diupload.');
    }

    public function downloadFile(Request $request, string $documentId, string $fileId): StreamedResponse
    {
        $doc = Document::query()->findOrFail($documentId);
        $this->assertCanAccessDocument($doc);

        $file = DocumentFile::query()
            ->where('file_id', $fileId)
            ->where('document_id', $doc->document_id)
            ->firstOrFail();

        if (!Storage::exists($file->storage_path)) {
            abort(404);
        }

        return Storage::download($file->storage_path, $file->file_name);
    }

    private function generateDocumentNumber(): string
    {
        $prefix = 'DOC-' . now()->format('Ym') . '-';
        $tries = 0;

        while ($tries < 20) {
            $tries++;
            $seq = (int) (Document::query()->where('document_number', 'like', $prefix . '%')->count()) + random_int(1, 50);
            $candidate = $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

            $exists = Document::query()->where('document_number', $candidate)->exists();
            if (!$exists) {
                return $candidate;
            }
        }

        return $prefix . strtoupper(substr(md5((string) microtime(true)), 0, 8));
    }

    private function storeNewFileVersion(Request $request, Document $doc): void
    {
        $uploaded = $request->file('file');
        if (!$uploaded) {
            return;
        }

        $tmpPath = $uploaded->getRealPath();
        $checksum = hash_file('sha256', $tmpPath);

        $dup = DocumentFile::query()->where('checksum', $checksum)->where('file_size', (int) $uploaded->getSize())->exists();
        if ($dup) {
            abort(422, 'File duplikat terdeteksi (checksum sama).');
        }

        // Lock existing versions to avoid race
        $maxVersion = (int) DocumentFile::query()
            ->where('document_id', $doc->document_id)
            ->lockForUpdate()
            ->max('version_number');

        $newVersion = $maxVersion + 1;

        DocumentFile::query()
            ->where('document_id', $doc->document_id)
            ->where('is_latest', true)
            ->update(['is_latest' => false]);

        $ext = strtolower((string) $uploaded->getClientOriginalExtension());
        $safeExt = preg_replace('/[^a-z0-9]+/', '', $ext);
        $filename = 'v' . $newVersion . ($safeExt ? ('.' . $safeExt) : '');

        $path = 'documents_archive/vendor_' . (int) $doc->vendor_id . '/' . $doc->document_id . '/' . $filename;

        Storage::putFileAs(dirname($path), $uploaded, basename($path));

        DocumentFile::query()->create([
            'document_id' => $doc->document_id,
            'version_number' => $newVersion,
            'file_name' => $uploaded->getClientOriginalName(),
            'file_type' => (string) $uploaded->getClientMimeType(),
            'file_size' => (int) $uploaded->getSize(),
            'storage_path' => $path,
            'checksum' => $checksum,
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
            'is_latest' => true,
        ]);

        $doc->update(['updated_by' => Auth::id()]);
    }
}
