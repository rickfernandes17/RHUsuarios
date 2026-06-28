<?php

namespace App\Services;

use LdapRecord\Models\ActiveDirectory\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ActiveDirectoryService
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.active_directory_api.url', 'http://sistemarh.api.local'), '/');
    }

    /**
     * Cria um novo usuário no Active Directory.
     * Primeiro salva via LDAP (LdapRecord) e depois chama a API ASP.NET para definir senha e ativar a conta.
     */
    public function createUser(
        string $nome,
        string $sobrenome,
        string $login,
        string $senha,
        string $usuariosOu,
        string $emailCorporativo,
    ): array {
        $user = new User();

        $user->inside($usuariosOu);

        $user->cn = "{$nome} {$sobrenome}";
        $user->givenName = $nome;
        $user->sn = $sobrenome;
        $user->displayName = "{$nome} {$sobrenome}";

        $user->sAMAccountName = $login;
        $user->userPrincipalName = "{$login}@meudominio.ad";
        $user->mail = $emailCorporativo;
        // Obriga troca de senha no próximo logon
        $user->pwdLastSet = 0;

        // Salva o usuário básico via LDAP (LdapRecord)
        $user->save();

        try {
            // Define a senha via API ASP.NET
            $this->changePassword($login, $senha);

            // Habilita o status da conta do usuário via API ASP.NET
            //$this->enableAccount($login);
        } catch (\Exception $e) {
            // Em caso de falha nas operações pós-criação da API ASP.NET,
            // exclui o usuário do AD (via LDAP) para garantir consistência
            try {
                $user->delete();
                Log::info("Usuário {$login} removido do Active Directory devido a falha nas chamadas subsequentes da API ASP.NET.");
            } catch (\Exception $deleteException) {
                Log::error("Falha ao tentar remover usuário {$login} do Active Directory após erro na API ASP.NET: " . $deleteException->getMessage());
            }

            throw $e;
        }

        return [
            'dn' => $user->getDn(),
            'login' => $login
        ];
    }

    /**
     * Define/Troca a senha de um usuário no Active Directory usando a API ASP.NET.
     */
    public function changePassword(string $samAccountName, string $newPassword): void
    {
        $response = Http::post("{$this->apiUrl}/api/ActiveDirectory/trocar-senha", [
            'samAccountName' => $samAccountName,
            'novaSenha' => $newPassword,
        ]);
        if ($response->failed()) {
            $errorMsg = $response->json('mensagem') ?? 'Erro ao alterar a senha do usuário no Active Directory.';
            Log::error("Falha na API ASP.NET (trocar-senha) para o usuário {$samAccountName}: {$errorMsg}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Habilita uma conta de usuário no Active Directory usando a API ASP.NET.
     */
    public function enableAccount(string $samAccountName): void
    {
        $response = Http::post("{$this->apiUrl}/api/ActiveDirectory/habilitar", [
            'samAccountName' => $samAccountName,
        ]);

        if ($response->failed()) {
            $errorMsg = $response->json('mensagem') ?? 'Erro ao habilitar a conta do usuário no Active Directory.';
            Log::error("Falha na API ASP.NET (habilitar) para o usuário {$samAccountName}: {$errorMsg}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Desabilita uma conta de usuário no Active Directory usando a API ASP.NET.
     */
    public function disableAccount(string $samAccountName): void
    {
        $response = Http::post("{$this->apiUrl}/api/ActiveDirectory/desabilitar", [
            'samAccountName' => $samAccountName,
        ]);

        if ($response->failed()) {
            $errorMsg = $response->json('mensagem') ?? 'Erro ao desabilitar a conta do usuário no Active Directory.';
            Log::error("Falha na API ASP.NET (desabilitar) para o usuário {$samAccountName}: {$errorMsg}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Desbloqueia uma conta de usuário travada no Active Directory usando a API ASP.NET.
     */
    public function unlockAccount(string $samAccountName): void
    {
        $response = Http::post("{$this->apiUrl}/api/ActiveDirectory/desbloquear", [
            'samAccountName' => $samAccountName,
        ]);

        if ($response->failed()) {
            $errorMsg = $response->json('mensagem') ?? 'Erro ao desbloquear a conta do usuário no Active Directory.';
            Log::error("Falha na API ASP.NET (desbloquear) para o usuário {$samAccountName}: {$errorMsg}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Busca informações detalhadas de um usuário no Active Directory usando a API ASP.NET.
     */
    public function getUser(string $samAccountName): array
    {
        $response = Http::get("{$this->apiUrl}/api/ActiveDirectory/usuario/{$samAccountName}");

        if ($response->failed()) {
            $errorMsg = $response->json('mensagem') ?? 'Erro ao obter informações do usuário no Active Directory.';
            Log::error("Falha na API ASP.NET (usuario/{$samAccountName}): {$errorMsg}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception($errorMsg);
        }

        return $response->json();
    }
}

