<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'assigned_to',
        'due_date',
        'total_tasks',
        'completed_tasks',
        'pending_tasks',
        'in_progress_tasks',
        'last_generated_at',
    ];

    protected $casts = [
        'last_generated_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);

    }
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

}
