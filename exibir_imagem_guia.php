<?php
require_once __DIR__ . '/conexao.php';

$tipo = $_GET['tipo'] ?? '';
$id = (int) ($_GET['id'] ?? 0);
if ($id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}

if ($tipo === 'capa') {
    $sql = "SELECT capa AS midia FROM guia WHERE id_guia = $id LIMIT 1";
} elseif ($tipo === 'foto') {
    $sql = "SELECT foto AS midia FROM guia WHERE id_guia = $id LIMIT 1";
} elseif ($tipo === 'etapa') {
    $sql = "SELECT midia FROM etapa WHERE id_etapa = $id LIMIT 1";
} else {
    http_response_code(400);
    exit('Tipo de mídia inválido.');
}

$resultado = mysqli_query($conn, $sql);
if (!$resultado) {
    http_response_code(500);
    exit('Não foi possível consultar a mídia.');
}
$registro = mysqli_fetch_assoc($resultado);
if (!$registro || $registro['midia'] === null || $registro['midia'] === '') {
    http_response_code(404);
    exit('Mídia não encontrada.');
}

$midia = $registro['midia'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->buffer($midia) ?: '';
$permitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
if (!in_array($mime, $permitidos, true)) {
    http_response_code(415);
    exit('O formato da mídia salva não é reconhecido.');
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen($midia));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
echo $midia;
