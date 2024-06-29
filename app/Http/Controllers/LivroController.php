<?php

namespace App\Http\Controllers;

use App\Models\Indice;
use Illuminate\Http\Request;
use App\Models\Livro;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
        $livros = Livro::with(['usuario_publicador', 'indices' => function ($query) use ($tituloIndice) {
            $query->whereNull('indice_pai_id')
                ->where('titulo', 'like', "%{$tituloIndice}%")
                ->with(['subindices' => function ($query) use ($tituloIndice) {
                    $query->whereNotNull('indice_pai_id')
                        ->orWhere('titulo', 'like', "%{$tituloIndice}%")
                        ->with(['subindices']);
                }]);
        }])->get();


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

    public function show($id)
    {
        $livro = Livro::find($id);
        if (!$livro) {
            return response()->json(['message' => 'Livro não encontrado'], 404);
        }
        return response()->json($livro);
    }

    public function update(Request $request, $id)
    {
        $livro = Livro::find($id);
        if (!$livro) {
            return response()->json(['message' => 'Livro não encontrado'], 404);
        }
        $livro->update($request->all());
        return response()->json($livro);
    }

    public function destroy($id)
    {
        $livro = Livro::find($id);
        if (!$livro) {
            return response()->json(['message' => 'Livro não encontrado'], 404);
        }
        $livro->delete();
        return response()->json(['message' => 'Livro deletado com sucesso']);
    }


}
