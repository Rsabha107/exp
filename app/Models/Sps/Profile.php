<?php

namespace App\Models\Sps;

use App\Models\Setting\Event;
use App\Models\Setting\Location;
use App\Models\Setting\SeqNumGen;
use App\Models\Setting\storageType;
use App\Models\Setting\Venue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class Profile extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    protected $table = 'profiles';

    public static function boot()
    {

        parent::boot();

        // static::creating(function ($profile) {
        //     do {
        //         $profile->ref_number = request()->venue->title . '-SPS-' . random_int(100000, 999999);
        //         // $profile->ref_number = request()->venue.'-SPS-' . Str::upper(Str::random(10)); // e.g. "AB12CD34XY"
        //     } while (self::where('ref_number', $profile->ref_number)->exists());
        // });

        static::creating(function ($model) {
            $venue = Venue::find(request()->venue_id);
            $location = Location::find(request()->location_id);
            $generated_number = SeqNumGen::firstOrFail();
            $last_number = $generated_number->max('last_number') + 1;
            $generated_number->update(['last_number' => $last_number]);
            $venue_short_name = $venue->short_name ?? 'VENUE';
            $location_name = $location->title ?? 'LOCATION';
            $model->ref_number = $venue_short_name . '-SPS-'. generateInitials($location_name) . '-' . str_pad($last_number, 10, '0', STR_PAD_LEFT);
        });
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function items()
    {
        return $this->hasMany(StoredItem::class, 'profile_id');
    }
    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'item_status_id');
    }

    public function profileStatus()
    {
        return $this->belongsTo(ProfileStatus::class, 'item_status_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    public function storageType()
    {
        return $this->belongsTo(storageType::class, 'storage_type_id');
    }
}
