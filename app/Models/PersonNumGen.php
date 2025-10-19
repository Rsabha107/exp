<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonNumGen extends Model
{
    use HasFactory;
    protected $table = 'person_number_gen';
    protected $guarded = [];

}
