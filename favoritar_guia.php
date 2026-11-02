<?php
session_start();
require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: guias.php');
    exit;
}

$id_usuario = (int) $_SESSION['id'];
$id_guia = (int) $_POST['id_guia'];

if ($id_guia < 1) {
    header('Location: guias.php');
    exit;
}

$sql = "SELECT id_guia
        FROM favoritos
        WHERE id_usuario = '$id_usuario' AND id_guia = '$id_guia'";
$resultado = $conn->query($sql);
$favoritada = $resultado->num_rows > 0;

if (!$favoritada) {
    $sql = "INSERT INTO favoritos (id_usuario, id_guia)
            VALUES ('$id_usuario', '$id_guia')";
    $resultado = $conn->query($sql);
} else {
    $sql = "DELETE FROM favoritos
            WHERE id_usuario = '$id_usuario' AND id_guia = '$id_guia'";
    $resultado = $conn->query($sql);
}

header('Location: guia.php?id=' . $id_guia);
exit();