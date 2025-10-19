<?php

namespace App\Models;

use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = "categories";
    protected $fillable = [
        'title',
        'event',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'category_id');
    }
    public function leadTasks()
    {
        return $this->hasMany(LeadTask::class, 'category_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event', 'id');
    }

    public function leadComment()
    {
        return $this->hasMany(LeadComment::class, 'category_id');
    }
}
