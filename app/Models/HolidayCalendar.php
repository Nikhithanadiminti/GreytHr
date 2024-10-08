<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolidayCalendar extends Model
{
    use HasFactory;
    protected $table = 'holiday_calendars';
    protected $fillable=[
        'day', 'date', 'month', 'year', 'festivals'
     ];

}
