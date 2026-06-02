<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['titulo', 'salario_id', 'categoria_id', 'empresa', 'ultimo_dia', 'descripcion', 'imagen', 'user_id'])]
class Vacante extends Model
{
    protected $casts = ['ultimo_dia' => 'date'];

    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }

    public function salario() {
        return $this->belongsTo(Salario::class);
    }

    public function candidatos() {
        return $this->hasMany(Candidato::class);
    }

    public function reclutador() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
