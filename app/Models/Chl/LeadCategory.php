<?php

namespace App\Models;

use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCategory extends Model
{
    use HasFactory;
    protected $table = "lead_categories";
    protected $fillable = [
        'title',
        'event_id',
        'venue_id',
    ];


    public function leadTasks()
    {
        return $this->hasMany(LeadTask::class, 'category_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id', 'id');
    }

    // LeadCategory.php
    public function leadComments()
    {
        return $this->hasMany(LeadComment::class, 'category_id');
    }
}
