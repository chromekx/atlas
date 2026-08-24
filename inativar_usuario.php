<?php
session_start();
include "conexao.php";

if (!isset($_SESSION['id']) || $_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit();
} else if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id = $_GET['id'];
$sql = "UPDATE usuarios SET estado = 'Inativo' WHERE id_usuario = '$id'";

if ($conn->query($sql)) {
    header("Location: admin.php#" . $id);
    exit();
}
