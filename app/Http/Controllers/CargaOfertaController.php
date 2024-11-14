<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CargaOferta;
use App\Models\OfertaDetalle;
use App\Models\RutaCargaOferta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CargaOfertaController extends Controller
{
    // Obtener todas las cargas de oferta
    public function index()
    {
        return response()->json(
            CargaOferta::with([
                'ofertaDetalle.produccion.producto',
                'ofertaDetalle.unidadMedida',
                'ofertaDetalle.moneda',
                'rutas'
            ])->get(),
            200
        );
    }

    // Crear una nueva carga de oferta
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_oferta_detalle' => 'required|exists:oferta_detalles,id',
                'pesokg' => 'required|numeric|min:0.1',
                'precio' => 'required|numeric|min:0',
                'estado' => 'required|string|max:255'
            ], [
                'id_oferta_detalle.required' => 'El campo id_oferta_detalle es obligatorio.',
                'id_oferta_detalle.exists' => 'El detalle de oferta especificado no existe.',
                'pesokg.required' => 'El campo peso es obligatorio.',
                'pesokg.numeric' => 'El peso debe ser un número.',
                'precio.required' => 'El campo precio es obligatorio.',
                'precio.numeric' => 'El precio debe ser un número.',
                'estado.required' => 'El campo estado es obligatorio.'
            ]);

            // Verificar que el peso de la carga no exceda la cantidad disponible en la oferta
            $ofertaDetalle = OfertaDetalle::findOrFail($request->id_oferta_detalle);
            $cantidadDisponible = $ofertaDetalle->cantidad_fisico - $ofertaDetalle->cantidad_comprometido;

            if ($request->pesokg > $cantidadDisponible) {
                return response()->json([
                    'message' => 'El peso de la carga supera la cantidad disponible en la oferta.'
                ], 422);
            }

            // Actualizar la cantidad comprometida en el detalle de la oferta
            $ofertaDetalle->cantidad_comprometido += $request->pesokg;
            $ofertaDetalle->save();

            $cargaOferta = CargaOferta::create($request->all());
            return response()->json($cargaOferta, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una carga de oferta específica
    public function show($id)
    {
        try {
            $cargaOferta = CargaOferta::with([
                'ofertaDetalle.produccion.producto',
                'ofertaDetalle.unidadMedida',
                'ofertaDetalle.moneda',
                'rutas'
            ])->findOrFail($id);

            return response()->json($cargaOferta, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de oferta no encontrada'], 404);
        }
    }

    // Actualizar datos de una carga de oferta
    public function update(Request $request, $id)
    {
        try {
            $cargaOferta = CargaOferta::findOrFail($id);

            $request->validate([
                'id_oferta_detalle' => 'sometimes|required|exists:oferta_detalles,id',
                'pesokg' => 'sometimes|required|numeric|min:0.1',
                'precio' => 'sometimes|required|numeric|min:0',
                'estado' => 'sometimes|required|string|max:255'
            ]);

            // Verificar si se intenta actualizar el peso
            if ($request->has('pesokg')) {
                $ofertaDetalle = OfertaDetalle::findOrFail($request->id_oferta_detalle ?? $cargaOferta->id_oferta_detalle);

                // Calcular la diferencia de peso para ajustar la cantidad comprometida
                $pesoDiferencia = $request->pesokg - $cargaOferta->pesokg;
                $cantidadDisponible = $ofertaDetalle->cantidad_fisico - $ofertaDetalle->cantidad_comprometido;

                if ($pesoDiferencia > $cantidadDisponible) {
                    return response()->json([
                        'message' => 'La nueva cantidad de peso supera la cantidad disponible en la oferta.'
                    ], 422);
                }

                // Actualizar la cantidad comprometida en el detalle de la oferta
                $ofertaDetalle->cantidad_comprometido += $pesoDiferencia;
                $ofertaDetalle->save();
            }

            $cargaOferta->update($request->all());
            return response()->json($cargaOferta, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de oferta no encontrada'], 404);
        }
    }

    // Eliminar una carga de oferta
    public function destroy($id)
    {
        try {
            $cargaOferta = CargaOferta::findOrFail($id);
            $ofertaDetalle = OfertaDetalle::findOrFail($cargaOferta->id_oferta_detalle);

            // Restar el peso de la carga eliminada de la cantidad comprometida
            $ofertaDetalle->cantidad_comprometido -= $cargaOferta->pesokg;
            $ofertaDetalle->save();

            $cargaOferta->delete();
            return response()->json(['message' => 'Carga de oferta eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de oferta no encontrada'], 404);
        }
    }

    // Función adicional: Obtener todas las cargas asociadas con un detalle de oferta específico
    public function getCargasByDetalle($ofertaDetalleId)
    {
        $cargas = CargaOferta::where('id_oferta_detalle', $ofertaDetalleId)->get();

        if ($cargas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron cargas para este detalle de oferta'], 404);
        }

        return response()->json($cargas, 200);
    }

    // Función adicional: Obtener cargas de oferta filtradas por estado
    public function getCargasByEstado($estado)
    {
        $cargas = CargaOferta::where('estado', $estado)->get();

        if ($cargas->isEmpty()) {
            return response()->json(['message' => "No se encontraron cargas de oferta con el estado: $estado"], 404);
        }

        return response()->json($cargas, 200);
    }
}
