<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniversityReference extends Model
{
    protected $table = 'university_references';

    protected $fillable = ['nom', 'acronyme'];
}
