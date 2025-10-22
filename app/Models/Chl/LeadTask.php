<?php

namespace App\Models\Chl;



use Illuminate\Database\Eloquent\Model;

class LeadTask extends Model
{
    protected $table = "lead_tasks";
    // protected $fillable = [
    //     'title',
    //     'category_id',
    //     'status_id',
    //     'status_color_id',
    //     'completed',
    //     'event_id',
    //     'venue_id',
    //     'comment',
    //     'due_date',
    //     'reporting_id',
    // ];

    protected $guarded = [];


    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // public function status()
    // {
    //     return $this->belongsTo(TodoStatus::class, 'status_id');
    // }
    // public function statusColor()
    // {
    //     return $this->belongsTo(StatusColor::class, 'status_color_id');
    // }
    public function comments()
    {
        return $this->hasOne(LeadComment::class, 'event_id')
            ->where('venue_id', $this->venue_id);
    }
}
