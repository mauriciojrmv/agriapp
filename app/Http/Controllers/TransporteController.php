<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transporte;
use App\Models\Conductor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class TransporteController extends Controller
{
    // Obtener todos los transportes con sus relaciones
    public function index()
    {
        return response()->json(Transporte::with('conductor', 'rutaCargaPedidos', 'rutaCargaOfertas')->get(), 200);
    }

    // Crear un nuevo transporte
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_conductor' => 'required|exists:conductors,id',
                'capacidadmaxkg' => 'required|numeric|min:0',
                'marca' => 'required|string|max:255',
                'modelo' => 'required|string|max:255',
                'placa' => 'required|string|max:20|unique:transportes,placa'
            ], [
                'id_conductor.required' => 'El campo id_conductor es obligatorio.',
                'id_conductor.exists' => 'El conductor especificado no existe.',
                'capacidadmaxkg.required' => 'El campo capacidad máxima es obligatorio.',
                'capacidadmaxkg.numeric' => 'La capacidad máxima debe ser un número.',
                'marca.required' => 'El campo marca es obligatorio.',
                'modelo.required' => 'El campo modelo es obligatorio.',
                'placa.required' => 'El campo placa es obligatorio.',
                'placa.unique' => 'Esta placa ya está registrada.'
            ]);

            $transporte = Transporte::create($request->all());
            return response()->json($transporte, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un transporte específico con sus relaciones
    public function show($id)
    {
        try {
            $transporte = Transporte::with('conductor', 'rutaCargaPedidos', 'rutaCargaOfertas')->findOrFail($id);
            return response()->json($transporte, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transporte no encontrado'], 404);
        }
    }

    // Actualizar datos de un transporte
    public function update(Request $request, $id)
    {
        try {
            $transporte = Transporte::findOrFail($id);

            $request->validate([
                'id_conductor' => 'sometimes|required|exists:conductors,id',
                'capacidadmaxkg' => 'sometimes|required|numeric|min:0',
                'marca' => 'sometimes|required|string|max:255',
                'modelo' => 'sometimes|required|string|max:255',
                'placa' => 'sometimes|required|string|max:20|unique:transportes,placa,' . $id
            ], [
                'id_conductor.exists' => 'El conductor especificado no existe.',
                'capacidadmaxkg.numeric' => 'La capacidad máxima debe ser un número.',
                'marca.required' => 'El campo marca es obligatorio.',
                'modelo.required' => 'El campo modelo es obligatorio.',
                'placa.unique' => 'Esta placa ya está registrada.'
            ]);

            $transporte->update($request->all());
            return response()->json($transporte, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transporte no encontrado'], 404);
        }
    }

    // Eliminar un transporte
    public function destroy($id)
    {
        try {
            $transporte = Transporte::findOrFail($id);
            $transporte->delete();
            return response()->json(['message' => 'Transporte eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transporte no encontrado'], 404);
        }
    }

    // Obtener todos los transportes de un conductor específico
    public function getTransportesByConductor($conductorId)
    {
        try {
            $conductor = Conductor::findOrFail($conductorId);
            $transportes = Transporte::where('id_conductor', $conductorId)->get();

            if ($transportes->isEmpty()) {
                return response()->json(['message' => 'No se encontraron transportes para este conductor'], 404);
            }

            return response()->json($transportes, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Conductor no encontrado'], 404);
        }
    }

    // Buscar transportes por capacidad máxima
    public function searchByCapacity(Request $request)
    {
        $request->validate([
            'min_capacity' => 'nullable|numeric|min:0',
            'max_capacity' => 'nullable|numeric|min:0'
        ]);

        $query = Transporte::query();

        if ($request->filled('min_capacity')) {
            $query->where('capacidadmaxkg', '>=', $request->min_capacity);
        }

        if ($request->filled('max_capacity')) {
            $query->where('capacidadmaxkg', '<=', $request->max_capacity);
        }

        $transportes = $query->get();

        if ($transportes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron transportes en el rango de capacidad especificado'], 404);
        }

        return response()->json($transportes, 200);
    }
}
