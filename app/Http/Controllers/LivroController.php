<?php

namespace App\Http\Controllers;

use App\Models\Indice;
use Illuminate\Http\Request;
use App\Models\Livro;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use XMLReader;

class LivroController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listarTodos(Request $request): JsonResponse
    {
        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'titulo_do_indice' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1'
        ]);

        $tituloIndice = $request->get('titulo_do_indice');

        $livros = null;
        if(!empty($tituloIndice)){
            $livros = Livro::with(['usuario_publicador', 'indices' => function ($query) use ($tituloIndice) {
                $query->whereNull('indice_pai_id')
                    ->where('titulo', 'like', "%{$tituloIndice}%")
                    ->with(['subindices' => function ($query) use ($tituloIndice) {
                        $query->whereNotNull('indice_pai_id')
                            ->orWhere('titulo', 'like', "%{$tituloIndice}%")
                            ->with(['subindices']);
                    }]);
            }])->get();
        } else{
            $livros = Livro::with(['usuario_publicador', 'indices', 'indices.subindices'])->get();
        }

        return response()->json([
            'data' => $livros
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $dados = json_decode($request->getContent(), true);

        try {
            $validator = Validator::make($dados, [
                'titulo' => 'required|string|max:255',
                'indices' => 'required|array',
                'indices.*.titulo' => 'required|string|max:255',
                'indices.*.pagina' => 'required|integer',
                'indices.*.subindices' => 'nullable|array',
                'indices.*.subindices.*.titulo' => 'required|string|max:255',
                'indices.*.subindices.*.pagina' => 'required|integer',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $livro = Livro::create(['titulo' => $dados['titulo'], 'usuario_publicador_id' => $request->user()->id]);

            foreach ($dados['indices'] as $indiceData) {
                $indice = Indice::create([
                    'livro_id' => $livro->id,
                    'titulo' => $indiceData['titulo'],
                    'pagina' => $indiceData['pagina'],
                ]);

                if (isset($indiceData['subindices'])) {
                    foreach ($indiceData['subindices'] as $subindiceData) {
                        Indice::create([
                            'livro_id' => $livro->id,
                            'indice_pai_id' => $indice->id,
                            'titulo' => $subindiceData['titulo'],
                            'pagina' => $subindiceData['pagina'],
                        ]);
                    }
                }
            }

            return response()->json(['message' => 'Livro cadastrado com sucesso'], 201);

        } catch (ValidationException $e) {

            return response()->json($e->errors(), 400);

        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function importarIndices(Request $request, int $id): JsonResponse
    {
        $xml = $request->file('xml');

        try {
            $request->validate([
                'xml' => 'required|file',
            ]);

            $reader = new XMLReader;
            $reader->open($xml);

            while ($reader->read() !== FALSE) {
                $this->formatarXml($reader, $indices);
            }

            $reader->close();

            $this->salvarImportacaoXml($indices, $id);

            return response()->json(['message' => 'Livros importados com sucesso'], 201);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), 400);
        }
    }

    /**
     * @param array|null $indices
     * @param int $livro_id
     * @param int|null $parent_id
     * @return void
     */
    private function salvarImportacaoXml(array $indices = null, int $livro_id, int $parent_id = null): void
    {
        foreach ($indices as $index) {
            $indice = Indice::create([
                'livro_id' => $livro_id,
                'indice_pai_id' => $parent_id,
                'titulo' => $index['titulo'],
                'pagina' => $index['pagina'],
            ]);

            if (!empty($index['subindices'])) {
                $this->salvarImportacaoXml($index['subindices'], $livro_id, $indice->id);
            }
        }

    }

    /**
     * @param XMLReader $reader
     * @param array|null $indices
     * @param int|null $parentIndex
     * @return void
     */
    private function formatarXml(XMLReader $reader, array &$indices = null, int $parentIndex = null): void
    {
        while ($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'item') {
                $pagina = $reader->getAttribute('pagina');
                $titulo = $reader->getAttribute('titulo');
                $index = [
                    'pagina' => $pagina,
                    'titulo' => $titulo,
                    'subindices' => []
                ];

                if ($parentIndex !== null) {
                    $indices[$parentIndex]['subindices'][] = $index;
                    $currentIndex = count($indices[$parentIndex]['subindices']) - 1;
                    $this->formatarXml($reader, $indices[$parentIndex]['subindices'], $currentIndex);
                } else {
                    $indices[] = $index;
                    $currentIndex = count($indices) - 1;
                    $this->formatarXml($reader, $indices, $currentIndex);
                }
            } elseif ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'item') {
                return;
            }
        }
    }

}
