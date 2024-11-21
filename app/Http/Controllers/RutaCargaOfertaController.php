<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RutaCargaOferta;
use App\Models\CargaOferta;
use App\Models\RutaOferta;
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
            'transporte.conductor',
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
                'distancia' => 'required|numeric|min:0',
            ]);

            $rutaCargaOferta = RutaCargaOferta::create($request->all());

            return response()->json($rutaCargaOferta, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear la ruta de carga de oferta.', 'error' => $e->getMessage()], 500);
        }
    }

    // Mostrar detalles de una carga de ruta de oferta específica
    public function show($id)
    {
        try {
            $rutaCargaOferta = RutaCargaOferta::with([
                'cargaOferta.ofertaDetalle.produccion.producto',
                'rutaOferta',
                'transporte.conductor',
            ])->findOrFail($id);

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
                'distancia' => 'sometimes|required|numeric|min:0',
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

    // Método para que el conductor acepte o rechace una carga
/*    public function updateEstadoConductor(Request $request, $id)
{
    try {
        $request->validate([
            'estado_conductor' => 'required|in:aceptado,rechazado',
        ], [
            'estado_conductor.required' => 'El campo estado_conductor es obligatorio.',
            'estado_conductor.in' => 'El estado_conductor debe ser "aceptado" o "rechazado".',
        ]);

        // Buscar la RutaCargaOferta por ID
        $rutaCargaOferta = RutaCargaOferta::findOrFail($id);

        // Actualizar el estado del conductor en RutaCargaOferta
        $rutaCargaOferta->update(['estado_conductor' => $request->estado_conductor]);

        // Si el estado es "aceptado", actualizar el estado de RutaCargaOferta y CargaOferta
        if ($request->estado_conductor === 'aceptado') {
            $rutaCargaOferta->update(['estado' => 'en_proceso']); // Cambiar el estado de RutaCargaOferta

            $cargaOferta = CargaOferta::findOrFail($rutaCargaOferta->id_carga_oferta);
            $cargaOferta->update(['estado' => 'asignado']); // Cambiar el estado de CargaOferta
        }

        return response()->json([
            'message' => 'Estado del conductor y carga actualizados correctamente.',
            'ruta_carga_oferta' => $rutaCargaOferta,
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'RutaCargaOferta o CargaOferta no encontrada'], 404);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al actualizar el estado.', 'error' => $e->getMessage()], 500);
    }
} */

public function aceptarRuta(Request $request, $idRutaOferta)
{
    try {
        // Validar la entrada
        $request->validate([
            'id_conductor' => 'required|exists:conductors,id',
        ]);

        // Obtener la ruta de oferta
        $rutaOferta = RutaOferta::findOrFail($idRutaOferta);

        // Validar que la ruta no esté ya en proceso
        if ($rutaOferta->estado === 'en_proceso') {
            return response()->json(['message' => 'La ruta ya está en proceso.'], 422);
        }

        // Actualizar el estado de la RutaOferta
        $rutaOferta->update(['estado' => 'en_proceso']);

        // Obtener las cargas asociadas y generar puntos
        $cargas = RutaCargaOferta::where('id_ruta_oferta', $idRutaOferta)->with('cargaOferta')->get();

        $orden = 1;
        foreach ($cargas as $carga) {
            // Actualizar el estado de la carga
            $carga->cargaOferta->update(['estado' => 'asignado']);

            // Guardar los puntos automáticamente
            $lat = $carga->cargaOferta->ofertaDetalle->produccion->terreno->ubicacion_latitud;
            $lon = $carga->cargaOferta->ofertaDetalle->produccion->terreno->ubicacion_longitud;

            $carga->update([
                'orden' => $orden,
                'distancia' => 0, // Aquí puedes calcular distancias si es necesario
            ]);

            $orden++;
        }

        return response()->json([
            'message' => 'Ruta aceptada y puntos generados correctamente.',
            'ruta_oferta' => $rutaOferta,
            'cargas' => $cargas,
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Ruta no encontrada.'], 404);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al aceptar la ruta.', 'error' => $e->getMessage()], 500);
    }
}


public function confirmarRecogida(Request $request, $id)
{
    try {
        $rutaCargaOferta = RutaCargaOferta::findOrFail($id);

        // Validar el estado de la ruta antes de continuar
        if ($rutaCargaOferta->estado !== 'en_proceso') {
            return response()->json([
                'message' => 'La carga no está en estado de proceso para ser confirmada.'
            ], 422);
        }

        // Obtener la carga asociada
        $cargaOferta = CargaOferta::findOrFail($rutaCargaOferta->id_carga_oferta);

        // Actualizar el estado de la carga a 'finalizado'
        $cargaOferta->update(['estado' => 'finalizado']);

        // Confirmar recogida y mantener estado de RutaCargaOferta
        $rutaCargaOferta->update(['estado' => 'en_proceso']);

        return response()->json([
            'message' => 'Recogida de la carga confirmada exitosamente.',
            'rutaCargaOferta' => $rutaCargaOferta,
            'cargaOferta' => $cargaOferta,
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'RutaCargaOferta o CargaOferta no encontrada.'], 404);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al confirmar la recogida.', 'error' => $e->getMessage()], 500);
    }
}

public function getPuntosRuta($idRutaOferta)
{
    try {
        // Recuperar la RutaOferta con sus RutaCargaOferta relacionadas
        $rutaOferta = RutaOferta::with(['rutaCargaOferta.cargaOferta.ofertaDetalle.produccion.producto', 'rutaCargaOferta.cargaOferta.ofertaDetalle.unidadMedida'])
            ->findOrFail($idRutaOferta);

        // Obtener los puntos de las cargas con información adicional
        $puntos = $rutaOferta->rutaCargaOferta->map(function ($rutaCarga) {
            $carga = $rutaCarga->cargaOferta;
            $ofertaDetalle = $carga->ofertaDetalle;
            $producto = $ofertaDetalle->produccion->producto;
            $unidadMedida = $ofertaDetalle->unidadMedida;

            return [
                'lat' => $ofertaDetalle->produccion->terreno->ubicacion_latitud,
                'lon' => $ofertaDetalle->produccion->terreno->ubicacion_longitud,
                'tipo' => 'carga',
                'id_carga_oferta' => $carga->id,
                'producto' => $producto->nombre,
                'cantidad' => $carga->pesokg, // Ajusta este campo según tu modelo
                'unidad' => $unidadMedida->nombre,
                'precio' => $ofertaDetalle->precio, // Opcional, si quieres incluir precio
            ];
        });

        // Agregar el punto de acopio
        $puntos->push([
            'lat' => -17.750000, // Latitud del punto de acopio
            'lon' => -63.100000, // Longitud del punto de acopio
            'tipo' => 'punto_acopio',
        ]);

        return response()->json(['puntos_ruta' => $puntos], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'RutaOferta no encontrada.'], 404);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al recuperar los puntos de la ruta.', 'error' => $e->getMessage()], 500);
    }
}



}
