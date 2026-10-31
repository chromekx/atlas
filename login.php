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

    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <a class="footer-logo" href="index.php" aria-label="Voltar para o início"><img src="imgs_website/logoatlas.png" alt="ATLAS"></a>
                <p>Ideias, oportunidades e caminhos para você criar sua independência.</p>
                <div class="footer-socials" aria-label="Redes sociais">
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="mailto:contato@atlas.com" aria-label="Enviar e-mail"><i class="fa-regular fa-envelope"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Explorar</h3>
                <a href="index.php">Início</a>
                <a href="cadastro_guias.php">Cadastrar Guia</a>
                <a href="suporte.php">Suporte
                </a>
            </div>
            <div class="footer-section footer-contact">
                <h3>Vamos conversar?</h3>
                <p>Tem uma dúvida ou uma ideia? Fale com a gente.</p><a href="mailto:contato@atlas.com">contato@atlas.com <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Atlas. Feito para ir longe.</p>
            <div><a href="#">Privacidade</a><a href="#">Termos de uso</a></div>
        </div>
    </footer>

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