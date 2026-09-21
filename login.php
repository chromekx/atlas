<?php
session_start();

if (isset($_POST['entrar'])) {
    include('conexao.php');

    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $erro = '';

    $sql = "SELECT id_usuario, foto, nome, email, preferencia, senha, nivel, estado FROM usuario WHERE email = '$email'";
    $resultado = $conn->query($sql);
    $usuario = $resultado->fetch_assoc();

    if (!$usuario) {
        $erro = "Email ou senha incorretos.";
    } else if (!password_verify($senha, $usuario['senha'])) {
        $erro = "Email ou senha incorretos.";
    } else if ($usuario['estado'] == 'inativo') {
        $erro = "Este usuário está inativo.";
    } else {
        $_SESSION['id'] = $usuario['id_usuario'];
        $_SESSION['foto'] = $usuario['foto'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['preferencia'] = $usuario['preferencia'];
        $_SESSION['nivel'] = $usuario['nivel'];
        $_SESSION['estado'] = $usuario['estado'];
        header('Location: index.php');
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
    <title>Iniciar Sessão - ATLAS</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <header>
        <nav class="nav-options">
            <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>
        </nav>

        <nav class="nav-btns">
            <a onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main>
        <form class="login-form" method="POST">
            <h2>Iniciar Sessão</h2>

            <div class="cadastrar">
                <p>Não possui uma conta?</p>
                <a href="cadastro.php">Cadastre-se</a>
            </div>

            <div class="login">
                <div class="text">
                    <div class="division">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="division">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>
                </div>
                <button type="submit" name="entrar">Entrar</button>

            </div>
        </form>
    </main>

    <script src="js/header.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (!empty($erro)): ?>
        <script>
            Swal.fire({
                title: "<?= $erro ?>",
                confirmButtonColor: "#263783",
                padding: '25px',
                confirmButtonText: "Ok",
            })
        </script>
    <?php endif; ?>
</body>

</html>