<?php
// Protección del directorio uploads
// Previene listado de archivos si .htaccess falla
header('HTTP/1.0 403 Forbidden');
die('Acceso denegado');
