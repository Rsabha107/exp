<?php

namespace App\Models\Setting;

use App\Models\GlobalStatus;
use App\Models\LeadComment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;    

class Event extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = "events";
    protected $guarded = [];


    public function active_status()
    {
        return $this->belongsTo(GlobalStatus::class, 'active_flag');
    }

    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'venue_event', 'venue_id', 'event_id');
    }
    public function leadComment()
    {
        return $this->hasOne(LeadComment::class, 'event_id');
    }
}
