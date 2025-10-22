<?php

namespace App\Models\Chl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoStatus extends Model
{
    use HasFactory;
    protected $table = "todo_statuses";
    protected $fillable = [
        'title',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_id');
    }

    public function leadTasks()
    {
        return $this->hasMany(LeadTask::class, 'status_id');
    }
}
