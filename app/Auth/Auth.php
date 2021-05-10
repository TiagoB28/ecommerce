<?php

namespace App\Auth;

use App\Models\User;

class Auth
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Verifica se a sessão existe e, se existi, ela procura o id do usuário e retorna
     * todos os dados do usuário.
     * @return mixed
     */
    public function user()
    {
        if (isset($_SESSION['user']))
            return User::find($_SESSION['user']);
    }

    /**
     * Verifica se a sessão existe.
     * @return bool
     */
    public function check()
    {
        return isset($_SESSION['user']);
    }

    /**
     * Função que avalia a tentativa de login do usuário.
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function attempt(string $login, string $password)
    {
        $user = User::where('deslogin', $login)->first();

        if (!$user || !password_verify($password, $user->despassword)) {
            $this->container->flash->addMessage('error', 'Suas credencias estão erradas');
            return false;
        }

       $_SESSION['user'] = $user->iduser;

        return true;
    }
}