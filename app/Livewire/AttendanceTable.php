<?php

namespace App\Livewire;

use App\Helpers\FlashMessageHelper;
use App\Models\EmployeeDetails;
use App\Models\HolidayCalendar;
use App\Models\LeaveRequest;
use App\Models\SwipeRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Torann\GeoIP\Facades\GeoIP;

class AttendanceTable extends Component
{
    public $distinctDates;

    public $todaysDate;
    public $viewDetailsInswiperecord,$standardWorkingMinutes;

    public $viewDetailsOutswiperecord;
    public $moveCaretLeftSession1 = false;

    public $viewDetailsswiperecord;
    public $moveCaretLeftSession2 = false;
    public $holiday;

    public $swiperecord;

    public $date;

    public $showAlertDialog = false;

    public  string $year;

    public  string $start;

    public $employeeDetails;
    public string $end;

    public $totalshortfallHoursWorked;

    public $totalexcessHoursWorked;

    public $totalexcessMinutesWorked;
    public $totalshortfallMinutesWorked;
    public $totalHoursWorked;

    public $totalWorkedMinutes;
    public $totalMinutesWorked;
    public $startDate;

    public $checkdate=0;
    public $employeeIdForTable;
    public $endDate;
    public $legend=true;
    public $showSR = false;

    public $country='-';

    public $dateforpopup;
    public $fromDate;

    public $toDate;
    public $city='-';

    public $employee_shift_type;
    public $öpenattendanceperiod = false;
    public $postal_code='-';

    public $employeeShiftDetails;
    protected $listeners = [
        'update',
    ];

    protected $rules = [
        'fromDate' => 'required|date',
        'toDate' => 'required|date|after_or_equal:fromDate', // Ensuring toDate is after or equal to fromDate
    ];
    public function mount()
    {
        // First initialize
        $this->year = Carbon::now()->format('Y');
        $this->start = Carbon::now()->year($this->year)->firstOfMonth()->format('Y-m-d');

        $this->end = Carbon::now()->year($this->year)->lastOfMonth()->format('Y-m-d');
        $this->fromDate=$this->start;
        $this->toDate=$this->end;
        $this->employeeDetails=EmployeeDetails::where('emp_id',auth()->guard('emp')->user()->emp_id)->first();
        $ip = request()->ip();
        $location = GeoIP::getLocation($ip);
        $lat = $location['lat'];
        $lon = $location['lon'];
        $this->country = $location['country'];
        $this->city = $location['city'];
        $this->postal_code = $location['postal_code'];
        $this->employeeShiftDetails = DB::table('employee_details')
    ->join('company_shifts', function($join) {
        $join->on(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(employee_details.company_id, '$[0]'))"), '=', 'company_shifts.company_id')
             ->on('employee_details.shift_type', '=', 'company_shifts.shift_name');
    })
    ->where('employee_details.emp_id', auth()->guard('emp')->user()->emp_id)
    ->select('company_shifts.shift_start_time','company_shifts.shift_end_time','company_shifts.shift_name', 'employee_details.*')
    ->first();
    }
  
    public function updatefromDate()
    {
        $this->fromDate=$this->fromDate;
        $this->checkUpdateDate();
    }
    public function öpenattendanceperiodModal()
    {

        $this->öpenattendanceperiod = true;
    }
    public function closeattendanceperiodModal()
    {
        $this->öpenattendanceperiod = false;
    }
    public function updatetoDate()
    {
        $this->toDate=$this->toDate;
        $this->checkUpdateDate();
    }
    public function checkUpdateDate()
    {
        if($this->fromDate>$this->toDate)
        {
            $this->checkdate=1;
            FlashMessageHelper::flashError('Start Date should be lesser than End Date');
           
        }
    }
    private function isEmployeeLeaveOnDate($date, $employeeId)
    {
        try {
            $employeeId = auth()->guard('emp')->user()->emp_id;


            return LeaveRequest::where('emp_id', $employeeId)
            ->where('leave_applications.leave_status', 2)
            ->where(function ($query) use ($date) {
                $query->whereDate('from_date', '<=', $date)
                    ->whereDate('to_date', '>=', $date);
            })
            ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status') // Join with status_types
            ->exists();
        } catch (\Exception $e) {
            Log::error('Error in isEmployeeLeaveOnDate method: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while checking employee leave. Please try again later.');
            return false; // Return false to handle the error gracefully
        }
    }
    private function detectEmployeeLeaveType($date, $employeeId)
    {
        try {
            $employeeId = auth()->guard('emp')->user()->emp_id;


            return LeaveRequest::where('emp_id', $employeeId)
            ->where('leave_applications.leave_status', 2)
            ->where(function ($query) use ($date) {
                $query->whereDate('from_date', '<=', $date)
                    ->whereDate('to_date', '>=', $date);
            })
            ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status') // Join with status_types
            ->value('leave_applications.leave_type');
        } catch (\Exception $e) {
            Log::error('Error in isEmployeeLeaveOnDate method: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while checking employee leave. Please try again later.');
            return false; // Return false to handle the error gracefully
        }
    }
    public function openlegend()
    {
        $this->legend=!$this->legend;
    }
    public function update($start, $end) 
    {
        $this->year = carbon::parse($start)->format('Y');
        $this->start = $start;
        $this->end = $end;
    }

    public function changeYear()
    {
        $this->start = Carbon::parse($this->start)->year($this->year)->format('Y-m-d');
        $this->end = Carbon::parse($this->end)->year($this->year)->format('Y-m-d');
    }
    public function toggleCaretDirectionForSession1()
    {
        $this->moveCaretLeftSession1 = !$this->moveCaretLeftSession1;
    }

    public function viewDetails($i)
    {
        $this->showAlertDialog = true;
        $this->dateforpopup = $i;
        $this->viewDetailsswiperecord = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)->whereDate('created_at', $this->dateforpopup)->get();
        $this->viewDetailsInswiperecord = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)->where('in_or_out', 'IN')->whereDate('created_at', $this->dateforpopup)->first();
       
        $this->viewDetailsOutswiperecord = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)->where('in_or_out', 'OUT')->whereDate('created_at', $this->dateforpopup)->first();
        
    }
    public function close()
    {
        $this->viewDetailsInswiperecord = null;
        $this->viewDetailsOutswiperecord = null;
        $this->showAlertDialog = false;
    }
    public function toggleCaretDirectionForSession2()
    {
        $this->moveCaretLeftSession2 = !$this->moveCaretLeftSession2;
    }
    public function render()
    {
        $this->todaysDate = date('Y-m-d');
        $employeeId = auth()->guard('emp')->user()->emp_id;
        $this->employeeIdForTable = auth()->guard('emp')->user()->emp_id;
        $this->swiperecord = SwipeRecord::where('emp_id', $employeeId)->where('is_regularized', 1)->get();
        $currentMonth = date('F');
        $currentYear = date('Y');
        $this->holiday = HolidayCalendar::pluck('date')
            ->toArray();

        $swipeRecords = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)->get();
        $groupedDates = $swipeRecords->groupBy(function ($record) {
            return Carbon::parse($record->created_at)->format('Y-m-d');
        });
        $this->distinctDates = $groupedDates->mapWithKeys(function ($records, $key) {
            $inRecord = $records->where('in_or_out', 'IN')->first();
            $outRecord = $records->where('in_or_out', 'OUT')->last();

            return [
                $key => [
                    'in' => "IN",
                    'first_in_time' => optional($inRecord)->swipe_time,
                    'last_out_time' => optional($outRecord)->swipe_time,
                    'out' => "OUT",
                ]
            ];
        });
        return view('livewire.attendance-table');
    }
}
