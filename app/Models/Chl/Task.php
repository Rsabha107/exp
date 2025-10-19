<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = "tasks";

    // Make sure all relevant fields are fillable
    protected $fillable = [
        'title',
        'category_id',
        'completed',
        'event',    
        'venue',
        'status_id',
        'status_color_id',
        'due_date',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    /**
     * Task belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Task belongs to a todo status
     */
    public function status()
    {
        return $this->belongsTo(TodoStatus::class, 'status_id');
    }

    /**
     * Task belongs to a status color
     */
    public function statusColor()
    {
        return $this->belongsTo(StatusColor::class, 'status_color_id');
    }
}
