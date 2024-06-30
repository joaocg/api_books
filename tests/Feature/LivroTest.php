<?php

namespace Tests\Feature;

use App\Models\Livro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LivroTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_all_books()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        Livro::factory()->count(5)->create(['usuario_publicador_id' => $user->id]);

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

        $user = User::factory()->create();
        Passport::actingAs($user);
        $livros = Livro::factory()->count(3)->create(['usuario_publicador_id' => $user->id]);

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
