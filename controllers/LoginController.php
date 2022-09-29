<?php 

    namespace Controllers;

    use Classes\Email;
    use Model\Usuario;
    use MVC\Router;


    class LoginController{
        public static function login(Router $router){
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $auth = new Usuario($_POST);

                $alertas = $auth->validarLogin();

                if(empty($alertas)){
                    //Comprobar que exista el usuario.
                    $usuario = Usuario::where('email', $auth->email);

                    if($usuario){
                        //verificar el password
                        if($usuario->ComprobarPasswordAndVerificado($auth->password)){
                            //Autenticar al usuario
                            session_start();

                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;

                            //Redireccionamiento
                            if($usuario->admin === '1'){
                                $_SESSION['admin'] = $usuario->admin ?? NULL;

                                header('Location: /admin');
                            }else{
                                header('Location: /cita');
                            }
                        }
                    }
                    else{
                        Usuario::setAlerta('error', 'Usuario no encontrado');
                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router->render('auth/login', [
                'alertas'=>$alertas
            ]);
        }
        public static function logout(){
            session_start();

            $_SESSION = [];

            header('Location: /');
        }
        public static function olvide(Router $router){
            
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $auth = new Usuario($_POST);

                $alertas = $auth->validarEmail();

                if(empty($alertas)){
                    $usuario = Usuario::where('email', $auth->email);

                    if($usuario && $usuario->confirmado === "1"){
                        //Generar token de un solo uso para cambiar psw.
                        $usuario->crearToken();
                        $usuario->guardar();

                        //Enviar el Email.
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarInstrucciones();

                        //Alerta de exito de envio del E-Mail para restablecer la contraseña.
                        Usuario::setAlerta('exito', 'Se ha enviado un email a tu correo para restablecer tu contraseña');
                    }else {
                        Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    }

                    $alertas = Usuario::getAlertas();

                }
            }

            
            $router->render('auth/olvide-password', [
                'alertas'=>$alertas
            ]);
        }
        public static function recuperar(Router $router){
            $alertas = [];
            $error = false;

            $token = s($_GET['token']);

            //Buscar usuario por su token.
            $usuario = Usuario::where('token', $token);

            if(empty($usuario)){
                Usuario::setAlerta('error', 'Token invalido');
                $error = true;
            }

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $password = new Usuario($_POST);
                $alertas = $password->validarPassword();

                if(empty($alertas)){
                    $usuario->password = $password->password;
                    $usuario->hashPassword();
                    $usuario->token = null;

                    $resultado = $usuario->guardar();

                    if($resultado){
                        header('Location: /');
                    }
                }
            }

            $alertas = Usuario::getAlertas();
            $router->render('auth/recuperar-password', [
                'alertas'=>$alertas,
                'error'=>$error
            ]);
        }
        public static function crear(Router $router){
            $usuario = new Usuario;

            //Alertas vacias.
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $usuario->sincronizar($_POST);
                $alertas = $usuario->validarNC();

                //Revisar que $alertas este vacio.
                if(empty($alertas)){
                    //Verificar que el usuario no este registrado. 
                    $resultado = $usuario->existeUsuario();

                    if($resultado->num_rows){
                        //Esta registrado.
                        $alertas = Usuario::getAlertas();
                    }
                    else{
                        //No esta registrado.

                        //Hashear el password.
                        $usuario->hashPassword();

                        //Generar Token.
                        $usuario->crearToken();

                        //Enviar el email.
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarConfirmacion();

                        //Crear el usuario.
                        $resultado = $usuario->guardar();

                        if($resultado){
                            header('Location: /mensaje');
                        }
                    }
                }
            }

            $router->render('auth/crear-cuenta', [
                'usuario'=>$usuario,
                'alertas'=>$alertas
            ]);
        }


        public static function mensaje(Router $router){    
            $router->render('auth/mensaje');
        } 

        public static function confirmar(Router $router){
            $token = s($_GET['token']);
            $usuario = Usuario::where('token', $token);

            if(empty($usuario)){
                //Mostrar mensaje de error
                Usuario::setAlerta('error', 'Token no valido');
                
            }else{
                //Modificar a usuario identificado.
                $usuario->confirmado = '1';
                $usuario->token = '';
                $usuario->guardar();
                Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
            }

            $alertas = Usuario::getAlertas();

            $router->render('auth/confirmar-cuenta', [
                'alertas'=>$alertas
            ]);
        } 
    }