<?php
// PASO 1. CONECTAR A LA BASE DE DATOS CON LOS DATOS DE LA CLASE DE LA PROFESORA NURIA
$conexion = mysqli_connect('localhost', 'DCU', 'mgtpn2020', 'DCU');

// PASO 2. CAPTURAMOS DE LA BASE DE DATOS LA INFORMACION BASICA DEL CLIENTE PARA CREAR EL SALUDO PERSONALIZADO
// Si no hay ID, detenemos todo.
if (!isset($_GET["id"]) || empty($_GET["id"])) 
{
    die("<header style='text-align:center; padding:50px;'><h1>Acceso Personalizado Requerido</h1></header>");
}
    $id_user = intval($_GET["id"]
);

// PASO 3. LA LOGICA DE RANKING PARA LAS PREFERENCIAS SELECCIONADAS POR EL CLIENTE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_reservar'])) {
    if (!empty($_POST['seleccionados'])) {
        $seleccionados = $_POST['seleccionados'];          // IMPORTANTE: Este es una cadena de caracteres para los IDs de las preferencias del cliente
        $cantidad = count($seleccionados);
        $puntos_a_sumar = 1.0 / $cantidad;                // Desde esta línea estamos implementando el sistema de puntos (1/n) donde n son la cantidad de productos

        foreach ($seleccionados as $id_pref) 
        {
            $id_pref = intval($id_pref);
            // El ranking se actualiza sumando el valor n8n (1/n)
            $sql_update = "UPDATE Honey_R_cliente_pref 
                           SET ranking = GREATEST(0, LEAST(6, ranking + $puntos_a_sumar)) 
                           WHERE id_usuario = $id_user AND id_pref = $id_pref";
            mysqli_query($conexion, $sql_update);
        }

        // Por ahora, redirigimos a la misma pagina y observaremos el cambio de posicion segun la seleccion del usuario PERO el objetivo debe ser "https://wa.me/tu_numero_negocio?text=Hola!_He_realizado_mi_seleccion_en_Honey_Obrador" donde se envia un mensaje predefinido al whatsapp del negocio para iniciar el procesos de negociacion de costos y reserva segun el pedido indicado por el cliente"
        mysqli_close($conexion);
        header("Location: index.php?id=$id_user"); 
        exit;
    }
}

//PASO 4. ESTRUCTURA PARA MOSTRAR EL MENSAJE DE BIENVENIDA PERSONALIZADO
// Aqui nos traemos al usuario y su mensaje de marketing personalizado
$sql_user_data = "
    SELECT u.nombre_comercial, u.tipo_cliente, h.hook_texto 
    FROM Honey_usuarios u
    INNER JOIN Honey_marketing_hooks h ON u.tipo_cliente = h.tipo_cliente
    WHERE u.id_usuario = $id_user";

$res_user = mysqli_query($conexion, $sql_user_data);
$user = mysqli_fetch_array($res_user);

if (!$user) {
    die("<header style='text-align:center; padding:50px;'><h1>Usuario no registrado</h1></header>");
}

