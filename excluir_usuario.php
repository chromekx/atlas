<?php
include "conexao.php";

$id = $_GET['id'];
$sql = "DELETE FROM usuarios WHERE id_usuario = '$id'";

if ($conn->query($sql)) {
    header("Location: admin.php");
    exit();
}
?>