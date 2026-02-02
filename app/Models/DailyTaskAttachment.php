<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class DailyTaskAttachment extends Model
{
    protected $table = 'm_igi_daily_task_attachments';

    protected $fillable = [
        'daily_task_id',
        'disk',
        'storage_path',
        'file_name',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(DailyTask::class, 'daily_task_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        $disk = $this->disk ?: 'public';

        $storagePath = ltrim((string) $this->storage_path, '/');

        // For local "public" disk, build URL from the current request host+port
        // (APP_URL may omit port in local dev, causing broken links).
        if ($disk === 'public') {
            $relative = '/storage/' . $storagePath;

            try {
                if (app()->runningInConsole()) {
                    return url($relative);
                }

                $req = request();
                if ($req) {
                    return $req->getSchemeAndHttpHost() . $relative;
                }
            } catch (\Throwable $e) {
                // Fall back below.
            }

            return url($relative);
        }

        /** @var FilesystemAdapter $fs */
        $fs = Storage::disk($disk);

        return $fs->url($storagePath);
    }
}
