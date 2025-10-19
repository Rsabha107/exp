<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeqNumGen extends Model
{
    use HasFactory;
    protected $table = 'seq_number_gen';
    protected $guarded = [];
}
