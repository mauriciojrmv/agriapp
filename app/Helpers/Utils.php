<?php

namespace App\Helpers;

use App\Models\OfertaDetalle;
use Illuminate\Support\Collection;

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
}
