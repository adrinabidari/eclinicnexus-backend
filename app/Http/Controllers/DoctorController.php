<?php

namespace App\Http\Controllers;

use App\Models\DoctorTimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function index()
    {
        return Doctor::with(['user', 'specialization', 'created_by'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => ['required'],
            'dob' => ['required'],
            'gender' => ['required'],
        ]);

        if ($request->hasFile('image')) {
            // Store the file in the 'images' directory within the 'public' disk
            $path = $request->file('image')->store('images', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'profile' => $path ?? null,
            'role_id' => 2, //role_id = 1 for admin, 2 for doctor, 3 for staff and 4 for patient
        ]);
        if ($user) {
            // var_dump($user->id);
            $phone = $request->phone === 'undefined' ? null : $request->phone;
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'contact' => $phone,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'address' => $request->address,
                'specialization_id' => $request->specialization,
                'status' => $request->status ? 1 : 0,
                'created_by' => auth()->id()
            ]);
        }
    }

    public function doctor_detail(Request $request)
    {
        $id = $request->query('id');

        return Doctor::where('user_id', $id)
            ->with(['user'])
            ->first();
    }

    public function edit(Request $request)
    {
        $user = User::where('id', $request->id)->update([
            'name' => $request->name,
        ]);

        $phone = $request->phone == 'null' ? null : $request->phone;


        $doctor = Doctor::where('user_id', $request->id)->update([
            'contact' => $request->phone,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'address' => $request->address,
            'specialization_id' => $request->specialization,
        ]);
    }
    public function dashboard(Request $request)
    {
        $doctor_id = $request->query('doctor_id');

        $today = date('Y-m-d');
        $nextDay = date('Y-m-d', strtotime('+1 day'));

        $totalAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor_id)
            ->count();

        $todayAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor_id)
            ->where('date', $today)
            ->count();

        $nextAppointments = DB::table('appointments')
            ->where('doctor_id', $doctor_id)
            ->where('date', $nextDay)
            ->count();

        $monthlyEarnings = DB::table('appointments')
            ->where('doctor_id', $doctor_id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->sum('total_amount');

        $previousMonthEarnings = DB::table('appointments')
            ->where('doctor_id', $doctor_id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m', strtotime('-1 month')))
            ->sum('total_amount');

        $monthlyAppointmentsData = DB::table('appointments')
            ->where('doctor_id', $doctor_id)
            ->whereYear('date', date('Y'))
            ->select(DB::raw('MONTH(date) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->get();

        // Initialize an array with all 12 months set to 0 appointments
        $monthlyAppointments = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyAppointments[] = [
                'month' => $i,
                'count' => 0,
                'month_name' => date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

        // Fill the array with actual data from the database
        foreach ($monthlyAppointmentsData as $data) {
            $monthlyAppointments[$data->month - 1]['count'] = $data->count;
        }

        return response()->json([
            'totalAppointments' => $totalAppointments,
            'todayAppointments' => $todayAppointments,
            'nextAppointments' => $nextAppointments,
            'monthlyEarnings' => $monthlyEarnings,
            'previousMonthEarnings' => $previousMonthEarnings,
            'monthlyAppointments' => $monthlyAppointments,
        ]);
    }

    public function admin_dashboard(Request $request)
    {
        $doctor_id = $request->query('doctor_id');

        $today = date('Y-m-d');
        $nextDay = date('Y-m-d', strtotime('+1 day'));

        $totalAppointments = DB::table('appointments')
            ->count();

        $todayAppointments = DB::table('appointments')
            ->where('date', $today)
            ->count();

        $nextAppointments = DB::table('appointments')
            ->where('date', $nextDay)
            ->count();

        $monthlyEarnings = DB::table('appointments')
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->sum('total_amount');

        $previousMonthEarnings = DB::table('appointments')
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m', strtotime('-1 month')))
            ->sum('total_amount');

        $monthlyAppointmentsData = DB::table('appointments')
            ->whereYear('date', date('Y'))
            ->select(DB::raw('MONTH(date) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->get();

        // Initialize an array with all 12 months set to 0 appointments
        $monthlyAppointments = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyAppointments[] = [
                'month' => $i,
                'count' => 0,
                'month_name' => date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

        // Fill the array with actual data from the database
        foreach ($monthlyAppointmentsData as $data) {
            $monthlyAppointments[$data->month - 1]['count'] = $data->count;
        }

        return response()->json([
            'totalAppointments' => $totalAppointments,
            'todayAppointments' => $todayAppointments,
            'nextAppointments' => $nextAppointments,
            'monthlyEarnings' => $monthlyEarnings,
            'previousMonthEarnings' => $previousMonthEarnings,
            'monthlyAppointments' => $monthlyAppointments,
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        $doctor = Doctor::where('user_id', $id)->first();
        if ($doctor) {
            $doctor->delete();
        }

        $user->delete();
    }

}
