<?php

namespace App\Models\Traits;

use Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid
{
    /**
     * Evento para pegar uuid
     *
     */
    public static function boot()
    {
        parent::boot(); // Sobrescrevendo methodo original
        /**
         *  Evento creantig ocorre antes do objeto ser criado
         *  Função de callback com objeto model para criar uuid
         */
        static::creating(function ($obj) {
            $obj->id = RamseyUuid::uuid4();
        });
    }
}
