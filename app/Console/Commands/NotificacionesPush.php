<?php

namespace App\Console\Commands;

use App\Helpers\Utils;
use Illuminate\Console\Command;

class NotificacionesPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enviar:noti';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {
           // $deviceToken = 'eq2vvfV6Q-qANxNPZiwcJH:APA91bHBUFtmUActqOO8o6xzF-AXLTUQ_Yg_wISm0cBWkKqZyLzCPsVYUrxBlhh0N1XdQo5GxAZOHk-iy7BslHKU0-UZDVeq0iMoiLHBcQXuZizkyFA2tNU';
            $deviceToken = 'exbrpqSHSqGhL-EE49XNVi:APA91bGuflVTxExQxTJC4dwhlIJq3wylDAn8CIKuYnaBvTvyqBy0254985IEwC1N1E13t6arln2YVDQHgO0dp-awdTncY0LY11svkR8ZrApVRkeZKgtmpL8';

            
            $title = 'Notificación de prueba';
            $body = 'Este es el cuerpo de la notificación';
            $locations = [
                ['lat' => -17.888710427161428, 'lon' => -63.2807762900374],


                ['lat' =>    -17.864040992165666, 'lon' =>  -63.26532676557653],


                ['lat' => -17.813220975272536, 'lon' => -63.20867850922002]
            ];
            $data = [
                'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
            ];

           /*  $data = [
               "screen" => "CargaOfertaScreen",
            ];
 */


            // Llamada al helper para enviar la notificación
            $response = Utils::sendFcmNotificationWithLocations($deviceToken, $title, $body, $data, 2);

            // Mostrar éxito en la consola
            $this->info('Notificación enviada con éxito: ' . json_encode($response));

            return 0; // Código de salida para éxito
        } catch (\Exception $e) {
            // Manejar errores y mostrar mensaje en la consola
            $this->error('Error al enviar la notificación: ' . $e->getMessage());

            return 1; // Código de salida para error
        }
    }
}
