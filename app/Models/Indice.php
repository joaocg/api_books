<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indice extends Model
{
    use HasFactory;

    protected $table = 'indices';

    protected $fillable = [
        'titulo',
        'livro_id',
        'pagina'
    ];

    protected $hidden = [
        'livro_id',
        'indice_pai_id',
        'created_at',
        'updated_at'
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function indicePai()
    {
        return $this->belongsTo(Indice::class, 'indice_pai_id');
    }

    public function subindices()
    {
        return $this->hasMany(Indice::class, 'indice_pai_id');
    }
}
