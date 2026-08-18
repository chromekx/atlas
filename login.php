<?php
session_start();

if (isset($_POST['entrar'])) {
    include('conexao.php');

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT id_usuario, email, senha, nivel, nome FROM usuarios WHERE email = '$email'";
    $resultado = $conn->query($sql);
    $usuario = $resultado->fetch_assoc();
    $erro = '';

    if ($email !== $usuario['email'] || !password_verify($senha, $usuario['senha'])) {
        $erro = "<p class='erro'>Email ou senha incorretos.</p>";
        echo $erro;
        //exit();
    } else {
        $_SESSION['id'] = $usuario['id_usuario'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nivel'] = $usuario['nivel'];
        $_SESSION['nome'] = $usuario['nome'];
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
            <a href="index.php"><img class="logo" src="imgs/logoatlas.png"></a>
            <div class="item">
                <p>Início</p>
            </div>
        </nav>

        <nav class="nav-btns">
            <a onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main>
        <form method="POST" class="login-form">
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

                <?php if (!empty($erro)): ?>
                    <p class="erro"><?php echo $erro; ?></p>
                <?php endif; ?>
            </div>
        </form>
    </main>

    <script src="js/index.js"></script>
</body>

</html>