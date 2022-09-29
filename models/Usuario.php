<?php 

namespace Model;

use Model\ActiveRecord;

class Usuario extends ActiveRecord {
    //Base de datos.
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];
    
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
    }

    //Mensajes de validacion para la creacion de una cuenta. 

    public function validarNC(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if(!$this->apellido){
            self::$alertas['error'][] = 'El apellido es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El e-mail es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }else{
            if(strlen($this->password) < 6){
                self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres.';
            }
        }
        if(!$this->telefono){
            self::$alertas['error'][] = 'El telefono es obligatorio';
        }

        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El e-mail es obligatorio.';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña es necesaria.';
        }

        return self::$alertas;
    }
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El e-mail es obligatorio.';
        }

        return self::$alertas;
    }
    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña es obligatoria.';
        }
        if(strlen($this->password)<6){
            self::$alertas['error'][]= 'La contraseña debe tener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    //Revisa si el usuario ya existe.
    public function existeUsuario(){
        $query = "SELECT * FROM " . self::$tabla ." WHERE email = '". $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas['error'][] = 'El usuario ya esta registrado.';
        }

        return $resultado;
    }
    //Hashear password
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //Crear Token
    public function crearToken(){
        $this->token=uniqid();
    }

    //Comprobar password and verificado
    public function ComprobarPasswordAndVerificado($passwordActual){
        $resultado = password_verify($passwordActual, $this->password);

        if(!$resultado || !$this->confirmado){
            self::setAlerta('error', 'Password incorrecta o el usuario no esta confirmado.');
        }else{
            return true;
        }
    }

}