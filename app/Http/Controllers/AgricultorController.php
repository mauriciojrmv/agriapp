<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agricultor;
use App\Models\Produccion;
use App\Models\Oferta;
use App\Models\OfertaDetalle;
use App\Models\CargaOferta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AgricultorController extends Controller
{
    // Obtener todos los agricultores con sus terrenos
    public function index()
    {
        return response()->json(Agricultor::with('terrenos')->get(), 200);
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
                'estado' => 'sometimes|required|string|in:activo,inactivo',
                'tokendevice' => 'nullable|string|unique:agricultors,tokendevice'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'telefono.unique' => 'Este número de teléfono ya está registrado.',
                'email.required' => 'El campo email es obligatorio.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.required' => 'El campo contraseña es obligatorio.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'nit.unique' => 'Este NIT ya está registrado.',
                'carnet.unique' => 'Este carnet ya está registrado.',
                'estado.in' => 'El campo estado solo puede tener los valores "activo" o "inactivo".',
                'tokendevice.unique' => 'El token de dispositivo ya está en uso.'
            ]);

            $data = $request->all();
            $data['password'] = Hash::make($request->password); // Cifrar la contraseña

            $agricultor = Agricultor::create($data);
            return response()->json($agricultor, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un agricultor específico con sus terrenos
    public function show($id)
    {
        try {
            $agricultor = Agricultor::with('terrenos')->findOrFail($id);
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
                'estado' => 'sometimes|required|string|in:activo,inactivo',
                'tokendevice' => 'nullable|string|unique:agricultors,tokendevice,' . $id
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'telefono.unique' => 'Este número de teléfono ya está registrado.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'nit.unique' => 'Este NIT ya está registrado.',
                'carnet.unique' => 'Este carnet ya está registrado.',
                'estado.in' => 'El campo estado solo puede tener los valores "activo" o "inactivo".',
                'tokendevice.unique' => 'El token de dispositivo ya está en uso.'
            ]);

            $data = $request->all();
            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password); // Cifrar si se proporciona una nueva contraseña
            }

            $agricultor->update($data);
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

    // Obtener las producciones de un agricultor específico
    public function getProduccionesByAgricultorId($id)
{
    try {
        // Encuentra al agricultor por su ID
        $agricultor = Agricultor::findOrFail($id);

        // Obtén las producciones relacionadas con sus terrenos
        $producciones = Produccion::whereHas('terreno', function ($query) use ($id) {
            $query->where('id_agricultor', $id);
        })->with(['producto', 'unidadMedida', 'terreno'])->get();

        if ($producciones->isEmpty()) {
            return response()->json(['message' => 'No se encontraron producciones para este agricultor'], 404);
        }

        return response()->json($producciones, 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Agricultor no encontrado'], 404);
    }
}

// Obtener las ofertas relacionadas con un agricultor
public function getOfertasByAgricultorId($id)
{
    try {
        // Verificar si el agricultor existe
        $agricultor = Agricultor::findOrFail($id);

        // Obtener las ofertas relacionadas a las producciones de los terrenos del agricultor
        $ofertas = Oferta::whereHas('produccion.terreno', function ($query) use ($id) {
            $query->where('id_agricultor', $id);
        })->with(['produccion.producto', 'detalles'])->get();

        // Verificar si no se encontraron ofertas
        if ($ofertas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron ofertas para este agricultor'], 404);
        }

        // Retornar las ofertas
        return response()->json($ofertas, 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Agricultor no encontrado'], 404);
    }
}

public function getOfertaDetallesByAgricultorId($id)
{
    try {
        // Verificar si el agricultor existe
        $agricultor = Agricultor::findOrFail($id);

        // Obtener los detalles de ofertas relacionados con las producciones de los terrenos del agricultor
        $ofertaDetalles = OfertaDetalle::whereHas('produccion.terreno', function ($query) use ($id) {
            $query->where('id_agricultor', $id);
        })->with(['produccion.producto', 'moneda', 'unidadMedida', 'oferta'])->get();

        // Verificar si no se encontraron detalles de oferta
        if ($ofertaDetalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles de oferta para este agricultor'], 404);
        }

        // Retornar los detalles de oferta
        return response()->json($ofertaDetalles, 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Agricultor no encontrado'], 404);
    }
}

public function getOfertaCargasByAgricultorId($id)
{
    try {
        // Verificar si el agricultor existe
        $agricultor = Agricultor::findOrFail($id);

        // Obtener las cargas de ofertas relacionadas con las producciones de los terrenos del agricultor
        $cargaOfertas = CargaOferta::whereHas('ofertaDetalle.produccion.terreno', function ($query) use ($id) {
            $query->where('id_agricultor', $id);
        })->with(['ofertaDetalle.produccion', 'ofertaDetalle.unidadMedida', 'ofertaDetalle.moneda'])->get();

        // Verificar si no se encontraron cargas de ofertas
        if ($cargaOfertas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron cargas de oferta para este agricultor'], 404);
        }

        // Retornar las cargas de ofertas
        return response()->json($cargaOfertas, 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Agricultor no encontrado'], 404);
    }
}


    public function getTerrenosByAgricultorId($id)
{
    try {
        // Verificar si el agricultor existe
        $agricultor = Agricultor::findOrFail($id);

        // Obtener los terrenos asociados al agricultor
        $terrenos = $agricultor->terrenos;

        // Verificar si no tiene terrenos
        if ($terrenos->isEmpty()) {
            return response()->json(['message' => 'Este agricultor no tiene terrenos registrados'], 404);
        }

        // Retornar los terrenos asociados
        return response()->json($terrenos, 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Agricultor no encontrado'], 404);
    }
}
}
