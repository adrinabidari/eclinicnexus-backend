<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'service_id',
        'date',
        'day',
        'time_slot_id',
        'status',
        'payment',
        'payment_method',
        'description',
        'amount',
        'additional_fee',
        'total_amount'
    ];

    public function timeSlot()
    {
        return $this->belongsTo(DoctorTimeSlot::class, 'time_slot_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
