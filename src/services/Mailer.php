<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/env.php';

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

class Mailer {
    public static function enviar($para, $assunto, $mensagemHtml, $mensagemTexto = '') {
        try {
            $mailerSend = new MailerSend([
                'api_key' => $_ENV['MAILERSEND_API_KEY']
            ]);

            $recipients = [
                new Recipient($para, $para)
            ];

            $emailParams = (new EmailParams())
                ->setFrom($_ENV['MAILERSEND_FROM'])
                ->setFromName($_ENV['MAILERSEND_NAME'])
                ->setRecipients($recipients)
                ->setSubject($assunto)
                ->setHtml($mensagemHtml)
                ->setText($mensagemTexto ?: strip_tags($mensagemHtml));

            $mailerSend->email->send($emailParams);

            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar com MailerSend: " . $e->getMessage());
            return false;
        }
    }
}
