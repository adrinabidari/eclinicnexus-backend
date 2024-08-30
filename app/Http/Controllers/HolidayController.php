<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        return Holiday::all();
    }

    public function store(Request $request)
    {
        $user = Holiday::create([
            'date' => $request->date,
            'reason' => $request->reason === 'undefined' ? null : $request->reason,
            'user_id' => auth()->id(),
        ]);
    }
}
