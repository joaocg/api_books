<?php

namespace Tests\Feature;

use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;

class ImportIndicesTest extends TestCase
{
    use RefreshDatabase;

    protected $livro;

    protected function setUp(): void
    {
        parent::setUp();
        $this->livro = Livro::factory()->create(['usuario_publicador_id' => $this->user->id]);
    }

    /** @test */
    public function it_imports_indices_from_xml()
    {
        /**
         * Simulando um arquivo de teste
         */
        $xmlContent = '<?xml version="1.0" encoding="ISO-8859-1"?>
        <indice>
            <item pagina="1" titulo="Seção 1">
                <item pagina="1" titulo="Seção 1.1">
                    <item pagina="1" titulo="Seção 1.1.1"/>
                    <item pagina="1" titulo="Seção 1.1.2"/>
                </item>
                <item pagina="2" titulo="Seção 1.2"/>
            </item>
            <item pagina="2" titulo="Seção 2"/>
            <item pagina="2" titulo="Seção 3"/>
        </indice>';

        Storage::fake('test');

        $xmlFilePath = 'indices.xml';
        Storage::disk('test')->put($xmlFilePath, $xmlContent);

        $response = $this->postJson('/api/v1/livros/' . $this->livro->id . '/importar-indices-xml', [
            'xml' => new UploadedFile(
                Storage::disk('test')->path($xmlFilePath),
                $xmlFilePath,
                'application/xml',
                null,
                true
            ),
        ]);
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Livros importados com sucesso',
        ]);
    }

}
