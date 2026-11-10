<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/conexao.php";

$sql = "UPDATE usuario SET estado = 'inativo' WHERE id_usuario = " . $_SESSION['id'];
$resultado = $conn->query($sql);

header("Location: login.php");
exit();