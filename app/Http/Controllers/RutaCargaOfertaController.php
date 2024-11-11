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
        return response()->json(RutaCargaOferta::with('cargaOferta', 'rutaOferta', 'transporte')->get(), 200);
    }

    // Crear una nueva carga de ruta de oferta
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_carga_oferta' => 'required|exists:carga_ofertas,id',
                'id_ruta_oferta' => 'required|exists:ruta_ofertas,id',
                'orden' => 'sometimes|required|integer|min:1',
                'estado' => 'required|string|max:255',
                'distancia' => 'sometimes|required|numeric|min:0'
            ]);

            // Obtener los puntos de recogida (latitud y longitud) de cada carga
            $cargas = CargaOferta::with('rutaCargaOferta')
                        ->where('id_ruta_oferta', $request->id_ruta_oferta)
                        ->get(['ubicacion_latitud', 'ubicacion_longitud']);

            $puntos = $cargas->map(function ($carga) {
                return [
                    'lat' => $carga->ubicacion_latitud,
                    'lon' => $carga->ubicacion_longitud
                ];
            })->toArray();

            // Asignar transporte basado en cercanía al primer punto
            $transporteId = $this->assignTransporteToClosestPoint($puntos[0]['lat'], $puntos[0]['lon']);

            if (!$transporteId) {
                return response()->json(['message' => 'No hay transporte disponible con capacidad suficiente.'], 422);
            }

            // Optimizar el orden de la ruta de recogida
            $optimizedRoute = $this->optimizeRouteOrder($puntos, $puntos[0]);

            // Crear la entrada de ruta y registrar la ruta optimizada
            foreach ($optimizedRoute as $index => $point) {
                RutaCargaOferta::create([
                    'id_carga_oferta' => $request->id_carga_oferta,
                    'id_ruta_oferta' => $request->id_ruta_oferta,
                    'id_transporte' => $transporteId,
                    'orden' => $index + 1,
                    'estado' => $request->estado,
                    'distancia' => $this->calculateDistance($point['lat'], $point['lon'], $puntos[0]['lat'], $puntos[0]['lon'])
                ]);
            }

            return response()->json(['message' => 'Ruta optimizada creada correctamente'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una carga de ruta de oferta específica
    public function show($id)
    {
        try {
            $rutaCargaOferta = RutaCargaOferta::with('cargaOferta', 'rutaOferta', 'transporte')->findOrFail($id);
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
                'id_transporte' => 'sometimes|exists:transportes,id',
                'orden' => 'sometimes|required|integer|min:1',
                'estado' => 'sometimes|required|string|max:255',
                'distancia' => 'sometimes|required|numeric|min:0'
            ]);

            $rutaCargaOferta->update($request->all());
            return response()->json($rutaCargaOferta, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
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

    // Función para asignar automáticamente el transporte más cercano con capacidad suficiente
    private function assignTransporteToClosestPoint($lat, $lon)
    {
        $transporte = Transporte::with('conductor')
            ->where('capacidad', '>=', CargaOferta::sum('pesokg'))
            ->where('estado', 'disponible')
            ->get()
            ->map(function ($transporte) use ($lat, $lon) {
                $conductor = $transporte->conductor;
                $distance = $this->calculateDistance($lat, $lon, $conductor->ubicacion_latitud, $conductor->ubicacion_longitud);
                return ['id' => $transporte->id, 'distance' => $distance];
            })
            ->sortBy('distance')
            ->first();

        return $transporte ? $transporte['id'] : null;
    }

    // Algoritmo de Nearest Neighbor para optimizar el orden de la ruta
    private function optimizeRouteOrder($points, $startPoint)
    {
        $optimizedRoute = [$startPoint];
        $remainingPoints = $points;

        while (!empty($remainingPoints)) {
            $nearestPoint = null;
            $shortestDistance = PHP_INT_MAX;

            foreach ($remainingPoints as $key => $point) {
                $distance = $this->calculateDistance(
                    $optimizedRoute[count($optimizedRoute) - 1]['lat'],
                    $optimizedRoute[count($optimizedRoute) - 1]['lon'],
                    $point['lat'],
                    $point['lon']
                );

                if ($distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $nearestPoint = $point;
                    $nearestKey = $key;
                }
            }

            $optimizedRoute[] = $nearestPoint;
            unset($remainingPoints[$nearestKey]);
        }

        return $optimizedRoute;
    }

    // Función para calcular la distancia entre dos puntos (Haversine)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radio de la tierra en kilómetros
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
