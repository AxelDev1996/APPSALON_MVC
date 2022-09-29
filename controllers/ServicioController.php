<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController{
    public static function index(Router $router){
        if(session_status() != PHP_SESSION_ACTIVE)
            session_start();

        isAdmin();

        $servicios = Servicio::all();

        $router->render('servicios/index', [
            'servicios'=>$servicios,
            'nombre' => $_SESSION['nombre']
        ]);
    }

    //Funcion que crea un servicio
    public static function crear(Router $router){
        if(session_status() != PHP_SESSION_ACTIVE)
            session_start();

        isAdmin();

        $servicio = new Servicio;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();


            if(empty($alertas)){
                $servicio->guardar();
                header('Location: /servicios');
            }

        }

        $router->render('servicios/crear', [
            'nombre'=>$_SESSION['nombre'],
            'servicio'=>$servicio, 
            'alertas'=>$alertas 
        ]);
    }
    //Funcion que actualiza un servicio
    public static function actualizar(Router $router){
        if(session_status() == PHP_SESSION_DISABLED)
            session_start();


        isAdmin();

        if(!is_numeric($_GET['id'])) return;
        $alertas = [];
        $servicioAActualizar = Servicio::find($_GET['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $servicioAActualizar->sincronizar($_POST);
            $alertas = $servicioAActualizar->validar();

            if(empty($alertas)){
                $servicioAActualizar->guardar();
                header('Location: /servicios');
            }
        }

        $router->render('servicios/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicioAActualizar,
            'alertas' => $alertas
        ]);
    }


    public static function eliminar(){
        if(session_status() == PHP_SESSION_DISABLED)
            session_start();
        
        isAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'];
            $servicio = Servicio::find($id);
            $servicio->eliminar();
            header('Location: /servicios');
        }
    }
}