$tipo = $user['tipo_cliente'];
$mensaje_hook = $user['hook_texto'];
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <!-- ACA SE DA INICIO A LA ESTRUCTURA DE LA PAGINA WEB -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Esto nos permite que la pagina sea responsive y se adapte a diferentes tamaños de pantalla -->
    <title>Honey Obrador - Respostería y ECO-Tienda</title>
    <style>
        /* Establecesmos los estilos CSS que den un ambiente muy ameno y de negocio a la Pagina de HONEY OBRADOR */
        :root { --miel: #d4a373; --miel-oscuro: #bc8a5f; --bg-body: #fdfaf5; --text-main: #2c3e50; --white: #ffffff; --seasalt: #f8f9fa; }  /* la idea de darle nombre a los colores es para hacerlo mas armonioso dentro de la implementacion del codigo */
        
        body { margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; background-color: var(--bg-body); color: var(--text-main); }
        
        .header-mofu { 
            background: var(--white); box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 40px 20px; text-align: center; border-bottom: 2px solid var(--miel); margin-bottom: 20px;
        }
        
        .header-mofu h1 { color: var(--miel); margin: 5px 0; font-size: 2.5rem; }
        
        .hook-container { 
            max-width: 800px; margin: 20px auto; line-height: 1.6; 
            font-size: 1.05rem; color: #555; text-align: center;
        }
        
        .badge { 
            display: inline-block; padding: 4px 12px; background: var(--miel); 
            color: white; border-radius: 15px; font-size: 0.8rem; margin-top: 10px;
        }
       
        .contenedor-cards { display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; max-width: 1200px; margin: 0 auto; }
        
        .card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        width: 100%; max-width: 380px;
        overflow: hidden;
        position: relative; /* Necesario para posicionar el badge */
            /* Animación inicial suave */
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInSuave 1.2s ease-out forwards;
                /* transition: transform 0.4s ease;  */
                    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
                    /* efecto rebote suave */
            }
       
       .card:hover { transform: translateY(-10px); box-shadow: 0 12px 30px rgba(0,0,0,0.15); }
       
       .card img { width: 100%; height: auto; aspect-ratio:4/3; object-fit: cover; display: block;}
       
       .card-body { padding: 20px; text-align: center; }
 
       .checkbox-reserva {
            display: flex; align-items: center; justify-content: center;
            gap: 12px; background: var(--seasalt); padding: 15px; border-radius: 10px;
            margin-bottom: 15px; cursor: pointer; border: 2px solid transparent;
            transition: all 0.3s;
                        }
        
        .checkbox-reserva:hover { border-color: var(--miel); }
        
        .btn-principal {
            display: block; width: 300px; margin: 20px auto 50px;
            padding: 15px; background: var(--miel-oscuro); color: white;
            border: none; border-radius: 30px; font-size: 1.1rem; font-weight: bold;
            cursor: pointer; transition: background 0.3s;
            text-align: center; text-decoration: none;
        }
        .btn-principal:hover { background: #a6734d; transform: scale(1.05); }

        /* Estilo de la Cinta (Ribbon) */
        .ribbon {
            position: absolute;
            top: 15px;
            right: -5px;
            padding: 8px 15px;
            color: white;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 5px 0 0 5px;
            box-shadow: -2px 2px 5px rgba(3, 8, 72, 0.2);
            z-index: 10;
            }

        /* Variaciones de la cinta según id_pref */
        .pref-1 { background: #95a5a6; } /* Estándar - Gris */
        .pref-2 { background: #9b59b6; } /* Openmind - Púrpura */
        .pref-3 { background: #27ae60; } /* Vegano - Verde */
        .pref-4 { background: #e67e22; } /* Tradicional - Naranja */
        .pref-5 { background: #f1c40f; color: #2c3e50; } /* Sin Gluten - Amarillo */
        .pref-6 { background: #2c3e50; } /* B2B - Azul Oscuro */



        
       .input-check { width: 22px; height: 22px; accent-color: var(--miel-oscuro); }
       .card-title { font-size: 1.4rem; color: var(--miel); margin: 0 0 10px 0; }
       .card-text { font-size: 0.95rem; color: #555; margin-bottom: 15px; }
       .info-desplegable { max-height: 0; overflow: hidden; opacity: 0; transition: all 0.5s ease-in-out; background: #fdfaf5; text-align: left; font-size: 0.9rem; border-radius: 8px; }
       .activador-check:checked ~ .info-desplegable { max-height: 1000px; opacity: 1; padding: 15px; margin-top: 15px; border: 1px dashed var(--miel); }
       .boton-toggle { display: inline-block; background: var(--miel); color: white; padding: 10px 20px; border-radius: 25px; cursor: pointer; font-weight: bold; }
       footer { text-align: center; padding: 30px; background: #fff; margin-top: 40px; font-size: 0.9rem; }
       hr { border: 0; border-top: 1px solid var(--miel); margin: 10px 0; }


       /* SE LE DA EFECTO DE ANIMACION LENTA A LAS TARJETAS */
        @keyframes fadeInSuave 
            {
            0% { 
            opacity: 0; 
            transform: translateY(30px); 
                }
            100% { 
            opacity: 1; 
            transform: translateY(10px); 
                }
            }

        /* Delay opcional para que aparezcan una tras otra (efecto cascada) */
        .card:nth-child(1) { animation-delay: 0.2s; }
        .card:nth-child(2) { animation-delay: 0.4s; }
        .card:nth-child(3) { animation-delay: 0.6s; }
        .card:nth-child(4) { animation-delay: 0.8s; }
        .card:nth-child(5) { animation-delay: 1.0s; }
        .card:nth-child(6) { animation-delay: 1.2s; }

        /* AJUSTES PARA SMARTPHONES */
            @media (max-width: 600px) {
        .header-mofu h1 { font-size: 1.8rem; }
        .hook-container { font-size: 0.95rem; padding: 0 10px; }
        .contenedor-cards { padding: 10px; gap: 20px; }
        .card { width: 100%; max-width: 100%; } /* Con esto la tarjeta ocupará todo el ancho del smartphone */
        .btn-principal { width: 90%; } /* Con esto se ajusta el botón para cualquier pulgar */
        .card-title { font-size: 1.2rem; }
}

    </style>
</head>

<body>

<header class="header-mofu">
    <h1>Honey Obrador</h1>
    <p>Bienvenido, <strong><?php echo $user['nombre_comercial']; ?></strong></p>
    
    <div class="hook-container">
        <span class="badge"><?php echo ($tipo == 'B2B') ? 'Cliente Business' : 'Cliente Personal'; ?></span>
        <p><?php echo $mensaje_hook; ?></p>
    </div>
</header>


<form method="POST" action="">
<main class="contenedor-cards">
    <?php    // Desde este punto aperturo un PHP para hacer una consulta dinamica a la Base de Datos 
            // Buscamos las variantes de productos que coincidan con las preferencias del usuario
    $sql_productos = "
    SELECT DISTINCT
        pb.nombre_base,
        pref.id_pref,  -- ID para las cintas de las tarjetas 
        pref.nombre_pref,
        pb.imagen_referencial, 
        vp.descripcion_adaptada, 
        vp.ingred, 
        vp.recet, 
        vp.id_variante,
        rcp.ranking
    FROM Honey_productos_base pb
    INNER JOIN Honey_variantes_producto as vp ON pb.id_producto_base = vp.id_producto_base
    INNER JOIN Honey_R_cliente_pref as rcp ON vp.id_pref_principal = rcp.id_pref
    INNER JOIN Honey_preferencias as pref ON vp.id_pref_principal = pref.id_pref
    WHERE rcp.id_usuario = $id_user
    ORDER BY rcp.ranking DESC, pb.nombre_base ASC
    ";

$resultado_cards = mysqli_query($conexion, $sql_productos);

if (mysqli_num_rows($resultado_cards) > 0) 
    {
        while ($fila = mysqli_fetch_array($resultado_cards)) 
            {
            $clase_pref = "pref-" . $fila['id_pref']; // con esto asigno la clase para la cinta
    // Aqui cierro el PHP que engloba la consulta a la base de datos
    ?>         
    
    <div class="card">
        <div class="ribbon <?php echo $clase_pref; ?>">
            <?php echo $fila['nombre_pref']; ?>
        </div>

        <img src="img/<?php echo $fila['imagen_referencial']; ?>" alt="Imagen de <?php echo $fila['nombre_base']; ?>">

        <div class="card-body">
            <h3 class="card-title"><?php echo $fila['nombre_base']; ?></h3>

                <label class="checkbox-reserva">
                    <input type="checkbox" name="seleccionados[]" value="<?php echo $fila['id_pref']; ?>" class="input-check">
                    <span>Añadir a mi reserva</span>
                </label>

            <p style="font-weight: bold; color: var(--miel-oscuro); font-size: 1.2rem;"> Descripción: </p>
            <p class="card-text"><?php echo $fila['descripcion_adaptada']; ?></p>
            
            <input type="checkbox" id="toggle-<?php echo $fila['id_variante']; ?>" class="activador-check" style="display:none;">   <!--Con este check podemos ocultra o revelar más información del postre -->
            <label for="toggle-<?php echo $fila['id_variante']; ?>" class="boton-toggle">Detalles y Receta</label>                  <!--//Este es el boton activador -->

            <div class="info-desplegable">
                <strong>Ingredientes:</strong>
                <p><?php echo $fila['ingred']; ?></p>
                <hr>
                <strong>Preparación sugerida:</strong>
                <p><?php echo $fila['recet']; ?></p>
            </div>
        </div>
    </div>

    <?php 
        } 
    } else {
        echo "<p> De momento no hay productos disponibles para tus preferencias actuales.<br> pero puedes contactar con nosotros para buscar como podemos ayudarte.<br><hr> email: <a href='mailto:honeyobrador@honeyobradoor.com'>honeyobrador@honeyobradoor.com</a></p>";
    }
    ?>
</main>

<button type="submit" name="accion_reservar" class="btn-principal">
        Confirmar Selección y Reservar
    </button>

</form>


<footer>
    <p>&copy; 2026 Honey Obrador - Especialistas en Repostería Eco</p>
    <a href="http://www.ugr.es" style="color: var(--miel);">Universidad de Granada</a>
</footer>

</body>
</html>