<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'contact',
        'gender',
        'dob',
        'address',
        'specialization_id',
        'status',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
