<?php

namespace App\Http\Controllers;

use App\Jobs\SendAppointmentEmail;
use App\Mail\AppointmentEmail;
use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use NumberToWords\NumberToWords;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user_role = $user->role_id;

        // admin or staff
        if ($user_role == 1 || $user_role == 3) {
            return Appointment::with(['timeSlot', 'service', 'patient', 'doctor'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else if ($user_role == 2) { // doctor
            return Appointment::where('doctor_id', $user->id)
                ->with(['timeSlot', 'service', 'patient', 'doctor'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else { // patient
            return Appointment::where('patient_id', $user->id)
                ->with(['timeSlot', 'service', 'patient', 'doctor'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required'],
            'service_id' => ['required'],
            'patient_id' => ['required'],
            'time_slot_id' => ['required'],
            'payment_method' => ['required'],
            'amount' => ['required'],
            'total_amount' => ['required'],
        ]);

        $appointment = Appointment::create([
            'doctor_id' => $request->doctor_id,
            'patient_id' => $request->patient_id,
            'service_id' => $request->service_id,
            'date' => $request->date,
            'day' => $request->day,
            'time_slot_id' => $request->time_slot_id,
            'status' => 'booked',
            'payment' => 0, // 0 for not paid and 1 for paid
            'payment_method' => $request->payment_method,
            'description' => $request->desc === 'undefined' ? null : $request->desc,
            'amount' => $request->amount,
            'additional_fee' => $request->add_fees === 'undefined' ? null : $request->add_fees,
            'total_amount' => $request->total_amount
        ]);

        if ($appointment) {
            $app = Appointment::where('id', $appointment->id)
                ->with(['timeSlot', 'service', 'patient', 'doctor'])
                ->orderBy('created_at', 'desc')
                ->first();

            SendAppointmentEmail::dispatch($app)->delay(now()->addSeconds(30));
        }
    }

    public function appointment($id)
    {
        $app = Appointment::where('id', $id)
            ->with(['timeSlot', 'service', 'patient', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->first();
        return $app;
    }

    public function status_edit($id, Request $request)
    {
        return Appointment::where('id', $id)->update(['status' => $request->status]);
    }
    public function payment_edit($id, Request $request)
    {
        return Appointment::where('id', $id)->update(['payment' => $request->payment]);
    }

    public function generate_pdf($id)
    {
        $appointment = Appointment::where('id', $id)
            ->with(['timeSlot', 'service', 'patient', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->first();

        $html = $this->generateHtml($appointment);

        $pdf = PDF::loadHTML($html);
        return response()->make($pdf->stream('appointment_invoice.pdf'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="appointment_invoice.pdf"');
    }


    private function generateHtml($appointment)
    {

        $am = NumberToWords::transformNumber('en', $appointment->total_amount);


        $today = $date = date('F j, Y');
        $date = date('F j, Y', strtotime($appointment->date));
        $additionalFee = $appointment->additional_fee ?? 0;
        $payment = $appointment->payment ? 'PAID' : 'PENDING';

        $html = "
        <html>
        <head>
            <title>Invoice #</title>
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
                            <td class='border-r pr-4'>
                            <div>
                                <p class='whitespace-nowrap text-slate-400 text-right'>Date</p>
                                <p class='whitespace-nowrap font-bold text-main text-right'>{$today}</p>
                            </div>
                            </td>
                            <td>
                            <div class='ml-4'>
                                <p class='whitespace-nowrap text-slate-400 text-right'>Invoice #</p>
                                <p class='whitespace-nowrap font-bold text-main text-right'>NEXUS{$appointment->id}</p>
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
                            {$appointment->doctor->name}
                        </td>

                        <td class='w-full align-top text-sm text-right'>
                            <strong>Patient Name: </strong>
                            {$appointment->patient->name}
                        </td>
                    </tr>
                    <tr>
                        <td class='w-full align-top text-sm'>
                            <strong>Doctor Email: </strong>
                            {$appointment->doctor->email}
                        </td>

                        <td class='w-full align-top text-sm text-right'>
                            <strong>Patient Email: </strong>
                            {$appointment->patient->email}
                        </td>
                    </tr>
                    <tr>
                        <td class='w-full align-top text-sm'>
                            <strong>Services: </strong>
                            {$appointment->service->service}
                        </td>

                        <td class='w-full align-top text-sm text-right'>
                            <strong>Payment method: </strong>
                            {$appointment->payment_method}
                        </td>
                    </tr>
                    <tr>
                        <td class='w-full align-top text-sm'>
                        </td>

                        <td class='w-full align-top text-sm text-right'>
                            <strong>Payment Status: </strong>
                            {$payment}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class='px-20 my-3 bg-slate-100'>
            <table class='w-full border-collapse border-spacing-0 py-6'>
                <tbody>
                    <tr>
                        <td class='w-full align-top text-sm font-bold text-main'>
                            Appointment Date:
                        </td>

                        <td class='w-full align-top text-sm text-right font-bold text-main'>
                            Appointment Time:
                        </td>
                    </tr>
                    <tr>
                        <td class='w-full align-top text-sm '>
                        {$date}
                        </td>

                        <td class='w-full align-top text-sm text-right'>
                        {$appointment->timeSlot->start_time} - {$appointment->timeSlot->end_time}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class='py-10'>
            <table class='w-full border-collapse border-spacing-0'>
                <tbody>
                    <tr>
                        <td class='w-1/2 align-top text-sm pr-10'>
                            <table class='w-full border-collapse border-spacing-0 py-6 bg-gray pl-3'>
                                <tbody>
                                    <tr>
                                        <td class='w-full align-top text-sm font-bold'>
                                            Invoice Amount In Words
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='w-full align-top text-sm'>
                                            {$am}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>

                        <td class='w-1/2 align-top text-sm text-right pl-10'>
                            <table class='w-full border-collapse border-spacing-0 py-6'>
                                <tbody>
                                    <tr>
                                        <td class='w-full align-top text-sm font-bold'>
                                            Amount:
                                        </td>

                                        <td class='w-full align-top text-sm text-right'>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='w-full align-top text-sm font-bold'>
                                            Charge:
                                        </td>

                                        <td class='w-full align-top text-sm text-right'>
                                        Rs. {$appointment->amount}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='w-full align-top text-sm font-bold '>
                                            Extra Charge:
                                        </td>

                                        <td class='w-full align-top text-sm text-right'>
                                        Rs. {$additionalFee}
                                        </td>
                                    </tr>
                                    <tr class='bg-gray'>
                                        <td class='w-full align-top text-sm font-bold'>
                                            Payable Amount:
                                        </td>

                                        <td class='w-full align-top text-sm text-right font-bold'>
                                        Rs. {$appointment->total_amount}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
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
