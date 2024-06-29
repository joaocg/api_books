<?php

namespace App\Models;

use App\Models\Indice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Livro extends Model
{
    use HasFactory;

    protected $table = 'livros';

    protected $fillable = [
        'titulo',
        'usuario_publicador_id'
    ];

    protected $hidden = [
        'usuario_publicador_id',
        'created_at',
        'updated_at'
    ];
    public function indices()
    {
        return $this->hasMany(Indice::class);
    }

    public function usuario_publicador()
    {
        return $this->belongsTo(User::class);
    }
}
