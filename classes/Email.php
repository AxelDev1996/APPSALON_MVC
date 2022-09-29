<?php

    namespace Classes;
    use PHPMailer\PHPMailer\PHPMailer;

    class Email{

        public $email;
        public $nombre;
        public $token;
        

        public function __construct($email, $nombre, $token)
        {
            $this->email = $email;
            $this->nombre = $nombre;
            $this->token = $token;
        }

        //Envia un E-Mail para confirmar la existencia el correo electronico. 
        public function enviarConfirmacion(){
            //Crear el objeto del e-mail.
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'fddb8157e2c871';
            $mail->Password = '1f2ec369688a7c';

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
            $mail->Subject = 'Confirma tu cuenta';

            //set HTML

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre ."</strong> Has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace</p>";
            $contenido .= "<p>Presiona aqui: <a href=http://localhost:3000/confirmar-cuenta?token=" . $this->token . ">Confirmar Cuenta</a></p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje.</p>";
            $contenido .= "</html>";
            $mail->Body = $contenido;

            //Enviar el E-Mail.
            $mail->send();
        }

        public function enviarInstrucciones(){
            //Crear el objeto del e-mail.
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'fddb8157e2c871';
            $mail->Password = '1f2ec369688a7c';

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
            $mail->Subject = 'Reestablece tu password';

            //set HTML

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre ."</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
            $contenido .= "<p>Presiona aqui: <a href=http://localhost:3000/recuperar?token=" . $this->token . ">Reestablecer contraseña</a></p>";
            $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje.</p>";
            $contenido .= "</html>";
            $mail->Body = $contenido;

            //Enviar el E-Mail.
            $mail->send();
        }
    }