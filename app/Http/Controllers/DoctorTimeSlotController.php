<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorTimeSlot;
use App\Models\Holiday;
use Illuminate\Http\Request;

class DoctorTimeSlotController extends Controller
{

    public function schedule(Request $request)
    {
        $day = $request->query('day');
        $timeSlots = DoctorTimeSlot::where('user_id', auth()->id())
            ->where('day', $day)
            ->orderBy('hierarchy')  // Sort by hierarchy
            ->get();
        return $timeSlots;
    }


    public function schedule_edit(Request $request)
    {
        $day = $request->query('day');
        $schedules = $request->input('schedules', []);

        $user_id = auth()->id();
        $delete_previous = DoctorTimeSlot::where('day', $day)
            ->where('user_id', $user_id)
            ->delete();

        foreach ($schedules as $index => $item) {
            // var_dump($item);
            // var_dump($item['start_time']);
            $doctor_time_slot = DoctorTimeSlot::create([
                'user_id' => $user_id,
                'day' => $day,
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time'],
                'hierarchy' => $index,
            ]);
        }
    }

    public function time_slots(Request $request)
    {
        $date = $request->query('date');
        $doctor_id = $request->query('doctor_id');
        $day = $request->query('day');

        $timeSlots = Holiday::where('user_id', $doctor_id)
            ->where('date', $date)
            ->first();

        if ($timeSlots) {
            return [
                'message' => 'not-available',
            ];
        } else {
            $timeSlots = DoctorTimeSlot::where('user_id', $doctor_id)
                ->where('day', $day)
                ->orderBy('hierarchy')  // Sort by hierarchy
                ->get();

            $bookedTimeSlots = Appointment::with(['timeSlot'])
                ->where('doctor_id', $doctor_id)
                ->where('date', $date)
                ->get();

            return [
                'message' => 'success',
                'data' => $timeSlots,
                'booked' => $bookedTimeSlots,
            ];
        }
    }
}
