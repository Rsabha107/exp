<?php

namespace App\Models\Chl;

use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadReporting extends Model
{
    use HasFactory;

    protected $table = 'lead_reportings';

    protected $guarded = [];

    // protected $fillable = [
    //     'event_id',
    //     'venue_id',
    //     'status',
    //     'reporting_date',
    // ];

     public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leadComments()
    {
        return $this->hasMany(LeadComment::class, 'reporting_id');
    }

    public function leadCategories()
    {
        return $this->hasMany(LeadCategory::class, 'reporting_id');
    }

    public function leadTasks()
    {
        return $this->hasMany(LeadTask::class, 'reporting_id');
    }

        protected static function booted()
    {
        static::deleting(function ($reporting) {
            $reporting->leadComments()->delete();
            $reporting->leadCategories()->delete();
            $reporting->leadTasks()->delete();
        });
    }
}
