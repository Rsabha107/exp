<?php

namespace App\Models\Setting;

use App\Models\GlobalStatus;
use App\Models\LeadComment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PermissionVenueEvent extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = "permission_venue_events";
    protected $guarded = [];



    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'user_permissions', 'venue_id', 'event_id');
    }
}
