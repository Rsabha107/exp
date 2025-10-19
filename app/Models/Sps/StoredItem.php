<?php

namespace App\Models\Sps;

use App\Models\Setting\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class StoredItem extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    protected $table = 'stored_items';

    public function prohibited_item()
    {
        return $this->belongsTo(ProhibitedItem::class, 'item_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'item_status_id');
    }

}
