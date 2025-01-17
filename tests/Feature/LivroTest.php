<?php

namespace Tests\Feature;

use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivroTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_all_books()
    {

        Livro::factory()->count(5)->create(['usuario_publicador_id' => $this->user->id]);

        $response = $this->getJson('/api/v1/livros');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'titulo',
                    'usuario_publicador',
                    'indices' => [
                        '*' => [
                            'id',
                            'titulo',
                            'subindices' => [
                                '*' => [
                                    'id',
                                    'titulo'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertCount(5, $response->json('data'));
    }

    /** @test */
    public function it_lists_books_filtered_by_titulo_do_indice()
    {

        $livros = Livro::factory()->count(3)->create(['usuario_publicador_id' => $this->user->id]);

        foreach ($livros as $livro) {
            $livro->indices()->create([
                'titulo' => 'Capítulo 1',
                'pagina' => '10'
            ]);
        }

        $response = $this->getJson('/api/v1/livros?titulo_do_indice=Capítulo 1');

        $response->assertStatus(200);

        $this->assertCount(3, $response->json('data'));
    }
}
