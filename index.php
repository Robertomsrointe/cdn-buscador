<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos Digitales</title>
    <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&family=Roboto:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>

<?php
$directorioBase = "img"; // Carpeta donde están las imágenes

// Función para formatear nombres
function formatearNombre($nombre) {
    return ucwords(str_replace('_', ' ', pathinfo($nombre, PATHINFO_FILENAME)));
}

// Función para obtener carpetas e imágenes
function listarContenido($ruta) {
    $contenido = array_diff(scandir($ruta), ['..', '.']);
    $carpetas = [];
    $imagenes = [];

    foreach ($contenido as $item) {
        if (is_dir("$ruta/$item")) {
            $carpetas[] = $item;
        } elseif (preg_match('/\.(jpg|jpeg|png|gif)$/i', $item)) {
            $imagenes[] = $item;
        }
    }
    return ['carpetas' => $carpetas, 'imagenes' => $imagenes];
}

// Ruta actual según la galería seleccionada
$rutaActual = $directorioBase;
$galeriaActual = isset($_GET['galeria']) ? $_GET['galeria'] : null;

if ($galeriaActual) {
    $rutaActual .= "/$galeriaActual";
    if (!is_dir($rutaActual)) {
        die("❌ Galería no encontrada.");
    }
}

// Obtener carpetas e imágenes
$contenido = listarContenido($rutaActual);
?>

<div class="banner">
    <img class="logo" src="recursos/logo.png" alt="">
</div>

<!-- Contenedor para el título, botón y buscador -->
<div class="header-container">
    <?php if ($galeriaActual): ?>
        <a href="index.php" class="volver">⬅ Volver</a>
    <?php else: ?>
        <div class="espacio-vacio"></div> <!-- Mantiene la alineación si no hay botón -->
    <?php endif; ?>

    <h1><?= $galeriaActual ? formatearNombre($galeriaActual) : "Recursos Digitales" ?></h1>

    <!-- Campo de búsqueda -->
    <input type="text" id="buscador" placeholder="Buscar por referencia...">
</div>

<!-- Contenedor de resultados de búsqueda -->
<div class="imagenes" id="resultado-busqueda" style="display: none;"></div>


<!-- Contenedor de resultados de búsqueda -->
<div class="imagenes" id="resultado-busqueda" style="display: none;"></div>


<!-- Contenedor principal -->
<div class="main">
    
    <!-- Sección de categorías -->
    <div class="galerias">
        <?php foreach ($contenido['carpetas'] as $carpeta): ?>
            <div class="galeria">
                <a href="index.php?galeria=<?= urlencode($galeriaActual ? "$galeriaActual/$carpeta" : $carpeta) ?>">
                    <h2><?= formatearNombre($carpeta) ?></h2>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Contenedor de imágenes originales -->
    <div class="imagenes" id="imagenes-originales">
        <?php foreach ($contenido['imagenes'] as $imagen): ?>
            <div class="imagen">
                <img src="<?= "$rutaActual/$imagen" ?>" alt="<?= formatearNombre($imagen) ?>" class="descargar-imagen">
                <p><?= formatearNombre($imagen) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

</div> <!-- Fin del div.main -->

<script>
document.getElementById("buscador").addEventListener("input", function() {
    let query = this.value.trim();
    let resultados = document.getElementById("resultado-busqueda");
    let originales = document.getElementById("imagenes-originales");

    if (query.length > 0) {
        fetch("buscar.php?q=" + encodeURIComponent(query))
            .then(response => response.text())
            .then(data => {
                resultados.innerHTML = data;
                resultados.style.display = "grid"; // Mostrar resultados con el mismo diseño
                originales.style.display = "none"; // Ocultar imágenes originales
                activarDescarga(); // Aplicar funcionalidad de descarga
            });
    } else {
        resultados.innerHTML = ""; // Vaciar resultados si no hay búsqueda
        resultados.style.display = "none"; // Ocultar el contenedor de búsqueda
        originales.style.display = "grid"; // Mostrar imágenes originales
        activarDescarga(); // Aplicar la funcionalidad de descarga a las imágenes originales
    }
});

// Función para habilitar la descarga de imágenes al hacer clic
function activarDescarga() {
    document.querySelectorAll(".descargar-imagen").forEach(img => {
        img.addEventListener("click", function() {
            const url = this.src;
            const enlace = document.createElement("a");
            enlace.href = url;
            enlace.download = url.substring(url.lastIndexOf("/") + 1); // Obtener el nombre del archivo
            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
        });
    });
}

// Activar la descarga al cargar la página
activarDescarga();
</script>

</body>
</html>
