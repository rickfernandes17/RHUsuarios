<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use LdapRecord\Models\ActiveDirectory\OrganizationalUnit;
use Illuminate\Support\Facades\Log;

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
            'ou_dn' => 'required|string|max:500',
        ], [
            'nome.required' => 'O nome da empresa é obrigatório.',
            'dominio.required' => 'O domínio é obrigatório.',
            'dominio.unique' => 'Este domínio já está cadastrado no sistema.',
            'dominio.regex' => 'O formato do domínio é inválido. Exemplo: empresa.com ou empresa.com.br',
            'ou_dn.required' => 'A OU no Active Directory é obrigatória.',
        ]);

        // Formata o domínio para minúsculo
        $validated['dominio'] = strtolower(trim($validated['dominio']));

        try {
            $ouName = trim($validated['nome']);

            $baseDn = $validated['ou_dn'];

            // OU principal da empresa
            $empresaOu = new OrganizationalUnit();
            $empresaOu->setAttribute('ou', $ouName);
            $empresaOu->inside($baseDn);
            $empresaOu->save();

            $empresaDn = "OU={$ouName},{$baseDn}";

            $usuariosDn     = "OU=Usuarios,{$empresaDn}";
            $gruposDn       = "OU=Grupos,{$empresaDn}";
            $computadoresDn = "OU=Computadores,{$empresaDn}";
            $servidoresDn   = "OU=Servidores,{$empresaDn}";
            $desativadosDn  = "OU=Desativados,{$empresaDn}";

            // OUs filhas
            $subOus = [
                'Usuarios',
                'Grupos',
                'Computadores',
                'Servidores',
                'Desativados',
            ];

            foreach ($subOus as $subOu) {

                $ou = new OrganizationalUnit();
                $ou->setAttribute('ou', $subOu);
                $ou->inside($empresaDn);
                $ou->save();
            }

            $validated['ou_dn'] = $empresaDn;
            $validated['usuarios_ou_dn'] = $usuariosDn;
            $validated['grupos_ou_dn'] = $gruposDn;
            $validated['computadores_ou_dn'] = $computadoresDn;
            $validated['servidores_ou_dn'] = $servidoresDn;
            $validated['desativados_ou_dn'] = $desativadosDn;

            Empresa::create($validated);
        } catch (\Exception $e) {

            Log::error('Erro ao criar OU: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors([
                    'ldap' => 'Erro ao criar OU no Active Directory: ' . $e->getMessage()
                ]);
        }

        return redirect()->route('empresas.index')->with('success', 'Empresa cadastrada com sucesso!');
    }
}
