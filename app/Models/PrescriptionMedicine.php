<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionMedicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'dosage',
        'duration',
        'time',
        'interval',
        'hierarchy',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }
}
