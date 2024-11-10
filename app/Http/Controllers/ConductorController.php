<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conductor;
use App\Models\Transporte;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ConductorController extends Controller
{
    // Obtener todos los conductores
    public function index()
    {
        return response()->json(Conductor::all(), 200);
    }

    // Crear un nuevo conductor
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'carnet' => 'required|string|max:20|unique:conductors,carnet',
                'licencia_conducir' => 'required|string|max:20',
                'fecha_nacimiento' => 'required|date',
                'direccion' => 'required|string|max:255',
                'email' => 'required|email|unique:conductors,email',
                'password' => 'required|string|min:8',
                'ubicacion_latitud' => 'nullable|numeric',
                'ubicacion_longitud' => 'nullable|numeric',
                'estado' => 'required|string'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'carnet.required' => 'El campo carnet es obligatorio.',
                'carnet.unique' => 'Este carnet ya está registrado.',
                'licencia_conducir.required' => 'El campo licencia de conducir es obligatorio.',
                'email.required' => 'El campo email es obligatorio.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.required' => 'El campo contraseña es obligatorio.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
            ]);

            $conductor = Conductor::create($request->all());
            return response()->json($conductor, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un conductor específico
    public function show($id)
    {
        try {
            $conductor = Conductor::findOrFail($id);
            return response()->json($conductor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Conductor no encontrado'], 404);
        }
    }

    // Actualizar datos de un conductor
    public function update(Request $request, $id)
    {
        try {
            $conductor = Conductor::findOrFail($id);

            $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'apellido' => 'sometimes|required|string|max:255',
                'carnet' => 'sometimes|required|string|max:20|unique:conductors,carnet,' . $id,
                'licencia_conducir' => 'sometimes|required|string|max:20',
                'fecha_nacimiento' => 'sometimes|required|date',
                'direccion' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:conductors,email,' . $id,
                'password' => 'sometimes|required|string|min:8',
                'ubicacion_latitud' => 'nullable|numeric',
                'ubicacion_longitud' => 'nullable|numeric',
                'estado' => 'sometimes|required|string'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'carnet.unique' => 'Este carnet ya está registrado.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
            ]);

            $conductor->update($request->all());
            return response()->json($conductor, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Conductor no encontrado'], 404);
        }
    }

    // Eliminar un conductor
    public function destroy($id)
    {
        try {
            $conductor = Conductor::findOrFail($id);
            $conductor->delete();
            return response()->json(['message' => 'Conductor eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Conductor no encontrado'], 404);
        }
    }

    // Obtener los transportes de un conductor específico
    public function getTransportes($id)
    {
        try {
            $conductor = Conductor::findOrFail($id);
            $transportes = Transporte::where('id_conductor', $id)->get();

            if ($transportes->isEmpty()) {
                return response()->json(['message' => 'No se encontraron transportes para este conductor'], 404);
            }

            return response()->json($transportes, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Conductor no encontrado'], 404);
        }
    }
}
