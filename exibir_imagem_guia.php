<?php
require_once __DIR__ . '/conexao.php';

$tipo = $_GET['tipo'] ?? '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}

$colunasGuia = ['capa' => 'capa', 'foto' => 'foto', 'video' => 'video'];

if (isset($colunasGuia[$tipo])) {
    $coluna = $colunasGuia[$tipo];
    $sql = "SELECT `$coluna` AS midia FROM guia WHERE id_guia = $id";
} elseif ($tipo === 'etapa') {
    $sql = "SELECT midia FROM etapa WHERE id_etapa = $id";
} else {
    http_response_code(400);
    exit('Tipo de mídia inválido.');
}

$resultado = $conn->query($sql);
$registro = $resultado->fetch_assoc();

$conn->close();

if (!$registro || $registro['midia'] === null || $registro['midia'] === '') {
    http_response_code(404);
    exit;
}

$midia = $registro['midia'];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->buffer($midia) ?: 'application/octet-stream';

$permitidos = [
    'image/jpeg',
    'image/png',
    'image/webp',
    'image/gif',
    'video/mp4',
    'video/webm',
    'video/ogg'
];

if (!in_array($mime, $permitidos, true)) {
    http_response_code(415);
    exit('Formato de mídia não suportado.');
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen($midia));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=3600');

echo $midia;
?>