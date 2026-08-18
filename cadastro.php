<?php
if (isset($_POST['cadastrar'])) {
    include('conexao.php');

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmarSenha'];
    $preferencia = $_POST['preferencias'];

    $sql = "SELECT email FROM usuarios WHERE email = '$email'";
    $buscarEmails = $conn->query($sql);

    if ($senha !== $confirmarSenha) {
        $erro = "<p class='erro'>As senhas não são iguais.</p>";
    }

    if ($buscarEmails->num_rows > 0) {
        $erro = "<p class='erro'>Esse email já está cadastrado.";
    }

    if (empty($erro)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha, preferencia) VALUES ('$nome', '$email', '$senhaHash', '$preferencia')";
        $cadastro = $conn->query($sql);

        if ($cadastro) {
            header('Location: login.php');
        } else {
            $erro = "<p class='erro'>Houve um erro ao cadastrar sua conta.";
            echo $erro;
        }
    } else {
        echo $erro;
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
    <title>Cadastro - ATLAS</title>
    <link rel="stylesheet" href="css/cadastro.css">
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
        <form class="login-form" method="POST">
            <h2>Cadastro de Usuário</h2>

            <div class="logar">
                <p>Já possui uma conta?</p>
                <a href="login.php">Fazer login</a>
            </div>

            <div class="cadastro">

                <div class="text">
                    <div class="division">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>

                    <div class="division">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="division">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>

                    <div class="division">
                        <label for="confirmar_senha">Confirmar Senha:</label>
                        <input type="password" id="confirmarSenha" name="confirmarSenha" required>
                    </div>

                    <div class="division">
                        <label for="preferencias">Preferências:</label>
                        <select id="preferencias" name="preferencias">
                            <option value="esportes">Esportes</option>
                            <option value="música">Música</option>
                            <option value="cinema">Cinema</option>
                            <option value="livros">Livros</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="cadastrar">Cadastrar-se</button>
            </div>
        </form>
    </main>

    <script>
        import Swal from 'sweetalert2'

        if (<?php empty($erro) == false ?>) {
            Swal.fire({
                title: "<?php echo $erro ?>",
                width: 600,
                padding: "3em",
                color: "#716add",
                background: "#fff url(/images/trees.png)",
                backdrop: `
                #00007a66
                left top
                no-repeat
                `
            });
        }
    </script>

</body>

</html>