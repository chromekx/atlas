<?php
include '../../conexao.php';

$id = (int) $_GET['id'];

$sql = "SELECT capa FROM guia WHERE id_guia = $id";
$query = $conn->query($sql);

$guia = $query->fetch_assoc();

$finfo = new finfo(FILEINFO_MIME_TYPE);
$tipo = $finfo->buffer($guia['capa']);

header("Content-Type: $tipo");
header("Content-Length: " . strlen($guia['capa']));

echo $guia['capa'];
exit;
