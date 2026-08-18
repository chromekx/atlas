<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$bancodedados = "atlas";
$conn = new mysqli($servidor, $usuario, $senha, $bancodedados);

if ($conn -> connect_error) {
    die('Falha na conexão: ' . $conn -> connect_error);
}
?>