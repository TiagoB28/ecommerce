<?php

namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mail
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param array $to -> nome e email do destinatário(usuário).
     * @param string $template -> template utilizado para enviar o email.
     * @param string $subject -> assunto.
     * @param array $payload
     */
    public function send(array $to, string $template, string $subject, array $payload)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.ethereal.email';
            $mail->SMTPAuth = true;
            $mail->Username = 'florencio40@ethereal.email';
            $mail->Password = 'P257WaPd7p7MeUvyBh';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->CharSet = 'utf-8';
            $mail->setFrom('from@example.com', 'James Smith');
            $mail->addAddress($to['email'], $to['name']);

            $mail->isHTML(true);
            $mail->Subject = $subject;

            # configura o corpo do email que será enviado.
            $mail->Body = $this->container->view->render(
                $this->container->response,
                'mails/'. $template,
                $payload
            );

            $mail->send();
        } catch (PHPMailerException $e) {
            echo 'Houve um erro na tentativa de enviar um e-mail';
        }
    }
}