<?php

namespace App\Models\Chl;


use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use Illuminate\Database\Eloquent\Model;

class LeadComment extends Model
{
    protected $table = 'lead_comments';

    protected $fillable = [
        'venue_id',
        'event_id',
        'category_id',
        'comment',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
