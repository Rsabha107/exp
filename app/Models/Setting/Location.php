<?php

namespace App\Models\Setting;

use App\Models\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Location extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table="locations";
    protected $guarded = [];


    public function active_status()
    {
        return $this->belongsTo(GlobalStatus::class, 'active_flag');
    }
}
