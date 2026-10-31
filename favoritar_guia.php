<?php
session_start();

require_once __DIR__ . "/conexao.php";

$id_guia = $_GET['id'];

$sql = "INSERT INTO favoritos";