<?php

use App\Models\Orgao;
use App\Models\Fornecedor;
use App\Models\Despesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// =============================================
// ÓRGÃOS
// =============================================

Route::get('/orgaos', function () {
    $orgaos = Orgao::all();
    return response()->json($orgaos);
});

Route::post('/orgaos', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255|unique:orgaos,name',
    ]);

    $orgao = Orgao::create($request->only('name'));

    return response()->json($orgao, 201);
});

Route::get('/orgaos/{id}', function (string $id) {
    $orgao = Orgao::find($id);

    if (!$orgao) {
        return response()->json(['message' => 'Órgão não encontrado'], 404);
    }

    return response()->json($orgao);
});

Route::put('/orgaos/{id}', function (Request $request, string $id) {
    $orgao = Orgao::find($id);

    if (!$orgao) {
        return response()->json(['message' => 'Órgão não encontrado'], 404);
    }

    $request->validate([
        'name' => 'required|string|max:255|unique:orgaos,name,' . $orgao->id,
    ]);

    $orgao->update($request->only('name'));

    return response()->json($orgao);
});

Route::delete('/orgaos/{id}', function (string $id) {
    $orgao = Orgao::find($id);

    if (!$orgao) {
        return response()->json(['message' => 'Órgão não encontrado'], 404);
    }

    $orgao->delete();

    return response()->json(['message' => 'Órgão removido com sucesso']);
});

// =============================================
// FORNECEDORES
// =============================================

Route::get('/fornecedores', function () {
    $fornecedores = Fornecedor::all();
    return response()->json($fornecedores);
});

Route::post('/fornecedores', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'document' => 'required|string|max:20|unique:fornecedores,document',
    ]);

    $fornecedor = Fornecedor::create($request->only(['name', 'document']));

    return response()->json($fornecedor, 201);
});

Route::get('/fornecedores/{id}', function (string $id) {
    $fornecedor = Fornecedor::find($id);

    if (!$fornecedor) {
        return response()->json(['message' => 'Fornecedor não encontrado'], 404);
    }

    return response()->json($fornecedor);
});

Route::put('/fornecedores/{id}', function (Request $request, string $id) {
    $fornecedor = Fornecedor::find($id);

    if (!$fornecedor) {
        return response()->json(['message' => 'Fornecedor não encontrado'], 404);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'document' => 'required|string|max:20|unique:fornecedores,document,' . $fornecedor->id,
    ]);

    $fornecedor->update($request->only(['name', 'document']));

    return response()->json($fornecedor);
});

Route::delete('/fornecedores/{id}', function (string $id) {
    $fornecedor = Fornecedor::find($id);

    if (!$fornecedor) {
        return response()->json(['message' => 'Fornecedor não encontrado'], 404);
    }

    $fornecedor->delete();

    return response()->json(['message' => 'Fornecedor removido com sucesso']);
});

// =============================================
// DESPESAS
// =============================================

Route::get('/despesas', function (Request $request) {
    $query = Despesa::with(['orgao', 'fornecedor']);

    if ($request->has('orgao_id')) {
        $query->where('orgao_id', $request->orgao_id);
    }

    $despesas = $query->get();

    return response()->json($despesas);
});

Route::post('/despesas', function (Request $request) {
    $request->validate([
        'orgao_id' => 'required|exists:orgaos,id',
        'fornecedor_id' => 'required|exists:fornecedores,id',
        'descricao' => 'required|string|max:500',
        'valor' => 'required|numeric|min:0.01',
    ]);

    $despesa = Despesa::create($request->only([
        'orgao_id',
        'fornecedor_id',
        'descricao',
        'valor',
    ]));

    $despesa->load(['orgao', 'fornecedor']);

    return response()->json($despesa, 201);
});

// TODO: Implementar a lógica para retornar o total de despesas agrupado por órgão
Route::get('/despesas/total/orgao', function () {
    return response()->json([]);
});

// TODO: Implementar a lógica para retornar o total de despesas agrupado por fornecedor
Route::get('/despesas/total/fornecedor', function () {
    return response()->json([]);
});

// POST /api/despesas/{id}/comprovante — faz upload
Route::post('/despesas/{id}/comprovante', function (string $id) {
    $despesa = Despesa::findOrFail($id);

    $data = request()->validate([
        'file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
    ]);

    if ($despesa->comprovante_path) {
        Storage::disk('comprovantes')->delete($despesa->comprovante_path);
    }

    $path = $data['file']->store('', 'comprovantes');

    $despesa->update([
        'comprovante_path' => $path,
        'comprovante_mime' => $data['file']->getMimeType(),
    ]);

    return response()->json(['path' => $path], 200);
});

// GET /api/despesas/{id}/comprovante — baixa/visualiza o arquivo
Route::get('/despesas/{id}/comprovante', function (string $id) {
    $despesa = Despesa::findOrFail($id);

    if (!$despesa->comprovante_path || !Storage::disk('comprovantes')->exists($despesa->comprovante_path)) {
        return response()->json(['message' => 'Comprovante não encontrado.'], 404);
    }

    return response()->file(
        Storage::disk('comprovantes')->path($despesa->comprovante_path),
        ['Content-Type' => $despesa->comprovante_mime ?? 'application/octet-stream']
    );
});

// DELETE /api/despesas/{id}/comprovante — remove o arquivo
Route::delete('/despesas/{id}/comprovante', function (string $id) {
    $despesa = Despesa::findOrFail($id);

    if (!$despesa->comprovante_path) {
        return response()->json(['message' => 'Nenhum comprovante vinculado.'], 404);
    }

    Storage::disk('comprovantes')->delete($despesa->comprovante_path);

    $despesa->update(['comprovante_path' => null, 'comprovante_mime' => null]);

    return response()->noContent();
});

Route::get('/despesas/{id}', function (string $id) {
    $despesa = Despesa::with(['orgao', 'fornecedor'])->find($id);

    if (!$despesa) {
        return response()->json(['message' => 'Despesa não encontrada'], 404);
    }

    return response()->json(array_merge($despesa->toArray(), [
        'comprovante_url' => $despesa->comprovante_path
            ? url("/api/despesas/{$despesa->id}/comprovante")
            : null,
    ]));
});

Route::put('/despesas/{id}', function (Request $request, string $id) {
    $despesa = Despesa::find($id);

    if (!$despesa) {
        return response()->json(['message' => 'Despesa não encontrada'], 404);
    }

    $request->validate([
        'orgao_id' => 'required|exists:orgaos,id',
        'fornecedor_id' => 'required|exists:fornecedores,id',
        'descricao' => 'required|string|max:500',
        'valor' => 'required|numeric|min:0.01',
    ]);

    $despesa->update($request->only([
        'orgao_id',
        'fornecedor_id',
        'descricao',
        'valor',
    ]));

    $despesa->load(['orgao', 'fornecedor']);

    return response()->json($despesa);
});

Route::delete('/despesas/{id}', function (string $id) {
    $despesa = Despesa::find($id);

    if (!$despesa) {
        return response()->json(['message' => 'Despesa não encontrada'], 404);
    }

    $despesa->delete();

    return response()->json(['message' => 'Despesa removida com sucesso']);
});
