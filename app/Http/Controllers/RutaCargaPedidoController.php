<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RutaCargaPedido;
use App\Models\CargaPedido;
use App\Models\RutaPedido;
use App\Models\Transporte;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RutaCargaPedidoController extends Controller
{
    // Obtener todas las cargas asociadas a rutas de pedido
    public function index()
    {
        return response()->json(RutaCargaPedido::with('cargaPedido', 'rutaPedido', 'transporte')->get(), 200);
    }

    // Crear una nueva carga de ruta de pedido
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_carga_pedido' => 'required|exists:carga_pedidos,id',
                'id_ruta_pedido' => 'required|exists:ruta_pedidos,id',
                'orden' => 'sometimes|required|integer|min:1',
                'estado' => 'required|string|max:255',
                'distancia' => 'sometimes|required|numeric|min:0'
            ]);

            // Obtener la carga de pedido y sus coordenadas de Pedido a través de PedidoDetalle
            $cargaPedido = CargaPedido::with('pedidoDetalle.pedido')->findOrFail($request->id_carga_pedido);

            // Acceder a las coordenadas de Pedido
            $latitud = $cargaPedido->pedidoDetalle->pedido->ubicacion_latitud;
            $longitud = $cargaPedido->pedidoDetalle->pedido->ubicacion_longitud;

            // Asignar transporte basado en cercanía al primer punto de entrega
            $transporteId = $this->assignTransporteToClosestPoint($latitud, $longitud);

            if (!$transporteId) {
                return response()->json(['message' => 'No hay transporte disponible con capacidad suficiente.'], 422);
            }

            // Optimizar el orden de la ruta de entrega (Aquí podrías usar un algoritmo de optimización)
            $optimizedRoute = $this->optimizeRouteOrder([['lat' => $latitud, 'lon' => $longitud]], ['lat' => $latitud, 'lon' => $longitud]);

            // Crear la entrada de ruta y registrar la ruta optimizada
            foreach ($optimizedRoute as $index => $point) {
                RutaCargaPedido::create([
                    'id_carga_pedido' => $request->id_carga_pedido,
                    'id_ruta_pedido' => $request->id_ruta_pedido,
                    'id_transporte' => $transporteId,
                    'orden' => $index + 1,
                    'estado' => $request->estado,
                    'distancia' => $this->calculateDistance($point['lat'], $point['lon'], $latitud, $longitud)
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

    // Mostrar detalles de una carga de ruta de pedido específica
    public function show($id)
    {
        try {
            $rutaCargaPedido = RutaCargaPedido::with('cargaPedido', 'rutaPedido', 'transporte')->findOrFail($id);
            return response()->json($rutaCargaPedido, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de pedido no encontrada'], 404);
        }
    }

    // Actualizar datos de una carga de ruta de pedido
    public function update(Request $request, $id)
    {
        try {
            $rutaCargaPedido = RutaCargaPedido::findOrFail($id);

            $request->validate([
                'id_carga_pedido' => 'sometimes|required|exists:carga_pedidos,id',
                'id_ruta_pedido' => 'sometimes|required|exists:ruta_pedidos,id',
                'id_transporte' => 'sometimes|exists:transportes,id',
                'orden' => 'sometimes|required|integer|min:1',
                'estado' => 'sometimes|required|string|max:255',
                'distancia' => 'sometimes|required|numeric|min:0'
            ]);

            $rutaCargaPedido->update($request->all());
            return response()->json($rutaCargaPedido, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de pedido no encontrada'], 404);
        }
    }

    // Eliminar una carga de ruta de pedido
    public function destroy($id)
    {
        try {
            $rutaCargaPedido = RutaCargaPedido::findOrFail($id);
            $rutaCargaPedido->delete();
            return response()->json(['message' => 'Carga de ruta de pedido eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de pedido no encontrada'], 404);
        }
    }

    // Función para asignar automáticamente el transporte más cercano con capacidad suficiente
    private function assignTransporteToClosestPoint($lat, $lon)
    {
        $transporte = Transporte::with('conductor')
            ->where('capacidad', '>=', CargaPedido::sum('cantidad'))
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
