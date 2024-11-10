<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produccion;
use App\Models\Terreno;
use App\Models\Temporada;
use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProduccionController extends Controller
{
    // Obtener todos los detalles de producciones
    public function index()
    {
        return response()->json(Produccion::all(), 200);
    }

    // Crear una nueva producción
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_terreno' => 'required|exists:terrenos,id',
                'id_temporada' => 'required|exists:temporadas,id',
                'id_producto' => 'required|exists:productos,id',
                'id_unidadmedida' => 'required|exists:unidad_medidas,id',
                'descripcion' => 'nullable|string|max:255',
                'cantidad' => 'required|numeric|min:1',
                'fecha_cosecha' => 'required|date',
                'fecha_expiracion' => 'nullable|date|after_or_equal:fecha_cosecha',
                'estado' => 'required|string|max:255'
            ], [
                'id_terreno.required' => 'El campo id_terreno es obligatorio.',
                'id_terreno.exists' => 'El terreno especificado no existe.',
                'id_temporada.required' => 'El campo id_temporada es obligatorio.',
                'id_temporada.exists' => 'La temporada especificada no existe.',
                'id_producto.required' => 'El campo id_producto es obligatorio.',
                'id_producto.exists' => 'El producto especificado no existe.',
                'id_unidadmedida.required' => 'El campo id_unidadmedida es obligatorio.',
                'id_unidadmedida.exists' => 'La unidad de medida especificada no existe.',
                'cantidad.required' => 'El campo cantidad es obligatorio.',
                'fecha_cosecha.required' => 'El campo fecha de cosecha es obligatorio.',
                'estado.required' => 'El campo estado es obligatorio.'
            ]);

            $produccion = Produccion::create($request->all());
            return response()->json($produccion, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar una producción específica
    public function show($id)
    {
        try {
            $produccion = Produccion::findOrFail($id);
            return response()->json($produccion, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producción no encontrada'], 404);
        }
    }

    // Actualizar datos de una producción
    public function update(Request $request, $id)
    {
        try {
            $produccion = Produccion::findOrFail($id);

            $request->validate([
                'id_terreno' => 'sometimes|required|exists:terrenos,id',
                'id_temporada' => 'sometimes|required|exists:temporadas,id',
                'id_producto' => 'sometimes|required|exists:productos,id',
                'id_unidadmedida' => 'sometimes|required|exists:unidad_medidas,id',
                'descripcion' => 'nullable|string|max:255',
                'cantidad' => 'sometimes|required|numeric|min:1',
                'fecha_cosecha' => 'sometimes|required|date',
                'fecha_expiracion' => 'nullable|date|after_or_equal:fecha_cosecha',
                'estado' => 'sometimes|required|string|max:255'
            ], [
                'id_terreno.exists' => 'El terreno especificado no existe.',
                'id_temporada.exists' => 'La temporada especificada no existe.',
                'id_producto.exists' => 'El producto especificado no existe.',
                'id_unidadmedida.exists' => 'La unidad de medida especificada no existe.'
            ]);

            $produccion->update($request->all());
            return response()->json($produccion, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producción no encontrada'], 404);
        }
    }

    // Eliminar una producción
    public function destroy($id)
    {
        try {
            $produccion = Produccion::findOrFail($id);
            $produccion->delete();
            return response()->json(['message' => 'Producción eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producción no encontrada'], 404);
        }
    }

    // Función adicional: Obtener producciones activas (no expiradas)
    public function getProduccionesActivas()
    {
        $producciones = Produccion::where('fecha_expiracion', '>=', now())->where('estado', 'activo')->get();

        if ($producciones->isEmpty()) {
            return response()->json(['message' => 'No se encontraron producciones activas'], 404);
        }

        return response()->json($producciones, 200);
    }

    // Función adicional: Obtener producciones por terreno
    public function getProduccionesByTerreno($terrenoId)
    {
        $producciones = Produccion::where('id_terreno', $terrenoId)->get();

        if ($producciones->isEmpty()) {
            return response()->json(['message' => 'No se encontraron producciones para este terreno'], 404);
        }

        return response()->json($producciones, 200);
    }

    // Función adicional: Obtener producciones por temporada
    public function getProduccionesByTemporada($temporadaId)
    {
        $producciones = Produccion::where('id_temporada', $temporadaId)->get();

        if ($producciones->isEmpty()) {
            return response()->json(['message' => 'No se encontraron producciones para esta temporada'], 404);
        }

        return response()->json($producciones, 200);
    }

    // Función adicional: Obtener producciones por producto
    public function getProduccionesByProducto($productoId)
    {
        $producciones = Produccion::where('id_producto', $productoId)->get();

        if ($producciones->isEmpty()) {
            return response()->json(['message' => 'No se encontraron producciones para este producto'], 404);
        }

        return response()->json($producciones, 200);
    }
}
