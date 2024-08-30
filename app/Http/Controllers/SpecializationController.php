<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialization;

class SpecializationController extends Controller
{
    public function index()
    {
        return Specialization::orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        Specialization::create([
            'name' => $request->name,
        ]);
    }

    public function update($id, Request $request)
    {
        return Specialization::where('id', $id)->update(['name' => $request->name]);
    }

    public function destroy($id)
    {
        // Find the specialization by ID
        $specialization = Specialization::find($id);

        // Check if specialization exists
        if (!$specialization) {
            return response()->json([
                'message' => 'Specialization not found',
            ], 404);
        }

        // Delete the specialization
        $specialization->delete();
    }

}
