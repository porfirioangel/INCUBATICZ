<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Integrante extends Model
{
    protected $table = 'integrante';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'pri_apellido',
        'seg_apellido',
        'email',
        'nacimiento',
        'nivel_estudio',
        'carrera',
        'universidad',
        'titulo_profesional',
        'constancia_estudios',
        'constancia_obligaciones',
        'ine',
        'curp',
        'protesta_verdad',
        'rfc',
        'carta_sat'
    ];
}
