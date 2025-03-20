<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EnvironmentSensor;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function getCurrentDateTimeHtml()
    {
        $now = Carbon::now(); // Get the current time as a Carbon instance

        return [
            'currentDate' => $now->toDateString(), // Current date (YYYY-MM-DD)
            'currentDay' => $now->dayName,         // Current day name (e.g., Monday)
            'currentDayDay' => $now->day,          // Current day of the month (1-31)
            'currentMonth' => $now->format('F'),   // Current month name (e.g., January)
            'currentHour' => $now->format('g'),     // Current hour (1-12)
            'currentMinute' => $now->format('i'),   // Current minute (00-59)
            'currentTime' => $now->format('A'), // Current time (Hour:Minute AM/PM)
        ];
    }
}
