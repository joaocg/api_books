<?php

namespace Tests\Feature;

use App\Models\Livro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateLivroTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_book_with_indices()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $livroData = [
            'titulo' => 'Meu Livro com PhpUnit',
            'indices' => [
                [
                    'titulo' => 'Capítulo 1',
                    'pagina' => 1,
                    'subindices' => [
                        [
                            'titulo' => 'Seção 1.1',
                            'pagina' => 2,
                        ],
                        [
                            'titulo' => 'Seção 1.2',
                            'pagina' => 3,
                        ],
                    ],
                ],
                [
                    'titulo' => 'Capítulo 2',
                    'pagina' => 4,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/livros', $livroData);
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Livro cadastrado com sucesso',
        ]);

        $this->assertDatabaseHas('livros', [
            'titulo' => 'Meu Livro com PhpUnit',
            'usuario_publicador_id' => $user->id,
        ]);

        foreach ($livroData['indices'] as $indiceData) {
            $this->assertDatabaseHas('indices', [
                'titulo' => $indiceData['titulo'],
                'pagina' => $indiceData['pagina'],
                'livro_id' => Livro::where('titulo', 'Meu Livro com PhpUnit')->first()->id,
            ]);

            if (isset($indiceData['subindices'])) {
                foreach ($indiceData['subindices'] as $subindiceData) {
                    $this->assertDatabaseHas('indices', [
                        'titulo' => $subindiceData['titulo'],
                        'pagina' => $subindiceData['pagina'],
                        'livro_id' => Livro::where('titulo', 'Meu Livro com PhpUnit')->first()->id,
                    ]);
                }
            }
        }
    }
}
