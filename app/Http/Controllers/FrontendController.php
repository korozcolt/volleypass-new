<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Department;
use App\Models\City;
use App\Mail\ContactFormMail;

class FrontendController extends Controller
{
    /**
     * Mostrar página de contacto
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Obtener departamentos activos
     */
    public function getDepartments()
    {
        $departments = Department::active()->orderBy('name')->get(['id', 'name']);
        return response()->json($departments);
    }

    /**
     * Obtener ciudades por departamento
     */
    public function getCities($departmentId)
    {
        $cities = City::where('department_id', $departmentId)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);
        
        return response()->json($cities);
    }

    /**
     * Procesar formulario de contacto
     */
    public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'city_id' => 'required|exists:cities,id',
            'message' => 'required|string|max:1000',
        ], [
            'full_name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'phone.required' => 'El número de teléfono es obligatorio.',
            'department_id.required' => 'El departamento es obligatorio.',
            'department_id.exists' => 'El departamento seleccionado no es válido.',
            'city_id.required' => 'El municipio es obligatorio.',
            'city_id.exists' => 'El municipio seleccionado no es válido.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.max' => 'El mensaje no puede exceder 1000 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $department = Department::find($request->department_id);
            $city = City::find($request->city_id);

            $contactData = [
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'phone_secondary' => $request->phone_secondary,
                'department' => $department->name,
                'city' => $city->name,
                'message' => $request->message,
                'submitted_at' => now()->format('d/m/Y H:i:s')
            ];

            // Enviar correo al email configurado en .env
            $contactEmail = config('mail.contact_email', 'ing.korozco@gmail.com');
            Mail::to($contactEmail)->send(new ContactFormMail($contactData));

            return response()->json([
                'success' => true,
                'message' => 'Su solicitud ha sido enviada exitosamente. Nos pondremos en contacto con usted pronto.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al enviar su solicitud. Por favor, inténtelo nuevamente.'
            ], 500);
        }
    }
}