<?php

namespace App\Controllers;

use App\Models\User;
use App\Auth;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
    public function login($request, $response)
    {
        if($request->isGet())
            return $this->container->view->render($response, 'admin/login.twig');


        if(!$this->container->auth->attempt(
            $request->getParam('login'),
            $request->getParam('password'))) {
            return $response->withRedirect($this->container->router->pathFor('auth.login'));
        }

        return $response->withRedirect($this->container->router->pathFor('admin.users'));
    }

    public function logout($request, $response)
    {
        if(isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            return $response->withRedirect($this->container->router->pathFor('auth.login'));
        }

    }


    public function forgot($request, $response)
    {
        if($request->isGet())
            return $this->container->view->render($response, 'admin/forgot-password.twig');

        $email = $request->getParam('desemail');

        $person = Person::where('desemail', $email)->first();

        if (count($person) === 0) {
            echo 'Email não encontrado';
        } else {

            /**
             * A chave tem que ser igual ao que esta no model Mail: $mail->addAddress($to['email'], $to['name']);
             */
            $payload = [
                'name' => $person->desperson,
                'email' => $person->desemail

            ];

            $this->container->mail->send($payload, 'forgot.twig', 'Recover Password', $payload);
        }

        return $response->withRedirect($this->container->router->pathFor('auth.login'));
    }
}
