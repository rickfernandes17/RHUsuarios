<?php

namespace App\Services;

use LdapRecord\Models\ActiveDirectory\User;

class ActiveDirectoryService
{
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

        /*
         * Define senha
         */

        //$user->unicodepwd = $senha;

        /*
         * Habilita usuário
         */
        //$user->userAccountControl = 512;
        //dd($user);
        $user->save();

        return [
            'dn' => $user->getDn(),
            'login' => $login
        ];
    }
}
