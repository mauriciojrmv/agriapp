<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\CargaOferta;

class RecogidaConfirmada extends Notification
{
    use Queueable;

    protected $cargaOferta;
    protected $producto;

    public function __construct(CargaOferta $cargaOferta)
    {
        $this->cargaOferta = $cargaOferta;
        $this->producto = $cargaOferta->ofertaDetalle->producto;
    }

    /**
     * Define los canales de la notificación.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Usamos el canal de base de datos
    }

    /**
     * Definir los datos de la notificación que se almacenarán en la base de datos.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Confirmación de Recogida',
            'message' => 'Se ha recogido exitosamente el producto ' . $this->producto->nombre . ' (Cantidad: ' . $this->cargaOferta->cantidad . ')',
            'carga_oferta_id' => $this->cargaOferta->id,
            'producto_id' => $this->producto->id,
        ];
    }
}
