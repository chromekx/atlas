<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id']) || $_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit();
}

$id_editar = $_GET['id'];
$sql = "SELECT id_usuario, nome, email, preferencia, nivel FROM usuarios WHERE id_usuario = '$id_editar'";
$resultado = $conn->query($sql);
$usuario = $resultado->fetch_assoc();

if (isset($_POST['editar'])) {
    $novoNome = $_POST['nome'];
    $novoEmail = $_POST['email'];
    $novaSenha = $_POST['senha'];
    $novoNivel = $_POST['nivel'];
    $erro = '';

    if (!empty($novaSenha) && strlen($novaSenha) < 8) {
        $erro = "A senha precisa ter pelo menos 8 caracteres.";
    } else {
        if (!empty($novaSenha) && strlen($novaSenha) >= 8) {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome = '$novoNome', email = '$novoEmail', senha = '$senhaHash', nivel = '$novoNivel' WHERE id_usuario = '$id_editar'";
        } else {
            $sql = "UPDATE usuarios SET nome = '$novoNome', email = '$novoEmail', nivel = '$novoNivel' WHERE id_usuario = '$id_editar'";
        }

        $query = $conn->query($sql);
        header('Location: admin.php#id:' . $id_editar);
        exit();
    }
}

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
            <div class="perfil" id="perfil">
                <p>Olá, <?= $_SESSION['nome']; ?></p>
                <i class="fa-solid fa-caret-up" id="seta"></i>

                <div class="perfil-options" id="perfil-options">
                    <a class="perfil-option meu-perfil" id="perfil-option" href="meuperfil.php">Meu Perfil</a>
                    <a class="perfil-option config" id="perfil-option" href="configuracoes.php">Configurações</a>
                    <a class="perfil-option sair" id="perfil-option" href="sair.php">Sair</a>
                    <a class="perfil-option admin" id="perfil-option" href="admin.php">Painel do Administrador</a>
                </div>
            </div>
            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <form class="login-form" method="POST">
        <div class="cadastro">
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
                    <input type="password" id="senha" name="senha" maxlength="255">
                </div>

                <div class="division">
                    <label for="nivel">Nivel:</label>
                    <input type="number" id="nivel" name="nivel" required value="<?= $usuario['nivel']; ?>">
                </div>
            </div>
            <button type="submit" name="editar">Atualizar Usuário</button>
        </div>
    </form>

    <script src="js/header.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (!empty($erro)): ?>
        <script>
            Swal.fire({
                title: "<?= $erro ?>",
                confirmButtonColor: "#006eff",
                padding: '25px',
                confirmButtonText: "Ok",
            })
        </script>
    <?php endif; ?>
</body>

</html>