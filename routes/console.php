<?php

use App\Jobs\SendAppointmentReminderEmail;
use App\Mail\AppointmentEmail;
use App\Models\Appointment;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $appointments = Appointment::whereDate('date', now()->toDateString())->get();
    foreach ($appointments as $appointment) {
        SendAppointmentReminderEmail::dispatch($appointment);
    }
})->dailyAt('06:00');