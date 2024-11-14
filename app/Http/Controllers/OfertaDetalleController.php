<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfertaDetalle;
use App\Models\Oferta;
use App\Models\Moneda;
use App\Models\UnidadMedida;
use App\Models\CargaOferta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class OfertaDetalleController extends Controller
{
    // Obtener todos los detalles de ofertas
    public function index()
    {
        return response()->json(
            OfertaDetalle::with([
                'oferta',
                'produccion.producto',
                'unidadMedida',
                'moneda',
                'cargas'
            ])->get(),
            200
        );
    }

    // Crear un nuevo detalle de oferta
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_oferta' => 'required|exists:ofertas,id',
                'id_unidadmedida' => 'required|exists:unidad_medidas,id',
                'id_moneda' => 'required|exists:monedas,id',
                'id_produccion' => 'required|exists:produccions,id',
                'descripcion' => 'nullable|string|max:255',
                'cantidad_fisico' => 'required|numeric|min:1',
                'cantidad_comprometido' => 'nullable|numeric|min:0',
                'precio' => 'required|numeric|min:0',
                'estado' => 'sometimes|string|max:255'
            ]);

            // Obtener la oferta y la producción asociada
            $oferta = Oferta::findOrFail($request->id_oferta);
            $produccion = $oferta->produccion;

            // Verificar cantidad disponible en la producción
            $cantidadDisponible = $produccion->cantidad;
            $cantidadOfertada = OfertaDetalle::where('id_produccion', $request->id_produccion)->sum('cantidad_fisico');

            if (($cantidadOfertada + $request->cantidad_fisico) > $cantidadDisponible) {
                return response()->json([
                    'message' => 'La cantidad de la oferta supera la cantidad disponible en la producción.'
                ], 422);
            }

            $detalle = OfertaDetalle::create($request->all());
            return response()->json($detalle, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un detalle de oferta específico
    public function show($id)
    {
        try {
            $ofertaDetalle = OfertaDetalle::with([
                'oferta',
                'produccion.producto',
                'unidadMedida',
                'moneda',
                'cargas'
            ])->findOrFail($id);

            return response()->json($ofertaDetalle, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de oferta no encontrado'], 404);
        }
    }

    // Actualizar datos de un detalle de oferta
    public function update(Request $request, $id)
    {
        try {
            $detalle = OfertaDetalle::findOrFail($id);

            $request->validate([
                'id_oferta' => 'sometimes|required|exists:ofertas,id',
                'id_unidadmedida' => 'sometimes|required|exists:unidad_medidas,id',
                'id_moneda' => 'sometimes|required|exists:monedas,id',
                'descripcion' => 'nullable|string|max:255',
                'cantidad_fisico' => 'sometimes|required|numeric|min:1',
                'cantidad_comprometido' => 'nullable|numeric|min:0',
                'precio' => 'sometimes|required|numeric|min:0',
                'estado' => 'sometimes|string|max:255'
            ]);

            $detalle->update($request->all());
            return response()->json($detalle, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de oferta no encontrado'], 404);
        }
    }

    // Eliminar un detalle de oferta
    public function destroy($id)
    {
        try {
            $detalle = OfertaDetalle::findOrFail($id);
            $detalle->delete();
            return response()->json(['message' => 'Detalle de oferta eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de oferta no encontrado'], 404);
        }
    }

    // Obtener todas las cargas asociadas con un detalle de oferta específico
    public function getCargas($id)
    {
        try {
            $detalle = OfertaDetalle::findOrFail($id);
            $cargas = CargaOferta::where('id_oferta_detalle', $id)->get();

            if ($cargas->isEmpty()) {
                return response()->json(['message' => 'No se encontraron cargas para este detalle de oferta'], 404);
            }

            return response()->json($cargas, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de oferta no encontrado'], 404);
        }
    }

    // Verificar disponibilidad en función de cantidad comprometida
    public function checkDisponibilidad($id)
    {
        try {
            $detalle = OfertaDetalle::findOrFail($id);
            $disponible = $detalle->cantidad_fisico > $detalle->cantidad_comprometido;

            return response()->json([
                'oferta_detalle_id' => $id,
                'disponible' => $disponible
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de oferta no encontrado'], 404);
        }
    }

    // Obtener detalles de ofertas filtrados por moneda
    public function getDetallesByMoneda($monedaId)
    {
        $detalles = OfertaDetalle::where('id_moneda', $monedaId)->get();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles de oferta para esta moneda'], 404);
        }

        return response()->json($detalles, 200);
    }

    // Obtener detalles de ofertas filtrados por unidad de medida
    public function getDetallesByUnidadMedida($unidadMedidaId)
    {
        $detalles = OfertaDetalle::where('id_unidadmedida', $unidadMedidaId)->get();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles de oferta para esta unidad de medida'], 404);
        }

        return response()->json($detalles, 200);
    }
}
