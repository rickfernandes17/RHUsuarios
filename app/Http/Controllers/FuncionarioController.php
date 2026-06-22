<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Funcionario;
use App\Services\PostfixAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FuncionarioController extends Controller
{
    protected $postfixService;

    public function __construct(PostfixAdminService $postfixService)
    {
        $this->postfixService = $postfixService;
    }

    /**
     * Exibe a listagem de funcionários.
     */
    public function index()
    {
        $funcionarios = Funcionario::with('empresa')->orderBy('nome')->get();
        return view('funcionarios.index', compact('funcionarios'));
    }

    /**
     * Exibe o formulário de criação de funcionários.
     */
    public function create()
    {
        $empresas = Empresa::orderBy('nome')->get();
        return view('funcionarios.create', compact('empresas'));
    }

    /**
     * Salva o novo funcionário localmente e cria seu e-mail no PostfixAdmin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nome' => 'required|string|max:255',
            'sobrenome' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                'unique:funcionarios,cpf',
                'regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/' // validação básica de formato de CPF
            ],
            'cargo' => 'required|string|max:255',
            'email_local' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._-]+$/' // apenas caracteres permitidos na parte local do email
            ],
            'email_password' => 'required|string|min:6',
        ], [
            'empresa_id.required' => 'A empresa é obrigatória.',
            'empresa_id.exists' => 'A empresa selecionada é inválida.',
            'nome.required' => 'O nome é obrigatório.',
            'sobrenome.required' => 'O sobrenome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'cpf.regex' => 'O formato do CPF é inválido (Ex: 000.000.000-00).',
            'cargo.required' => 'O cargo é obrigatório.',
            'email_local.required' => 'A parte local do e-mail é obrigatória.',
            'email_local.regex' => 'A parte local do e-mail contém caracteres inválidos (Ex. permitido: nome.sobrenome).',
            'email_password.required' => 'A senha do e-mail é obrigatória.',
            'email_password.min' => 'A senha do e-mail deve ter pelo menos 6 caracteres.',
        ]);

        $empresa = Empresa::findOrFail($request->empresa_id);

        // Gera o endereço de e-mail corporativo completo
        $emailLocal = strtolower(trim($request->email_local));
        $emailCorporativo = $emailLocal . '@' . $empresa->dominio;

        // Validar se o e-mail completo gerado já existe localmente
        $existsLocal = Funcionario::where('email_corporativo', $emailCorporativo)->exists();
        if ($existsLocal) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['email_local' => 'Este e-mail corporativo já está em uso por outro funcionário.']);
        }

        try {
            // Usamos uma transação para garantir consistência no banco do sistema de RH local
            DB::beginTransaction();

            // 1. Cadastra o funcionário localmente no banco de RH
            $funcionario = Funcionario::create([
                'empresa_id' => $empresa->id,
                'nome' => $validated['nome'],
                'sobrenome' => $validated['sobrenome'],
                'cpf' => $validated['cpf'],
                'cargo' => $validated['cargo'],
                'email_corporativo' => $emailCorporativo,
            ]);

            // Nome completo do funcionário para cadastrar no PostfixAdmin
            $nomeCompleto = $funcionario->nome . ' ' . $funcionario->sobrenome;

            // 2. Cria a caixa postal e aliases no PostfixAdmin via conexão secundária
            // Caso ocorra alguma exceção no banco do PostfixAdmin, ela será capturada
            // e fará o rollback do funcionário no banco local.
            $this->postfixService->createMailAccount(
                $nomeCompleto,
                $emailCorporativo,
                $request->email_password,
                $empresa->dominio
            );

            DB::commit();

            return redirect()->route('funcionarios.index')
                ->with('success', "Funcionário {$nomeCompleto} cadastrado e e-mail corporativo ({$emailCorporativo}) criado com sucesso no PostfixAdmin!");

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Falha no cadastro do funcionário e criação de e-mail. Transação desfeita. Erro: " . $exception->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Falha ao integrar com o servidor de e-mails PostfixAdmin. Verifique se o banco do PostfixAdmin está acessível e tente novamente. Detalhes: ' . $exception->getMessage()]);
        }
    }
}
