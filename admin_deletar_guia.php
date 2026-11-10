<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/conexao.php";

$id_guia = $_GET['id_guia'];
$id_usuario = $_GET['id_usuario'];

$sql = "DELETE FROM guia WHERE id_guia = '$id_guia'";
$resultado = $conn->query($sql);

header("Location: admin_guias_usuario.php?id='$id_usuario'");
exit();