<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use App\Models\Concerns\SyncsDocumentsFromFilamentUploadState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeNote extends Model
{
    use HasActivityLog, SoftDeletes, SyncsDocumentsFromFilamentUploadState;

    protected $fillable = [
        'employee_id',
        'employee_note_type_id',
        'note',
    ];

    public function noteType(): BelongsTo
    {
        return $this->belongsTo(EmployeeNoteType::class, 'employee_note_type_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return HasMany<EmployeeNoteDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeNoteDocument::class);
    }
}
