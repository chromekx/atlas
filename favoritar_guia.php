<?php
session_start();
require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$id_guia = (int) $_GET['id'];
$id_usuario = (int) $_SESSION['id'];

$sql = "SELECT id_guia FROM favoritos
        WHERE id_usuario = '$id_usuario' AND id_guia = '$id_guia'";
$resultado = $conn->query($sql);

$coracao = '';

if ($resultado->num_rows == 0) {
    $sql = "INSERT INTO favoritos (id_guia, id_usuario) VALUES ('$id_guia', '$id_usuario')";
    $resultado = $conn->query($sql);
    $coracao = "Favoritar Guia<i class='fa-regular fa-heart'></i>";
} else {
    $sql = "DELETE FROM favoritos WHERE id_guia = '$id_guia' AND id_usuario = '$id_usuario'";
    $resultado = $conn->query($sql);
    $coracao = "Desfavoritar Guia<i class='fa-solid fa-heart'></i>";
}

header('Location: guia.php?id=' . $id_guia);
return $coracao;
