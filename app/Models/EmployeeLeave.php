<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeLeave extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'date_from',
        'date_to',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeavesType::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @param  array<int, string|null>  $paths
     * @param  array<int, string|null>  $fileDisplayNames
     */
    public function syncDocumentsFromUploadState(array $paths, array $fileDisplayNames, string $disk = 'local'): void
    {
        DB::transaction(function () use ($paths, $fileDisplayNames, $disk): void {
            $paths = array_values(array_filter(Arr::wrap($paths), fn (mixed $path): bool => filled($path) && is_string($path)));

            $pathToDisplayName = [];
            foreach ($paths as $index => $path) {
                $pathToDisplayName[$path] = isset($fileDisplayNames[$index]) && filled($fileDisplayNames[$index])
                    ? (string) $fileDisplayNames[$index]
                    : basename($path);
            }

            $storage = Storage::disk($disk);

            foreach ($this->documents()->get() as $document) {
                if (array_key_exists($document->file_path, $pathToDisplayName)) {
                    continue;
                }

                if ($storage->exists($document->file_path)) {
                    $storage->delete($document->file_path);
                }

                $document->delete();
            }

            foreach ($pathToDisplayName as $path => $displayName) {
                $this->documents()->firstOrCreate(
                    ['file_path' => $path],
                    ['file_name' => $displayName],
                );
            }
        });
    }

    /**
     * @return HasMany<EmployeeLeaveDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeLeaveDocument::class);
    }
}
