<?php

namespace App\Models;

use App\Models\Setting\Venue;
use Illuminate\Database\Eloquent\Model;

class VenueTask extends Model
{
    protected $fillable = ['venue_id', 'task_id', 'completed'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
