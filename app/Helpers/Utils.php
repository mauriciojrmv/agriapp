<?php

namespace App\Helpers;

use App\Models\OfertaDetalle;
use Firebase\JWT\JWT;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Utils
{
    /**
     * Filtra los detalles de las ofertas por un producto específico.
     *
     * @param Collection $detallesOfertas
     * @param int $idProducto
     * @return Collection
     */
    public static function getDetallesFiltrados(Collection $detallesOfertas, int $idProducto): Collection
    {
        return $detallesOfertas->filter(function ($detalle) use ($idProducto) {
            return $detalle->produccion->id_producto == $idProducto;
        });
    }

    public static function getDetallesOfertas(string $fechaInicio, string $fechaFin): Collection
    {
        return OfertaDetalle::whereHas('oferta', function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_expiracion', [$fechaInicio, $fechaFin])
                ->orWhere('fecha_expiracion', '>', $fechaFin);
        })->where('estado', 'activo')->get();
    }

    public static function getDetalleOfertaConMenorPrecio(Collection $detallesOfertas)
    {
        return $detallesOfertas->sortBy(function ($detalle) {
            // Evitar división por cero y calcular el precio por cantidad
            return $detalle->cantidad_fisico > 0 ? $detalle->precio / $detalle->cantidad_fisico : PHP_INT_MAX;
        })->first();
    }

    public static function getDetalleOfertaQueCumpleConLaCantidad(Collection $detallesOfertas, int $cantidadRequerida)
    {
        return  $detallesOfertas->filter(function ($detalle) use ($cantidadRequerida) {
            return $detalle->cantidad_fisico >= $cantidadRequerida and ($detalle->cantidad_comprometido + $cantidadRequerida) <= $detalle->cantidad_fisico;
        });
    }

    public static function getDetalleOfertaMenorPxU(Collection $detallesOfertas)
    {
        return $detallesOfertas->sortBy(function ($detalle) {
            return $detalle->precio / $detalle->cantidad_fisico;
        })->first();
    }

    public static function getDetallesSatisfacenCantidad(Collection $detallesOfertasFiltrados, int $cantidadRequerida): Collection
    {
        $cantidadAcumulada = 0;

        return $detallesOfertasFiltrados->filter(function ($detalle) use (&$cantidadAcumulada, $cantidadRequerida) {
            // Calcular la cantidad disponible para este detalle
            $cantidadDisponible = $detalle->cantidad_fisico - $detalle->cantidad_comprometido;
            if ($cantidadDisponible == 0) return false;
            // Verificar si aún necesitamos acumular más cantidad
            if ($cantidadAcumulada <= $cantidadRequerida) {
                $cantidadAcumulada += $cantidadDisponible;

                // Incluir el detalle en los resultados
                return true;
            }

            // Si ya alcanzamos la cantidad requerida, no incluir más detalles
            return false;
        });
    }

    public static function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radio de la Tierra en kilómetros

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distancia en kilómetros
    }


    public static function buscarCargaCercana($cargas, $radio, $latPos, $lonPos)
    {
        $closestCarga = null;
        $shortestDistance = PHP_INT_MAX; // Inicializar con un valor alto
        if ($cargas->isEmpty()) {
            return null; // No hay cargas para buscar
        }

        foreach ($cargas as $carga) {
            // Extraer latitud y longitud de la carga desde las relaciones
            $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
            $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;

            // Calcular la distancia usando la fórmula de Haversine
            $distance = self::haversine($latPos, $lonPos, $latCarga, $lonCarga);

            // Comparar si es la distancia más corta
            if ($distance <= $radio) {
                $shortestDistance = $distance;
                $closestCarga = $carga; // Guardar solo la carga más cercana
            }
        }

        return $closestCarga; // Devolver la carga más cercana
    }

    public static function getCargasRuta($cargas, $radio, $lat_inicial, $lon_inicial): Collection
    {

        return $cargas->filter(function ($carga) use ($radio, $lat_inicial, $lon_inicial) {
            $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
            $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;
            $distance = self::haversine($lat_inicial, $lon_inicial, $latCarga, $lonCarga);
            // echo "Posicion de la carga: " . $latCarga . " " . $lonCarga, PHP_EOL;

            if ($distance <= $radio) {

                return $carga;
            }
        });
    }

    public static function getCargasMismaIdOfertaDetalle(Collection $cargas, int $idOfertaDetalle): Collection
    {
        return $cargas->filter(function ($carga) use ($idOfertaDetalle) {
            return $carga->id_oferta_detalle == $idOfertaDetalle;
        });
    }

    public static function getSigCargaMasCercana($cargas, $latPos, $lonPos, $idOfertaDetalle)
    {
        $closestCarga = null;
        $shortestDistance = PHP_INT_MAX; // Inicializar con un valor alto

        foreach ($cargas as $carga) {
            if ($carga->id_oferta_detalle != $idOfertaDetalle) {
                // Extraer latitud y longitud de la carga desde las relaciones
                $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
                $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;

                // Calcular la distancia usando la fórmula de Haversine
                $distance = self::haversine($latPos, $lonPos, $latCarga, $lonCarga);

                // Comparar si es la distancia más corta
                if ($distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $closestCarga = $carga; // Guardar solo la carga más cercana
                }
            }
        }

        return $closestCarga; // Devolver la carga más cercana
    }


    public static function getCargasSatisfacenAltransporte(Collection $cargas, int $cantidadRequerida): Collection
    {
        $cantidadAcumulada = 0;

        return $cargas->filter(function ($carga) use (&$cantidadAcumulada, $cantidadRequerida) {
            // Calcular la cantidad disponible para este detalle
            $cargaKg = $carga->pesokg;
            $mas10 = $cantidadRequerida + ($cantidadRequerida * 10 / 100);
            $menos10 = $cantidadRequerida - ($cantidadRequerida * 10 / 100);
            $sum = $cargaKg + $cantidadAcumulada;

            // Verificar si aún necesitamos acumular más cantidad
            if ($cantidadAcumulada <= $cantidadRequerida && $sum <= $cantidadRequerida) {
                $cantidadAcumulada += $cargaKg;


                return true;
            }

            // Si ya alcanzamos la cantidad requerida, no incluir más detalles
            return false;
        });
    }

    public static function getCargaSatisfacenAltransporteC(Collection $cargas, float $cantidadRequerida): Collection
    {

        return $cargas->filter(function ($carga) use ($cantidadRequerida) {
            $cargaKg = $carga->pesokg;
            $mas10 = $cantidadRequerida + ($cantidadRequerida * 10 / 100);
            $menos10 = $cantidadRequerida - ($cantidadRequerida * 10 / 100);



            if ($cargaKg >= $menos10 && $cargaKg <= $mas10) {


                return true;
            }

            return false;
        });
    }


    public static function getBearerToken($tipo)
    {
        $serviceAccountPath = "";
        if ($tipo == 1) {
            $serviceAccountPath = storage_path('app/firebase/productor.json');
        } else {
            $serviceAccountPath = storage_path('app/firebase/conductor.json');
        }
        // Leer el archivo JSON
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

        $now = time();
        $payload = [
            'iss' => $serviceAccount['client_email'], // Emisor del token
            'sub' => $serviceAccount['client_email'], // Sujeto del token
            'aud' => $serviceAccount['token_uri'],    // URL de autorización
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        // Generar el JWT
        $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

        // Solicitar el Bearer Token
        $response = Http::asForm()->post($serviceAccount['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }


        throw new \Exception('Error al obtener el Bearer Token: ' . $response->body());
    }


    public  static function  sendFcmNotificationWithLocations($deviceToken, $title, $body, $data, $tipo)
    {
        $bearerToken = "";
        $proyectId = "";
        if ($tipo == 1) {
            $bearerToken = self::getBearerToken(1); // Genera el token dinámicamente
            $proyectId = "app-agricultor-c216e";
        } else {
            $bearerToken = self::getBearerToken(2); // Genera el token dinámicamente
            $proyectId = "conductor-app-daf91";
        }


        $url = 'https://fcm.googleapis.com/v1/projects/' . $proyectId . '/messages:send';

        // Estructura del mensaje
        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data
            ],
        ];

        // Enviar solicitud a Firebase
        $response = Http::withToken($bearerToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Error al enviar la notificación: ' . $response->body());
    }
}
