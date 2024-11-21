<?php
// Función Haversine para calcular la distancia entre dos coordenadas
function haversine($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // Radio de la Tierra en kilómetros

    // Convertir de grados a radianes
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    // Fórmula de Haversine
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // Distancia en kilómetros
}

// Coordenadas de inicio y fin
$lat_inicio = -17.886823473795285;
$lon_inicio = -63.31518453854105;


$lat_fin =-17.823218337206924;
$lon_fin =   -63.18441131517269;



// Calcular el radio (distancia entre inicio y fin)
$radio = haversine($lat_inicio, $lon_inicio, $lat_fin, $lon_fin);
echo "Radio (distancia entre inicio y fin): " . $radio . " km\n";

// Calcular el centro (mitad de camino entre inicio y fin)
$lat_centro = ($lat_inicio + $lat_fin) / 2;
$lon_centro = ($lon_inicio + $lon_fin) / 2;
echo "Centro: lat = $lat_centro, lon = $lon_centro\n";

// Lista de puntos
$puntos = [

     ['lat' => -17.837329076676998, 'lon' =>  -63.237055946131335],
    // ['lat' => -17.79525725653522, 'lon' => -63.278300993973936],

    // ['lat' => -17.907272043535567, 'lon' =>  -63.34092502846955],

    //['lat' =>  -17.927242758893136, 'lon' =>  -63.34744544412592],

   
    ['lat' =>  -17.99893182155778,  'lon' => -63.38991209475155], // Este punto debería quedar fuera del rango
    // Más puntos...
];

$puntos_cercanos = [];

// Verificar cuáles puntos están dentro del radio
foreach ($puntos as $punto) {
    $distancia = haversine($lat_centro, $lon_centro, $punto['lat'], $punto['lon']);
    if ($distancia <= $radio) {
        $puntos_cercanos[] = $punto;
    }
}

// Mostrar los puntos que están dentro del radio
echo "Puntos cercanos:\n";
print_r($puntos_cercanos);
