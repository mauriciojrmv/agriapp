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
            $deviceToken = 'd9DDEyr4T_unqGNlo-5BB-:APA91bE1QTpbGgqItZ0DLgk7qYkVeAwv-MSqDgwN5SZHCGIw7uQWVwW-WV1ygO8R3UKz8Bl5bntRl2sQvRoTiJB68tp8as4ZbPrwN-F80ozch8yM2lOfkvc';
            $title = 'Notificación de prueba';
            $body = 'Este es el cuerpo de la notificación';
            $locations = [['lat' => -123.45, 'lon' => -678.90]];

            // Llamada al helper para enviar la notificación
            $response = Utils::sendFcmNotificationWithLocations($deviceToken, $title, $body, $locations);

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
