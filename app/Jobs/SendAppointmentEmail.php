<?php

namespace App\Jobs;

use App\Mail\AppointmentEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAppointmentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $app = $this->appointment;
        $doctorName = $app->doctor->name;
        $serviceName = $app->service->service;
        $appointmentDate = date('F j, Y', strtotime($app->date));
        $appointmentTime = date('h:i A', strtotime($app->timeSlot->start_time)) . ' - ' . date('h:i A', strtotime($app->timeSlot->end_time));
        $patientName = $app->patient->name;

        $message = "
            <div style='color: #333;'>
                <p style='font-size: 16px;'>Hi {$patientName},</p>

                <p style='font-size: 16px;'>Your appointment with <strong>{$doctorName}</strong> has been successfully booked. Please find the details of your appointment below:</p>

                <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 20px;'>
                    <h3 style='color: #2c3e50; border-bottom: 1px solid #e6e6e6; padding-bottom: 10px;'>Appointment Details</h3>
                    <p style='font-size: 16px; margin: 10px 0;'>
                        <strong>Service:</strong> {$serviceName}<br>
                        <strong>Date:</strong> {$appointmentDate}<br>
                        <strong>Time:</strong> {$appointmentTime}<br>
                        <strong>Doctor:</strong> {$doctorName}
                    </p>
                </div>

                <p style='font-size: 16px; margin-top: 20px;'>If you have any questions or need to reschedule, please feel free to contact us.</p>

                <p style='font-size: 16px;'>Thank you for choosing our services.</p>

                <p style='font-size: 16px; color: #2c3e50; margin-top: 30px;'>Best regards,<br>eClinicNexus</p>
            </div>
        ";

        $subject = "Appointment Confirmation";

        Mail::to($app->patient->email)->send(new AppointmentEmail($message, $subject));
    }
}