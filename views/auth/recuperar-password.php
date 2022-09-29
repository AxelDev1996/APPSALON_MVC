<h1 class="nombre-pagina">Recuperar Password</h1>
<p class="descripcion-pagina">Coloca tu nuevo password a continuacion</p>

<?php
    include_once __DIR__ . "/../templates/alertas.php"
?>

<?php if($error) return; ?>

<form method="POST" class="formulario">
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Tu nuevo password">
    </div>

    <input type="submit" class="boton" value="Guardar nuevo password">
</form>


<div class="acciones">
    <a href="/">Ya tienes cuenta?, Iniciar Sesion</a>
    <a href="/crear-cuenta">Aun no tienes cuenta?, Obtener una</a>
</div>