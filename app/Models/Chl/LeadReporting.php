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

    protected $fillable = [
        'event_id',
        'venue_id',
        'status',
        'reporting_date',
    ];

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
}
