<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpExperience extends Model
{
    use HasFactory;
    protected $fillable = [
        'emp_id',
        'experience'

    ];
}
