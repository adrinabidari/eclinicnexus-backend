<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        return Service::create([
            'service' => $request->service,
            'charge' => $request->charge,
        ]);
    }

    public function update($id, Request $request)
    {
        return Service::where('id', $id)->update([
            'service' => $request->service,
            'charge' => $request->charge,
        ]);
    }

    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found',
            ], 404);
        }

        $service->delete();
    }
}
