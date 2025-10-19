<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusColor extends Model
{
    use HasFactory;
    protected $table = "status_colors";
    protected $fillable = [
        'title',
        'class',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_color_id');
    }
}
