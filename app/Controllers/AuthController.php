<?php

namespace App\Controllers;

use App\Models\Person;
use App\Models\User;
use App\Models\UserPasswordRecovery;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
    const CIPHER = 'AES-256-CBC';
    const OPTIONS = 0;

    public function login($request, $response)
    {
        if($request->isGet())
            return $this->container->view->render($response, 'admin/login.twig');


        if(!$this->container->auth->attemptAdmin(
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
            // erro no return
            return $response->withRedirect($this->container->router->pathFor('auth.login'));
        }
    }


    public function forgot($request, $response)
    {
        if($request->isGet())
            return $this->container->view->render($response, 'admin/forgot-password.twig');

        $email = $request->getParam('desemail');

        $person = Person::with('user')->where('desemail', $email)->first();

        if (count($person) === 0) {
            throw new \Exception('Email não encontrado');
        } else {

            $iduser = $person->user()->iduser;
            $desip = $_SERVER['REMOTE_ADDR'];

            $user = new UserPasswordRecovery();
            $user->iduser = $iduser;
            $user->desip = $desip;
            $user->save();

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


    public function loginSite($request, $response)
    {
        if($request->isGet())
            return $this->container->view->render($response, 'site/login.twig');


        if(!$this->container->auth->attemptSite(
            $request->getParam('login'),
            $request->getParam('password'))) {
            return $response->withRedirect($this->container->router->pathFor('site.login'));
        }

        return $response->withRedirect($this->container->router->pathFor('site.home'));
    }


    public function logoutSite($request, $response)
    {
        if(isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            return $response->withRedirect($this->container->router->pathFor('site.home'));
        }
    }


    /**
     * Função para enviar email para o usuário que não é administrado.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function forgotSite($request, $response)
    {
        if($request->isGet())
            return $this->container->view->render($response, 'site/forgot.twig');

        if($request->isPost())
            $email = $request->getParam('email');

            $person = Person::with('user')->where('desemail', $email)->first();

            if ($person->idperson === 0) {
                $this->container->flash->addMessage('error', 'Email não encontrado.');
                return $response->withRedirect($this->container->router->pathFor('site.forgot'));
            } else {


                $iduser = $person->user->iduser;
                $desip = $_SERVER['REMOTE_ADDR'];

                $userPasswordRecovery = new UserPasswordRecovery();
                $userPasswordRecovery->iduser = $iduser;
                $userPasswordRecovery->desip = $desip;
                $userPasswordRecovery->save();

                $idrecovery = $userPasswordRecovery->idrecovery;
                $key = 'HcodePhp7_Secret';
                $iv = '1234567812345678';


                $code = openssl_encrypt($idrecovery, self::CIPHER, $key,self::OPTIONS, $iv);

                $link = "http://localhost/hcode-slim-3/public/site/reset_password?code=";

                /**
                 * A chave tem que ser igual ao que esta no model Mail: $mail->addAddress($to['email'], $to['name']);
                 */
                $payload = [
                    'name' => $person->desperson,
                    'email' => $person->desemail,
                    'link' => $link,
                    'code' => $code

                ];

                $this->container->mail->send($payload, 'forgot.twig', 'Recover Password', $payload);
            }

            $this->container->flash->addMessage('success', 'Email enviado com sucesso.');
            return $response->withRedirect($this->container->router->pathFor('site.forgot'));
    }



    /**
     * Função para resetar a senha do usuário
     * @param $request
     * @param $response
     * @return mixed
     */
    public function resetPasswordSite($request, $response)
    {
        $code = $request->getQueryParams();
        $code = $code['code'];

        $key = 'HcodePhp7_Secret';
        $iv = '1234567812345678';

        $idrecovery = openssl_decrypt($code, self::CIPHER, $key, self::OPTIONS, $iv);

        $userPasswordRecovery = UserPasswordRecovery::where('idrecovery', '=', $idrecovery)->first();
        $iduser = $userPasswordRecovery->iduser;
        $user = User::where('iduser', '=', $iduser)->first();

        if($request->isGet())

            return $this->container->view->render($response, 'site/forgot-reset.twig');

        if ($request->isPost()) # erro aqui

            $user->update([
                'despassword' => $request->getParam('password')
            ]);

            $this->container->flash->addMessage('success', 'Senha recuperada com sucesso!');
            return $response->withRedirect($this->container->router->pathFor('site.login'));
    }
}
