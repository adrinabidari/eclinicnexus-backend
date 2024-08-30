<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorTimeSlotController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


// doctor routes
Route::get('/doctors', [DoctorController::class, 'index'])->middleware('auth:sanctum');
Route::get('/doctor-detail', [DoctorController::class, 'doctor_detail'])->middleware('auth:sanctum');
Route::post('/doctor-create', [DoctorController::class, 'store'])->middleware('auth:sanctum');
Route::post('/doctor-edit', [DoctorController::class, 'edit'])->middleware('auth:sanctum');
Route::get('/doctor-dashboard', [DoctorController::class, 'dashboard'])->middleware('auth:sanctum');
Route::delete('/delete-doctor/{id}', [DoctorController::class, 'destroy'])->middleware('auth:sanctum');

// Specialization
Route::get('/all-specialization', [SpecializationController::class, 'index'])->middleware('auth:sanctum');
Route::post('/specialization-create', [SpecializationController::class, 'store'])->middleware('auth:sanctum');
Route::put('/update-specialization/{id}', [SpecializationController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/delete-specialization/{id}', [SpecializationController::class, 'destroy'])->middleware('auth:sanctum');

//doctors schedule
Route::post('/doctor-schedule-edit', [DoctorTimeSlotController::class, 'schedule_edit'])->middleware('auth:sanctum');
Route::get('/doctor-schedule', [DoctorTimeSlotController::class, 'schedule'])->middleware('auth:sanctum');
Route::get('/doctor-time-slots', [DoctorTimeSlotController::class, 'time_slots'])->middleware('auth:sanctum');

// doctors holiday
Route::get('/holidays', [HolidayController::class, 'index'])->middleware('auth:sanctum');
Route::post('/holiday-create', [HolidayController::class, 'store'])->middleware('auth:sanctum');

// Staffs routes
Route::get('/all-staffs', [UserController::class, 'index'])->middleware('auth:sanctum');
Route::post('/staff-create', [UserController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/delete-staff/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');

// Patients 
Route::get('/all-patients', [PatientController::class, 'index'])->middleware('auth:sanctum');
Route::post('/patient-create', [PatientController::class, 'store']);
Route::get('/patient-detail', [PatientController::class, 'patient_detail'])->middleware('auth:sanctum');
Route::post('/patient-edit', [PatientController::class, 'edit'])->middleware('auth:sanctum');
Route::delete('/delete-patient/{id}', [PatientController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/patient-dashboard', [PatientController::class, 'patient_dashboard'])->middleware('auth:sanctum');

// Services
Route::get('/all-services', [ServiceController::class, 'index'])->middleware('auth:sanctum');
Route::put('/update-service/{id}', [ServiceController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/delete-service/{id}', [ServiceController::class, 'destroy'])->middleware('auth:sanctum');
Route::post('/service-create', [ServiceController::class, 'store'])->middleware('auth:sanctum');

// Appointments
Route::get('/all-appointments', [AppointmentController::class, 'index'])->middleware('auth:sanctum');
Route::post('/appointment-create', [AppointmentController::class, 'store'])->middleware('auth:sanctum');
Route::get('/appointments/{id}', [AppointmentController::class, 'appointment'])->middleware('auth:sanctum');
Route::put('/appointment-status-edit/{id}', [AppointmentController::class, 'status_edit'])->middleware('auth:sanctum');
Route::put('/appointment-payment-edit/{id}', [AppointmentController::class, 'payment_edit'])->middleware('auth:sanctum');
Route::get('/generate-appointment-pdf/{id}', [AppointmentController::class, 'generate_pdf'])->middleware('auth:sanctum');

// Medicines
Route::get('/all-medicines', [MedicineController::class, 'index'])->middleware('auth:sanctum');
Route::post('/medicine-create', [MedicineController::class, 'store'])->middleware('auth:sanctum');
Route::put('/update-medicine/{id}', [MedicineController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/delete-medicine/{id}', [MedicineController::class, 'destroy'])->middleware('auth:sanctum');

// Prescription
Route::post('/prescription-create', [PrescriptionController::class, 'store'])->middleware('auth:sanctum');
Route::get('/prescription', [PrescriptionController::class, 'prescription'])->middleware('auth:sanctum');
Route::get('/generate-prescription-pdf/{id}', [PrescriptionController::class, 'generate_pdf'])->middleware('auth:sanctum');

// Admin
Route::get('/admin-dashboard', [DoctorController::class, 'admin_dashboard'])->middleware('auth:sanctum');