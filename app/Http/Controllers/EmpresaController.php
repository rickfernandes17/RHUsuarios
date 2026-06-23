<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Exibe a listagem de empresas.
     */
    public function index()
    {
        $empresas = Empresa::withCount('funcionarios')->orderBy('nome')->get();
        return view('empresas.index', compact('empresas'));
    }

    /**
     * Exibe o formulário de criação de empresas.
     */
    public function create()
    {
        return view('empresas.create');
    }

    /**
     * Salva a nova empresa.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'dominio' => [
                'required',
                'string',
                'max:255',
                'unique:empresas,dominio',
                'regex:/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9](\.[a-zA-Z]{2,})+$/' // validação básica de domínio
            ],
        ], [
            'nome.required' => 'O nome da empresa é obrigatório.',
            'dominio.required' => 'O domínio é obrigatório.',
            'dominio.unique' => 'Este domínio já está cadastrado no sistema.',
            'dominio.regex' => 'O formato do domínio é inválido. Exemplo: empresa.com ou empresa.com.br',
        ]);

        // Formata o domínio para minúsculo
        $validated['dominio'] = strtolower(trim($validated['dominio']));

        Empresa::create($validated);

        return redirect()->route('empresas.index')->with('success', 'Empresa cadastrada com sucesso!');
    }
}
