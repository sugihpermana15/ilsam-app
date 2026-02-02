<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DailyTaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DailyTaskStoreRequest;
use App\Http\Requests\DailyTaskUpdateRequest;
use App\Models\DailyTask;
use App\Models\DailyTaskAttachment;
use App\Models\DailyTaskChecklistItem;
use App\Models\DailyTaskPriority;
use App\Models\DailyTaskStatusMaster;
use App\Models\DailyTaskType;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DailyTaskController extends Controller
{
    private function isAdminUser(?\App\Models\User $user): bool
    {
        return in_array((string) ($user?->role?->role_name ?? ''), ['Super Admin', 'Admin'], true);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', DailyTask::class);

        $isAdmin = $this->isAdminUser($user);

        $currentEmployee = null;
        if ($user?->employee_id) {
            $currentEmployee = Employee::query()->select(['id', 'name', 'no_id'])->find($user->employee_id);
        }

        $assignees = Employee::query()
            ->select(['id', 'name', 'no_id'])
            ->orderBy('name')
            ->limit(500)
            ->get();

        $taskTypes = DailyTaskType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (DailyTaskType $t) => ['value' => (int) $t->id, 'label' => $t->name])
            ->values()
            ->all();

        $taskPriorities = DailyTaskPriority::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (DailyTaskPriority $p) => ['value' => (int) $p->id, 'label' => $p->name])
            ->values()
            ->all();

        $taskStatuses = DailyTaskStatusMaster::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (DailyTaskStatusMaster $s) => ['value' => (int) $s->id, 'label' => $s->name])
            ->values()
            ->all();

        return view('pages.admin.daily_tasks.index', [
            'assignees' => $assignees,
            'isAdmin' => $isAdmin,
            'currentEmployee' => $currentEmployee,
            'taskTypes' => $taskTypes,
            'taskStatuses' => $taskStatuses,
            'taskPriorities' => $taskPriorities,
        ]);
    }

    public function datatable(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', DailyTask::class);

        $isAdmin = $this->isAdminUser($user);

        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $tab = trim((string) $request->input('f_tab', 'all'));
        $filterAssignedTo = (int) $request->input('f_assigned_to', 0);
        $filterStatus = (int) $request->input('f_status', 0);
        $filterPriority = (int) $request->input('f_priority', 0);
        $filterDueStart = trim((string) $request->input('f_due_start', ''));
        $filterDueEnd = trim((string) $request->input('f_due_end', ''));
        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $baseQuery = DailyTask::query()->visibleTo($user);

        $typeLabels = DailyTaskType::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();
        $priorityLabels = DailyTaskPriority::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();
        $statusLabels = DailyTaskStatusMaster::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();

        $myEmployeeId = (int) ($user->employee_id ?? 0);

        $query = (clone $baseQuery)
            ->when($tab === 'my', function ($q) use ($user, $myEmployeeId) {
                return $myEmployeeId > 0
                    ? $q->where('assigned_employee_id', $myEmployeeId)
                    : $q->where('assigned_to', $user->id);
            })
            ->when($tab === 'created', fn($q) => $q->where('created_by', $user->id))
            ->when($tab === 'overdue', function ($q) {
                $today = now()->toDateString();
                $q->whereNotNull('due_end')
                    ->where('due_end', '<', $today)
                    ->whereNotIn('status', [DailyTaskStatus::Done->value, DailyTaskStatus::Canceled->value]);
            })
            ->when($tab === 'completed', fn($q) => $q->where('status', DailyTaskStatus::Done->value))
            ->when($filterAssignedTo > 0, fn($q) => $q->where('assigned_employee_id', $filterAssignedTo))
            ->when($filterStatus > 0, fn($q) => $q->where('status', $filterStatus))
            ->when($filterPriority > 0, fn($q) => $q->where('priority', $filterPriority))
            ->when($filterDueStart !== '', fn($q) => $q->whereDate('due_end', '>=', $filterDueStart))
            ->when($filterDueEnd !== '', fn($q) => $q->whereDate('due_end', '<=', $filterDueEnd));

        $recordsTotal = (clone $baseQuery)->count();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
                $q->where('title', 'like', $like)
                    ->orWhere('description_preview', 'like', $like);
            });
        }

        $recordsFiltered = (clone $query)->count();

        $orderColumnIndex = (int) data_get($request->all(), 'order.0.column', -1);
        $orderDir = strtolower((string) data_get($request->all(), 'order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderDataKey = (string) data_get($request->all(), "columns.$orderColumnIndex.data", '');

        $sortable = [
            'id' => 'id',
            'task_type' => 'task_type',
            'title' => 'title',
            'due_start' => 'due_start',
            'due_end' => 'due_end',
            'status' => 'status',
            'priority' => 'priority',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];

        if (!empty($orderDataKey) && isset($sortable[$orderDataKey])) {
            $query->orderBy($sortable[$orderDataKey], $orderDir);
        } else {
            $query->orderByDesc('id');
        }

        $rows = $query
            ->with([
                'assignedEmployee:id,name,no_id',
                'assignedUser:id,name',
                'creator:id,name',
            ])
            ->withCount([
                'attachments',
                'checklists',
                'checklists as checklists_done_count' => fn($q) => $q->where('is_done', true),
            ])
            ->skip($start)
            ->take($length)
            ->get([
                'id',
                'task_type',
                'title',
                'description_preview',
                'due_start',
                'due_end',
                'status',
                'priority',
                'assigned_to',
                'assigned_employee_id',
                'created_by',
                'created_at',
                'updated_at',
                'completed_at',
                'canceled_at',
            ])
            ->map(function (DailyTask $task) use ($typeLabels, $priorityLabels, $statusLabels) {
                $dueStart = $task->due_start ? Carbon::parse($task->due_start)->format('Y-m-d') : null;
                $dueEnd = $task->due_end ? Carbon::parse($task->due_end)->format('Y-m-d') : null;

                $today = Carbon::today();
                $dueEndDate = $task->due_end ? Carbon::parse($task->due_end)->startOfDay() : null;
                $isClosed = in_array($task->status?->value, [DailyTaskStatus::Done->value, DailyTaskStatus::Canceled->value], true);
                $isOverdue = $dueEndDate !== null && $dueEndDate->lt($today) && !$isClosed;
                $overdueDays = $isOverdue ? (int) $dueEndDate->diffInDays($today) : 0;

                $sla = '-';
                if ($task->status === DailyTaskStatus::Canceled) {
                    $sla = 'Canceled';
                } elseif ($task->status === DailyTaskStatus::Done) {
                    if ($dueEndDate !== null && $task->completed_at !== null) {
                        $completedDate = Carbon::parse($task->completed_at)->startOfDay();
                        $sla = $completedDate->lte($dueEndDate) ? 'On Time' : 'Late';
                    } else {
                        $sla = 'Done';
                    }
                } elseif ($dueEndDate !== null) {
                    if ($isOverdue) {
                        $sla = $overdueDays > 0 ? ('Overdue ' . $overdueDays . 'd') : 'Overdue';
                    } elseif ($dueEndDate->equalTo($today)) {
                        $sla = 'Due Today';
                    } elseif ($dueEndDate->lte($today->copy()->addDays(2))) {
                        $sla = 'Due Soon';
                    } else {
                        $sla = 'On Track';
                    }
                }

                return [
                    'id' => $task->id,
                    'task_type' => $typeLabels[(int) $task->task_type] ?? '-',
                    'title' => $task->title,
                    'description_preview' => $task->description_preview,
                    'due_start' => $dueStart,
                    'due_end' => $dueEnd,
                    'sla' => $sla,
                    'is_overdue' => $isOverdue,
                    'overdue_days' => $overdueDays,
                    'status' => $statusLabels[(int) ($task->status?->value ?? 0)] ?? ($task->status?->label() ?? '-'),
                    'priority' => $priorityLabels[(int) $task->priority] ?? '-',
                    'assigned_to_name' => $task->assignedEmployee?->name ?? $task->assignedUser?->name,
                    'created_by_name' => $task->creator?->name,
                    'attachments_count' => (int) ($task->attachments_count ?? 0),
                    'checklists_count' => (int) ($task->checklists_count ?? 0),
                    'checklists_done_count' => (int) ($task->checklists_done_count ?? 0),
                    'updated_at' => $task->updated_at?->format('Y-m-d H:i'),
                ];
            })
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function json(DailyTask $task, Request $request)
    {
        $user = $request->user();
        $this->authorize('view', $task);

        $isAdmin = $this->isAdminUser($user);

        $task->load([
            'assignedEmployee:id,name,no_id',
            'assignedUser:id,name',
            'creator:id,name',
            'attachments.uploader:id,name',
            'checklists',
        ]);

        $typeLabels = DailyTaskType::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();
        $priorityLabels = DailyTaskPriority::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();
        $statusLabels = DailyTaskStatusMaster::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();

        $dueStart = $task->due_start ? Carbon::parse($task->due_start)->format('Y-m-d') : null;
        $dueEnd = $task->due_end ? Carbon::parse($task->due_end)->format('Y-m-d') : null;

        $today = Carbon::today();
        $dueEndDate = $task->due_end ? Carbon::parse($task->due_end)->startOfDay() : null;
        $isClosed = in_array($task->status?->value, [DailyTaskStatus::Done->value, DailyTaskStatus::Canceled->value], true);
        $isOverdue = $dueEndDate !== null && $dueEndDate->lt($today) && !$isClosed;
        $overdueDays = $isOverdue ? (int) $dueEndDate->diffInDays($today) : 0;

        $sla = '-';
        if ($task->status === DailyTaskStatus::Canceled) {
            $sla = 'Canceled';
        } elseif ($task->status === DailyTaskStatus::Done) {
            if ($dueEndDate !== null && $task->completed_at !== null) {
                $completedDate = Carbon::parse($task->completed_at)->startOfDay();
                $sla = $completedDate->lte($dueEndDate) ? 'On Time' : 'Late';
            } else {
                $sla = 'Done';
            }
        } elseif ($dueEndDate !== null) {
            if ($isOverdue) {
                $sla = $overdueDays > 0 ? ('Overdue ' . $overdueDays . 'd') : 'Overdue';
            } elseif ($dueEndDate->equalTo($today)) {
                $sla = 'Due Today';
            } elseif ($dueEndDate->lte($today->copy()->addDays(2))) {
                $sla = 'Due Soon';
            } else {
                $sla = 'On Track';
            }
        }

        $allowedStatusValues = DailyTaskStatus::allowedNextValues($task->status, $isAdmin);
        $currentStatusValue = (int) ($task->status?->value ?? 0);

        $allowedStatuses = DailyTaskStatusMaster::query()
            ->where(function ($q) use ($allowedStatusValues, $currentStatusValue) {
                $q->where(function ($qq) use ($allowedStatusValues) {
                    $qq->whereIn('id', $allowedStatusValues)->where('is_active', true);
                });

                if ($currentStatusValue > 0) {
                    $q->orWhere('id', $currentStatusValue);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (DailyTaskStatusMaster $s) => ['value' => (int) $s->id, 'label' => $s->name])
            ->values()
            ->all();

        return response()->json([
            'id' => $task->id,
            'is_admin' => $isAdmin,
            'task_type' => ['value' => (int) $task->task_type, 'label' => $typeLabels[(int) $task->task_type] ?? '-'],
            'title' => $task->title,
            'description' => $task->description,
            'due_start' => $dueStart,
            'due_end' => $dueEnd,
            'sla' => $sla,
            'is_overdue' => $isOverdue,
            'overdue_days' => $overdueDays,
            'status' => ['value' => (int) ($task->status?->value ?? 0), 'label' => $statusLabels[(int) ($task->status?->value ?? 0)] ?? ($task->status?->label() ?? '-')],
            'allowed_statuses' => $allowedStatuses,
            'priority' => ['value' => (int) $task->priority, 'label' => $priorityLabels[(int) $task->priority] ?? '-'],
            'assigned_to' => [
                'id' => $task->assigned_employee_id ?: $task->assigned_to,
                'name' => $task->assignedEmployee?->name ?? $task->assignedUser?->name,
            ],
            'assigned_employee_id' => $task->assigned_employee_id,
            'created_by' => ['id' => $task->created_by, 'name' => $task->creator?->name],
            'completed_at' => $task->completed_at?->format('Y-m-d H:i'),
            'canceled_at' => $task->canceled_at?->format('Y-m-d H:i'),
            'attachments' => $task->attachments->map(fn(DailyTaskAttachment $a) => [
                'id' => $a->id,
                'file_name' => $a->file_name,
                'file_type' => $a->file_type,
                'file_size' => $a->file_size,
                'url' => $a->url(),
                'uploaded_by' => $a->uploader?->name,
                'created_at' => $a->created_at?->format('Y-m-d H:i'),
            ])->values(),
            'checklists' => $task->checklists->map(fn(DailyTaskChecklistItem $i) => [
                'id' => $i->id,
                'item_text' => $i->item_text,
                'is_done' => (bool) $i->is_done,
                'created_at' => $i->created_at?->format('Y-m-d H:i'),
            ])->values(),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', DailyTask::class);

        $period = strtolower(trim((string) $request->query('period', 'daily')));
        $period = in_array($period, ['daily', 'weekly', 'monthly', 'yearly'], true) ? $period : 'daily';

        $today = Carbon::today();

        $start = null;
        $end = null;
        $periodLabel = '';

        if ($period === 'daily') {
            $date = trim((string) $request->query('date', ''));
            $d = $date !== '' ? Carbon::parse($date) : $today;
            $start = $d->copy()->startOfDay();
            $end = $d->copy()->endOfDay();
            $periodLabel = 'Harian: ' . $d->format('Y-m-d');
        } elseif ($period === 'weekly') {
            $weekDate = trim((string) $request->query('week_date', ''));
            $d = $weekDate !== '' ? Carbon::parse($weekDate) : $today;
            $start = $d->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $end = $d->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
            $periodLabel = 'Mingguan: ' . $start->format('Y-m-d') . ' s/d ' . $end->format('Y-m-d');
        } elseif ($period === 'monthly') {
            $month = trim((string) $request->query('month', ''));
            $d = $month !== '' ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : $today->copy()->startOfMonth();
            $start = $d->copy()->startOfMonth()->startOfDay();
            $end = $d->copy()->endOfMonth()->endOfDay();
            $periodLabel = 'Bulanan: ' . $d->format('Y-m');
        } else { // yearly
            $year = (int) $request->query('year', (int) $today->year);
            $year = $year > 0 ? $year : (int) $today->year;
            $d = Carbon::create($year, 1, 1, 0, 0, 0);
            $start = $d->copy()->startOfYear()->startOfDay();
            $end = $d->copy()->endOfYear()->endOfDay();
            $periodLabel = 'Tahunan: ' . $d->format('Y');
        }

        $filterAssignedTo = (int) $request->query('assigned_to', 0);
        $filterStatus = (int) $request->query('status', 0);
        $filterPriority = (int) $request->query('priority', 0);
        $filterDueStart = trim((string) $request->query('due_start', ''));
        $filterDueEnd = trim((string) $request->query('due_end', ''));
        $tab = trim((string) $request->query('tab', 'all'));

        $baseQuery = DailyTask::query()->visibleTo($user);

        $typeLabels = DailyTaskType::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();
        $priorityLabels = DailyTaskPriority::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();
        $statusLabels = DailyTaskStatusMaster::query()->pluck('name', 'id')->mapWithKeys(fn ($v, $k) => [(int) $k => (string) $v])->all();

        $myEmployeeId = (int) ($user->employee_id ?? 0);

        $query = (clone $baseQuery)
            ->when($tab === 'my', function ($q) use ($user, $myEmployeeId) {
                return $myEmployeeId > 0
                    ? $q->where('assigned_employee_id', $myEmployeeId)
                    : $q->where('assigned_to', $user->id);
            })
            ->when($tab === 'created', fn($q) => $q->where('created_by', $user->id))
            ->when($tab === 'overdue', function ($q) {
                $todayStr = now()->toDateString();
                $q->whereNotNull('due_end')
                    ->where('due_end', '<', $todayStr)
                    ->whereNotIn('status', [DailyTaskStatus::Done->value, DailyTaskStatus::Canceled->value]);
            })
            ->when($tab === 'completed', fn($q) => $q->where('status', DailyTaskStatus::Done->value))
            ->whereBetween('created_at', [$start, $end])
            ->when($filterAssignedTo > 0, fn($q) => $q->where('assigned_employee_id', $filterAssignedTo))
            ->when($filterStatus > 0, fn($q) => $q->where('status', $filterStatus))
            ->when($filterPriority > 0, fn($q) => $q->where('priority', $filterPriority))
            ->when($filterDueStart !== '', fn($q) => $q->whereDate('due_end', '>=', $filterDueStart))
            ->when($filterDueEnd !== '', fn($q) => $q->whereDate('due_end', '<=', $filterDueEnd))
            ->with([
                'assignedEmployee:id,name,no_id',
                'assignedUser:id,name',
                'creator:id,name',
            ])
            ->orderByDesc('id');

        $tasks = $query->get([
            'id',
            'task_type',
            'title',
            'due_start',
            'due_end',
            'status',
            'priority',
            'assigned_to',
            'assigned_employee_id',
            'created_by',
            'created_at',
            'updated_at',
        ])->map(function (DailyTask $task) use ($typeLabels, $priorityLabels, $statusLabels) {
            return [
                'id' => $task->id,
                'task_type' => $typeLabels[(int) $task->task_type] ?? '-',
                'title' => (string) $task->title,
                'due_start' => $task->due_start ? Carbon::parse($task->due_start)->format('Y-m-d') : '-',
                'due_end' => $task->due_end ? Carbon::parse($task->due_end)->format('Y-m-d') : '-',
                'status' => $statusLabels[(int) ($task->status?->value ?? 0)] ?? ($task->status?->label() ?? '-'),
                'priority' => $priorityLabels[(int) $task->priority] ?? '-',
                'assigned_to' => $task->assignedEmployee?->name ?? $task->assignedUser?->name ?? '-',
                'created_by' => $task->creator?->name ?? '-',
                'created_at' => $task->created_at?->format('Y-m-d H:i') ?? '-',
                'updated_at' => $task->updated_at?->format('Y-m-d H:i') ?? '-',
            ];
        })->values();

        $html = view('pages.admin.daily_tasks.export_pdf', [
            'periodLabel' => $periodLabel,
            'generatedAt' => now()->format('Y-m-d H:i'),
            'tasks' => $tasks,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        // Allow local file access for assets under public/ via file:// URLs.
        $options->set('chroot', public_path());
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $safePeriod = preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower($period));
        $filename = 'daily_tasks_' . $safePeriod . '_' . now()->format('Ymd_His') . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function store(DailyTaskStoreRequest $request)
    {
        $user = $request->user();
        $this->authorize('create', DailyTask::class);

        $data = $request->validated();

        $isAdmin = $this->isAdminUser($user);

        if (!$isAdmin) {
            if ($user?->employee_id) {
                $data['assigned_employee_id'] = $user->employee_id;
                $data['assigned_to'] = null;
            } else {
                // Backward-compatible fallback if user isn't linked to employee.
                $data['assigned_employee_id'] = null;
                $data['assigned_to'] = $user->id;
            }
        }

        $task = new DailyTask($data);
        $task->created_by = $user->id;
        $task->updated_by = $user->id;

        if ($task->status === DailyTaskStatus::Done && $task->completed_at === null) {
            $task->completed_at = now();
        }

        if ($task->status === DailyTaskStatus::Canceled && $task->canceled_at === null) {
            $task->canceled_at = now();
        }

        $task->syncDescriptionPreview();
        $task->save();

        $lines = (string) ($data['checklist_lines'] ?? '');
        $lines = preg_split('/\r\n|\r|\n/', $lines) ?: [];
        $lines = array_values(array_filter(array_map(fn($l) => trim((string) $l), $lines)));

        foreach (array_slice($lines, 0, 50) as $line) {
            $task->checklists()->create([
                'item_text' => Str::limit($line, 255, ''),
                'is_done' => false,
                'created_by' => $user->id,
            ]);
        }

        return redirect()->route('admin.daily_tasks.index')->with('success', 'Daily Task created.');
    }

    public function update(DailyTask $task, DailyTaskUpdateRequest $request)
    {
        $user = $request->user();
        $this->authorize('update', $task);

        $isAdmin = $this->isAdminUser($user);
        $data = $request->validated();

        $oldStatus = $task->status;
        $hasStatus = array_key_exists('status', $data);

        $task->fill($data);

        if ($hasStatus && $task->status instanceof DailyTaskStatus && $oldStatus instanceof DailyTaskStatus && $task->status !== $oldStatus) {
            if ($task->status === DailyTaskStatus::Done) {
                if ($task->completed_at === null) {
                    $task->completed_at = now();
                }
                $task->canceled_at = null;
            } elseif ($task->status === DailyTaskStatus::Canceled) {
                if ($task->canceled_at === null) {
                    $task->canceled_at = now();
                }
                $task->completed_at = null;
            } else {
                // Re-open behavior (admin only, enforced by request rules).
                if ($isAdmin && $oldStatus === DailyTaskStatus::Done) {
                    $task->completed_at = null;
                }
                if ($isAdmin && $oldStatus === DailyTaskStatus::Canceled) {
                    $task->canceled_at = null;
                }
            }
        }
        $task->updated_by = $user->id;
        $task->syncDescriptionPreview();
        $task->save();

        return response()->json(['ok' => true]);
    }

    public function destroy(DailyTask $task, Request $request)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(['ok' => true]);
    }

    public function uploadAttachment(DailyTask $task, Request $request)
    {
        $this->authorize('manageAttachments', $task);

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $file = $request->file('file');
        $disk = 'public';
        $path = $file->store('daily_tasks/' . $task->id . '/attachments', $disk);

        $attachment = $task->attachments()->create([
            'disk' => $disk,
            'storage_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()?->id,
        ]);

        return response()->json([
            'ok' => true,
            'attachment' => [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'url' => $attachment->url(),
            ],
        ]);
    }

    public function deleteAttachment(DailyTaskAttachment $attachment, Request $request)
    {
        $task = $attachment->task;
        $this->authorize('manageAttachments', $task);

        $disk = $attachment->disk ?: 'public';
        if ($attachment->storage_path) {
            Storage::disk($disk)->delete($attachment->storage_path);
        }

        $attachment->delete();

        return response()->json(['ok' => true]);
    }

    public function addChecklist(DailyTask $task, Request $request)
    {
        $this->authorize('manageChecklist', $task);

        $data = $request->validate([
            'item_text' => ['required', 'string', 'max:255'],
        ]);

        $item = $task->checklists()->create([
            'item_text' => $data['item_text'],
            'is_done' => false,
            'created_by' => $request->user()?->id,
        ]);

        return response()->json([
            'ok' => true,
            'item' => [
                'id' => $item->id,
                'item_text' => $item->item_text,
                'is_done' => (bool) $item->is_done,
            ],
        ]);
    }

    public function toggleChecklist(DailyTaskChecklistItem $item, Request $request)
    {
        $task = $item->task;
        $this->authorize('manageChecklist', $task);

        $data = $request->validate([
            'is_done' => ['required', 'boolean'],
        ]);

        $item->is_done = (bool) $data['is_done'];
        $item->save();

        return response()->json(['ok' => true]);
    }

    public function deleteChecklist(DailyTaskChecklistItem $item, Request $request)
    {
        $task = $item->task;
        $this->authorize('manageChecklist', $task);

        $item->delete();

        return response()->json(['ok' => true]);
    }
}
