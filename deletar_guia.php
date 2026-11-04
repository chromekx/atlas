<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/conexao.php";

$id_usuario = (int) $_SESSION['id'];
$id_guia = isset($_GET['id_guia']) ?? 0;

if ($id_guia == 0) {
    header('Location: meu_perfil.php');
    exit;
}

$sql = "DELETE FROM guia WHERE id_guia = '$id_guia' AND id_usuario = '$id_usuario'";
$resultado = $conn->query($sql);

header("Location: meu_perfil.php");
exit();