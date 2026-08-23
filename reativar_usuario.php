<?php
session_start();
include "conexao.php";

$id = $_GET['id'];
$sql = "UPDATE usuarios SET estado = 'Ativo' WHERE id_usuario = '$id'";

if ($conn->query($sql)) {
    header("Location: admin.php#" . $id );
    exit();
}
?>