<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RutaCargaOferta;
use App\Models\CargaOferta;
use App\Models\RutaOferta;
use App\Models\Transporte;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RutaCargaOfertaController extends Controller
{
    // Obtener todas las cargas asociadas a rutas de oferta
    public function index()
    {
        $rutasCargasOfertas = RutaCargaOferta::with([
            'cargaOferta.ofertaDetalle.produccion.producto',
            'rutaOferta',
            'transporte.conductor'
            ])->get();
        return response()->json($rutasCargasOfertas, 200);
    }

    // Crear una nueva carga de ruta de oferta
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_carga_oferta' => 'required|exists:carga_ofertas,id',
                'id_ruta_oferta' => 'required|exists:ruta_ofertas,id',
                'id_transporte' => 'required|exists:transportes,id',
                'orden' => 'required|integer|min:1',
                'estado' => 'required|string|max:255',
                'distancia' => 'required|numeric|min:0'
            ], [
                'id_carga_oferta.required' => 'El campo id_carga_oferta es obligatorio.',
                'id_carga_oferta.exists' => 'La carga de oferta especificada no existe.',
                'id_ruta_oferta.required' => 'El campo id_ruta_oferta es obligatorio.',
                'id_ruta_oferta.exists' => 'La ruta de oferta especificada no existe.',
                'id_transporte.required' => 'El campo id_transporte es obligatorio.',
                'id_transporte.exists' => 'El transporte especificado no existe.',
                'orden.required' => 'El campo orden es obligatorio.',
                'orden.integer' => 'El campo orden debe ser un número entero.',
                'estado.required' => 'El campo estado es obligatorio.',
                'distancia.required' => 'El campo distancia es obligatorio.',
                'distancia.numeric' => 'La distancia debe ser un número.'
            ]);

            $rutaCargaOferta = RutaCargaOferta::create($request->all());

            return response()->json($rutaCargaOferta, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ocurrió un error al crear la ruta de carga de oferta.', 'error' => $e->getMessage()], 500);
        }
    }

    // Mostrar detalles de una carga de ruta de oferta específica
    public function show($id)
    {
        try {
            $rutaCargaOferta = RutaCargaOferta::with([
            'cargaOferta.ofertaDetalle.produccion.producto',
            'rutaOferta',
            'transporte.conductor'])->findOrFail($id);
            return response()->json($rutaCargaOferta, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de oferta no encontrada'], 404);
        }
    }

    // Actualizar datos de una carga de ruta de oferta
    public function update(Request $request, $id)
    {
        try {
            $rutaCargaOferta = RutaCargaOferta::findOrFail($id);

            $request->validate([
                'id_carga_oferta' => 'sometimes|required|exists:carga_ofertas,id',
                'id_ruta_oferta' => 'sometimes|required|exists:ruta_ofertas,id',
                'id_transporte' => 'sometimes|required|exists:transportes,id',
                'orden' => 'sometimes|required|integer|min:1',
                'estado' => 'sometimes|required|string|max:255',
                'distancia' => 'sometimes|required|numeric|min:0'
            ], [
                'id_carga_oferta.exists' => 'La carga de oferta especificada no existe.',
                'id_ruta_oferta.exists' => 'La ruta de oferta especificada no existe.',
                'id_transporte.exists' => 'El transporte especificado no existe.',
                'orden.integer' => 'El campo orden debe ser un número entero.',
                'distancia.numeric' => 'La distancia debe ser un número.'
            ]);

            $rutaCargaOferta->update($request->all());

            return response()->json($rutaCargaOferta, 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de oferta no encontrada'], 404);
        }
    }

    // Eliminar una carga de ruta de oferta
    public function destroy($id)
    {
        try {
            $rutaCargaOferta = RutaCargaOferta::findOrFail($id);
            $rutaCargaOferta->delete();

            return response()->json(['message' => 'Carga de ruta de oferta eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de oferta no encontrada'], 404);
        }
    }
}
