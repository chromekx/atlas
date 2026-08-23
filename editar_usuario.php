<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id']) || $_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_GET['id'];
$sql = "SELECT id_usuario, nome, email, preferencia, nivel FROM usuarios WHERE id_usuario = '$id_usuario'";
$resultado = $conn->query($sql);
$usuario = $resultado->fetch_assoc();

if (isset($_POST['']))

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>ATLAS</title>
    <link rel="stylesheet" href="css/editar_usuario.css">
    <link rel="favicon" href="imgs/logoatlas.png" type="image/x-icon">
</head>

<body>
    <header>
        <a class="logo" href="index.php"><img src="imgs/logoatlas.png"></a>

        <nav class="nav-btns">
            <a class="icon" onclick="abrirPesquisa()"><i class="fa-solid fa-magnifying-glass"></i></a>

            <?php if (isset($_SESSION['id'])): ?>
                <div class="perfil" id="perfil">
                    <p>Olá, <?= $_SESSION['nome']; ?></p>
                    <i class="fa-solid fa-caret-up" id="seta"></i>

                    <div class="perfil-options" id="perfil-options">
                        <a class="perfil-option meu-perfil" id="perfil-option" href="meuperfil.php">Meu Perfil</a>
                        <a class="perfil-option config" id="perfil-option" href="configuracoes.php">Configurações</a>
                        <a class="perfil-option sair" id="perfil-option" href="sair.php">Sair</a>
                        <?php if (isset($_SESSION['id']) && $_SESSION['nivel'] == 1): ?>
                            <a class="perfil-option admin" id="perfil-option" href="admin.php">Painel do Administrador</a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <button class="login-btn entrar" onclick="window.location.href='login.php'">Entrar</button>
                <button class="login-btn cadastro" onclick="window.location.href='cadastro.php'">Cadastrar</button>
            <?php endif; ?>

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <form class="login-form" method="POST">
        <div class="text">
            <div class="division">
                <label for="nome">Nome de Usuário:</label>
                <input type="text" id="nome" name="nome" minlength="3" maxlength="100" required value="<?= $usuario['nome']; ?>">
            </div>

            <div class="division">
                <label for="email">E-mail:</label>
                <input type="email" id="email" maxlength="150" name="email" required value="<?= $usuario['email']; ?>">
            </div>

            <div class="division">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" minlength="8" maxlength="255" required>
            </div>

            <div class="division">
                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmarSenha" name="confirmarSenha" required>
            </div>


            <div class="division">
                <label for="preferencias">Preferência:</label>
                <select id="preferencias" name="preferencias" value="<?= $usuario['preferencia']; ?>">
                    <option value="esportes">Esportes</option>
                    <option value="música">Música</option>
                    <option value="cinema">Cinema</option>
                    <option value="livros">Livros</option>
                </select>
            </div>
        </div>

        <div class="division">
            <label for="nivel">Nivel:</label>
        </div>
            <input type="number" id="nivel" name="nivel" required value="<?=$usuario['nivel'];?>">
        </div>

        <button type="submit" name="editar">Editar</button>
    </form>

    <script src="js/header.js"></script>
</body>

</html>