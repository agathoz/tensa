<?php
// Funciones de seguridad
function limpiarCadenas($cadena) {
    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);
    $cadena = htmlspecialchars($cadena, ENT_QUOTES, 'UTF-8');
    return $cadena;
}

function subirImagenSegura($archivoInfo, $destinoDir = '../../assets/uploads/') {
    if(!is_dir($destinoDir)){
        mkdir($destinoDir, 0755, true);
    }
    
    $nombreOriginal = basename($archivoInfo['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    $mime_type = mime_content_type($archivoInfo['tmp_name']);
    
    $permitidos_mime = ['image/jpeg', 'image/png', 'image/gif'];
    $permitidos_ext = ['jpg', 'jpeg', 'png', 'gif'];
    
    if(!in_array($mime_type, $permitidos_mime) || !in_array($extension, $permitidos_ext)){
        return false;
    }
    
    if(getimagesize($archivoInfo['tmp_name']) === false){
        return false;
    }
    
    $nuevoNombre = uniqid("img_", true) . '.' . $extension;
    $rutaFinal = $destinoDir . $nuevoNombre;
    
    if(move_uploaded_file($archivoInfo['tmp_name'], $rutaFinal)){
        return $nuevoNombre;
    }
    
    return false;
}
?>
