<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermVenueEvent extends Model
{
    use HasFactory;

    protected $table = 'perm_venue_events';
    protected $guarded = []; // allows mass assignment

    // Many-to-many relationship with Venue
    public function venues()
    {
        return $this->belongsToMany(
            Venue::class,      // related model
            'user_permission_venue_event',      // pivot table
            'event_id',          // foreign key on pivot for this model
            'venue_id'                             // foreign key on pivot for related model
        )->withTimestamps();
    }
}
