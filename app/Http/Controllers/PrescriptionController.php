<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function store(Request $request)
    {
        $editing = $request->query('editing');

        $values = $request->input('values', []);
        $appointment_id = $request->query('id');

        $problem = $values['problem'] ?? null;
        $test = $values['test'] ?? null;
        $advice = $values['advice'] ?? null;


        if ($editing === true) {
            Prescription::where('appointment_id', $appointment_id)
                ->update([
                    'test' => $test,
                    'advice' => $advice,
                    'problem_description' => $problem,
                ]);
            $prescription = Prescription::where('appointment_id', $appointment_id)
                ->first();

            PrescriptionMedicine::where('prescription_id', $prescription->id)->delete();
        } else {
            $prescription = Prescription::create([
                'appointment_id' => $appointment_id,
                'test' => $test,
                'advice' => $advice,
                'problem_description' => $problem,
            ]);
        }

        foreach ($values as $index => $item) {
            if (is_numeric($index)) {

                PrescriptionMedicine::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $item['medicine_id'],
                    'dosage' => $item['dosage'],
                    'duration' => $item['duration'],
                    'time' => $item['time'],
                    'interval' => $item['interval'],
                    'hierarchy' => $index,
                ]);
            }
        }
    }

    public function prescription(Request $request)
    {
        $id = $request->query('id');

        $prescription = Prescription::where('appointment_id', $id)
            ->with('medicines')
            ->first();

        if ($prescription) {

            // Convert to array
            $prescriptionArray = $prescription->toArray();

            // Output or use the array
            return response()->json($prescriptionArray);
        }
    }



    public function generate_pdf($id)
    {
        $prescription = Prescription::where('appointment_id', $id)
            ->with(['medicines', 'appointment.doctor', 'appointment.patient'])
            ->first();

        // return $prescription;
        $html = $this->generateHtml($prescription);

        $pdf = PDF::loadHTML($html);
        return response()->make($pdf->stream('prescription.pdf'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="prescription.pdf"');
    }

    private function generateHtml($prescription)
    {

        $today = Carbon::parse($prescription->created_at)->format('Y-m-d');
        $doctor = $prescription->appointment->doctor->name;
        $doctor_email = $prescription->appointment->doctor->email;
        $patient = $prescription->appointment->patient->name;
        $patient_email = $prescription->appointment->patient->email;

        $test = nl2br($prescription->test); // Convert newlines to <br> tags
        $problem = nl2br($prescription->problem_description); // Convert newlines to <br> tags
        $advice = nl2br($prescription->advice); // Convert newlines to <br> tags

        $medicineRows = '';
        foreach ($prescription->medicines as $index => $medicine) {
            $medicineName = $medicine->medicine->name;
            $dosage = "{$medicine->dosage}";

            // Check for 'after_meal' and 'before_meal' and format accordingly
            if ($medicine->time === 'after_meal') {
                $formattedTime = 'After meal';
            } elseif ($medicine->time === 'before_meal') {
                $formattedTime = 'Before meal';
            } else {
                // In case there are other values or null, keep it as it is
                $formattedTime = ucfirst(str_replace('_', ' ', $medicine->time));
            }

            $duration = str_replace('_', ' ', ucfirst($medicine->duration));
            $interval = str_replace('_', ' ', ucfirst($medicine->interval));

            $medicineRows .= "
            <tr class='border-b'>
                <td class='w-full align-top text-sm py-3' style='width: 9%;'>" . ($index + 1) . "</td>
                <td class='w-full align-top text-sm py-3'>{$medicineName}</td>
                <td class='w-full align-top text-sm py-3'>{$dosage} ({$formattedTime})</td>
                <td class='w-full align-top text-sm py-3'>{$duration} ({$interval})</td>
            </tr>
            ";

        }


        $html = "
        <html>
        <head>
            <title>Prescription</title>
        </head>
        <body>

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

            body {
                font-family: 'Poppins', sans-serif;
            }

            *,
            ::before,
            ::after {
            box-sizing: border-box;
            /* 1 */
            border-width: 0;
            /* 2 */
            border-style: solid;
            /* 2 */
            border-color: #e5e7eb;
            /* 2 */
            }

            .fixed{
            position: fixed;
            }

            .bottom-0{
            bottom: 0px;
            }

            .left-0{
            left: 0px;
            }

            .table{
            display: table;
            }

            .h-12{
            height: 3rem;
            }

            .w-1/2{
            width: 50%;
            }

            .w-full{
            width: 100%;
            }

            .border-collapse{
            border-collapse: collapse;
            }

            .border-spacing-0{
            --tw-border-spacing-x: 0px;
            --tw-border-spacing-y: 0px;
            border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
            }

            .whitespace-nowrap{
            white-space: nowrap;
            }

            .border-b{
            border-bottom-width: 1px;
            }
            
            .border-t{
            border-top-width: 1px;
            }

            .border-b-2{
            border-bottom-width: 2px;
            }

            .border-r{
            border-right-width: 1px;
            }

            .border-main{
            border-color: #5c6ac4;
            }

            .bg-main{
            background-color: #5c6ac4;
            }

            .bg-slate-100{
            background-color: #f1f5f9;
            }

            .p-3{
            padding: 0.75rem;
            }

            .px-14{
            padding-left: 3.5rem;
            padding-right: 3.5rem;
            }
            
            .px-20{
            padding-left: 10rem;
            padding-right: 10rem;
            }

            .mx-20{
            margin-left: 10rem;
            margin-right: 10rem;
            }

            .px-2{
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            }

            .py-10{
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
            }

            .py-3{
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            }

            .my-3{
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
            }

            .py-4{
            padding-top: 1rem;
            padding-bottom: 1rem;
            }

            .py-6{
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            }

            .pb-3{
            padding-bottom: 0.75rem;
            }

            .pl-2{
            padding-left: 0.5rem;
            }

            .pl-3{
            padding-left: 0.75rem;
            }

            .pl-4{
            padding-left: 1rem;
            }
            
            .ml-4{
            margin-left: 1rem;
            }

            .pr-3{
            padding-right: 0.75rem;
            }

            .pr-4{
            padding-right: 1rem;
            }

            .pr-10{
            padding-right: 2.5rem;
            }

            .pl-10{
            padding-left: 2.5rem;
            }
            
            .mr-10{
            margin-right: 2.5rem;
            }

            .text-center{
            text-align: center;
            }

            .text-right{
            text-align: right;
            }

            .align-top{
            vertical-align: top;
            }

            .text-sm{
            font-size: 0.875rem;
            line-height: 1.25rem;
            }

            .text-xs{
            font-size: 0.75rem;
            line-height: 1rem;
            }

            .font-bold{
            font-weight: 700;
            }

            .italic{
            font-style: italic;
            }

            .bg-gray{
            background-color: #f4f4f4;
            }

            .text-main{
            color: #5c6ac4;
            }

            .text-neutral-600{
            color: #525252;
            }

            .text-neutral-700{
            color: #404040;
            }

            .text-slate-300{
            color: #cbd5e1;
            }

            .text-slate-400{
            color: #94a3b8;
            }

            .text-white{
            color: #fff;
            }
        </style>
        <div>
        <div>
        <div class='px-2'>
            <table class='w-full border-collapse border-spacing-0'>
            <tbody>
                <tr>
                <td class='w-full align-top'>
                    <div>
                        <h3>eClinic Nexus</h3>
                        <p>Your Health, Our Expertise</p>
                    </div>
                </td>

                <td class='align-top'>
                    <div class='text-sm'>
                    <table class='border-collapse border-spacing-0'>
                        <tbody>
                        <tr>
                            <td class=''>
                            <div>
                                <p class='whitespace-nowrap text-slate-400 text-right'>Date</p>
                                <p class='whitespace-nowrap font-bold text-main text-right'>{$today}</p>
                            </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </td>
                </tr>
            </tbody>
            </table>
        </div>

        <div class='px-2 py-5'>
            <table class='w-full border-collapse border-spacing-0 border-b border-t'>
                <tbody>
                    <tr>
                        <td class='w-full align-top text-sm'>
                            <strong>Doctor Name: </strong>
                            {$doctor}
                        </td>

                        <td class='w-full align-top text-sm text-right'>
                            <strong>Patient Name: </strong>
                            {$patient}
                        </td>
                    </tr>
                    <tr class='pb-3'>
                        <td class='w-full align-top text-sm'>
                            <strong>Doctor Email: </strong>
                            {$doctor_email}
                        </td>

                        <td class='w-full align-top text-sm text-right'>
                            <strong>Patient Email: </strong>
                            {$patient_email}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class='my-3'>
            <p class='text-main'>
                <strong>
                Problem:
                </strong>
            </p>
            {$problem}
        </div>

        <div class='my-3'>
            <p class='text-main'>
                <strong>
                Test:
                </strong>
            </p>
            {$test}
        </div>

        <div class='my-3'>
            <p class='text-main'>
                <strong>
                Advice:
                </strong>
            </p>
            {$advice}
        </div>

        <div class='my-10'>
            <table class='w-full border-collapse border-spacing-0 py-6'>
                <tbody>
                    <tr class='bg-slate-100 py-3'>
                        <td class='w-full align-top font-bold py-3' style='width: 9%;'>
                            S.No.
                        </td>

                        <td class='w-full align-top font-bold py-3'>
                            Medicine Name
                        </td>

                        <td class='w-full align-top font-bold py-3'>
                            Dosage
                        </td>
                        
                        <td class='w-full align-top font-bold py-3'>
                            Duration
                        </td>
                    </tr>
                    {$medicineRows}
                </tbody>
            </table>
        </div>
        </div>
        </body>
        </html>
        ";
        return $html;
    }

}
