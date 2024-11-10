<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agricultor;
use App\Models\Terreno;
use App\Models\Produccion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class AgricultorController extends Controller
{
    // Obtener todos los agricultores
    public function index()
    {
        return response()->json(Agricultor::all(), 200);
    }

    // Crear un nuevo agricultor
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'telefono' => 'required|string|max:20|unique:agricultors,telefono',
                'email' => 'required|email|unique:agricultors,email',
                'direccion' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'informacion_bancaria' => 'nullable|string|max:255',
                'nit' => 'required|string|unique:agricultors,nit',
                'carnet' => 'required|string|unique:agricultors,carnet',
                'licencia_funcionamiento' => 'nullable|string|max:50',
                'estado' => 'required|string'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'telefono.unique' => 'Este número de teléfono ya está registrado.',
                'email.required' => 'El campo email es obligatorio.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.required' => 'El campo contraseña es obligatorio.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'nit.unique' => 'Este NIT ya está registrado.',
                'carnet.unique' => 'Este carnet ya está registrado.'
            ]);

            $agricultor = Agricultor::create($request->all());
            return response()->json($agricultor, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un agricultor específico
    public function show($id)
    {
        try {
            $agricultor = Agricultor::findOrFail($id);
            return response()->json($agricultor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Agricultor no encontrado'], 404);
        }
    }

    // Actualizar datos de un agricultor
    public function update(Request $request, $id)
    {
        try {
            $agricultor = Agricultor::findOrFail($id);

            $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'apellido' => 'sometimes|required|string|max:255',
                'telefono' => 'sometimes|required|string|max:20|unique:agricultors,telefono,' . $id,
                'email' => 'sometimes|required|email|unique:agricultors,email,' . $id,
                'direccion' => 'sometimes|required|string|max:255',
                'password' => 'sometimes|required|string|min:8',
                'informacion_bancaria' => 'nullable|string|max:255',
                'nit' => 'sometimes|required|string|unique:agricultors,nit,' . $id,
                'carnet' => 'sometimes|required|string|unique:agricultors,carnet,' . $id,
                'licencia_funcionamiento' => 'nullable|string|max:50',
                'estado' => 'sometimes|required|string'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'telefono.unique' => 'Este número de teléfono ya está registrado.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'nit.unique' => 'Este NIT ya está registrado.',
                'carnet.unique' => 'Este carnet ya está registrado.'
            ]);

            $agricultor->update($request->all());
            return response()->json($agricultor, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Agricultor no encontrado'], 404);
        }
    }

    // Eliminar un agricultor
    public function destroy($id)
    {
        try {
            $agricultor = Agricultor::findOrFail($id);
            $agricultor->delete();
            return response()->json(['message' => 'Agricultor eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Agricultor no encontrado'], 404);
        }
    }

    // Obtener los terrenos de un agricultor específico
    public function getTerrenos($id)
    {
        try {
            $agricultor = Agricultor::findOrFail($id);
            $terrenos = Terreno::where('id_agricultor', $id)->get();

            if ($terrenos->isEmpty()) {
                return response()->json(['message' => 'No se encontraron terrenos para este agricultor'], 404);
            }

            return response()->json($terrenos, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Agricultor no encontrado'], 404);
        }
    }

    // Obtener las producciones de un agricultor específico
    public function getProducciones($id)
    {
        try {
            $agricultor = Agricultor::findOrFail($id);
            $producciones = Produccion::whereHas('terreno', function ($query) use ($id) {
                $query->where('id_agricultor', $id);
            })->get();

            if ($producciones->isEmpty()) {
                return response()->json(['message' => 'No se encontraron producciones para este agricultor'], 404);
            }

            return response()->json($producciones, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Agricultor no encontrado'], 404);
        }
    }
}
