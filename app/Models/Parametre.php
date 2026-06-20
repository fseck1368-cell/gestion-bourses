<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $fillable = ['cle', 'valeur', 'type', 'groupe', 'label', 'description'];

    public static function get(string $cle, $default = null)
    {
        $param = self::where('cle', $cle)->first();
        return $param ? $param->valeur : $default;
    }

    public static function set(string $cle, $valeur): void
    {
        self::where('cle', $cle)->update(['valeur' => $valeur]);
    }
}
