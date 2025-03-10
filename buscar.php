<?php
$directorioBase = "img"; // Carpeta principal de imágenes

// Función para formatear nombres
function formatearNombre($nombre) {
    return ucwords(str_replace('_', ' ', pathinfo($nombre, PATHINFO_FILENAME)));
}

// Función para listar imágenes en todas las subcarpetas
function listarImagenes($ruta) {
    $imagenes = [];
    $archivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ruta));

    foreach ($archivos as $archivo) {
        if ($archivo->isFile() && preg_match('/\.(jpg|jpeg|png|gif)$/i', $archivo->getFilename())) {
            $imagenes[] = $archivo->getPathname(); // Ruta completa de la imagen
        }
    }
    return $imagenes;
}

// Obtener el término de búsqueda
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';

$imagenes = listarImagenes($directorioBase);
$resultados = '';

foreach ($imagenes as $imagen) {
    $nombreImagen = basename($imagen);

    // Si hay búsqueda, filtrar imágenes por coincidencia
    if ($busqueda && stripos($nombreImagen, $busqueda) === false) {
        continue;
    }

    // Construir HTML de cada imagen con funcionalidad de descarga
    $resultados .= '<div class="imagen">';
    $resultados .= '<img src="' . $imagen . '" alt="' . formatearNombre($nombreImagen) . '" class="descargar-imagen">';
    $resultados .= '<p>' . formatearNombre($nombreImagen) . '</p>';
    $resultados .= '</div>';
}

// Si no hay resultados
if (empty($resultados)) {
    $resultados = '<p>No se encontraron imágenes.</p>';
}

// Retornar el HTML
echo $resultados;
?>
