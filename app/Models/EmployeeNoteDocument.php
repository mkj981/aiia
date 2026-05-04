<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployeeNoteDocument extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_note_id',
        'file_path',
        'file_name',
    ];

}
