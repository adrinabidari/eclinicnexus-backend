<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        $medicine = Medicine::orderBy('created_at', 'desc')
            ->get();
        return $medicine;
    }

    public function store(Request $request)
    {
        return Medicine::create([
            'name' => $request->name,
        ]);
    }

    public function update($id, Request $request)
    {
        return Medicine::where('id', $id)->update([
            'name' => $request->name,
        ]);
    }


    public function destroy($id)
    {
        $medicine = Medicine::find($id);

        if (!$medicine) {
            return response()->json([
                'message' => 'Medicine not found',
            ], 404);
        }

        $medicine->delete();
    }
}
