<?php
if (isset($_POST['cadastrar'])) {
    include('conexao.php');

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmarSenha'];
    $preferencia = $_POST['preferencias'];
    $foto = $_FILES['foto'];

    if (!empty($_FILES['foto']) && $_FILES['foto']['name'] != '') {
        $foto = time() . '_' . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], 'imgs_banco/' . $foto);
    } else {
        $foto = null;
    }

    $sql = "SELECT email FROM usuario WHERE email = '$email'";
    $buscarEmails = $conn->query($sql);

    if (strlen($senha) < 8) {
        $erro = "A senha deve ter pelo menos 8 caracteres.";
    } else if ($senha !== $confirmarSenha) {
        $erro = "As senhas não são iguais.";
    } else if ($buscarEmails->num_rows > 0) {
        $erro = "Esse email já está cadastrado.";
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        if ($foto != null) {
            $sql = "INSERT INTO usuario (foto, nome, email, senha, preferencia) VALUES ('$foto', '$nome', '$email', '$senhaHash', '$preferencia')";
        } else {
            $sql = "INSERT INTO usuario (foto, nome, email, senha, preferencia) VALUES (null, '$nome', '$email', '$senhaHash', '$preferencia')";
        }
        $cadastro = $conn->query($sql);
        if ($cadastro) {
            header('Location: login.php');
        } else {
            $erro = "Houve um erro ao cadastrar sua conta.";
        }
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
            <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>
        </nav>

        <nav class="nav-btns">
            <a onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main>
        <form class="cadastro-form" method="POST" enctype="multipart/form-data" autocomplete="off">
            <h2>Cadastro de Usuário</h2>

            <div class="logar">
                <p>Já possui uma conta?</p>
                <a href="login.php">Fazer login</a>
            </div>

            <div class="cadastro">
                <div class="text">
                    <div class="division">
                        <label for="nome">Nome de Usuário:</label>
                        <input type="text" id="nome" name="nome" minlength="3" maxlength="100" autocomplete="off" required>
                    </div>

                    <div class="division">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" maxlength="150" name="email" autocomplete="off" required>
                    </div>

                    <div class="division">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" minlength="8" maxlength="255" autocomplete="new-password" required>
                    </div>

                    <div class="division">
                        <label for="confirmar_senha">Confirmar Senha:</label>
                        <input type="password" id="confirmarSenha" name="confirmarSenha" autocomplete="new-password" required>
                    </div>

                    <div class="division">
                        <label for="preferencias">Preferência:</label>
                        <select id="preferencias" name="preferencias">
                            <div class="categorias" id="categorias">
                                <option value="Culinária">Culinária</option>
                                <option value="Casa">Casa</option>
                                <option value="Limpeza">Limpeza</option>
                                <option value="Finanças">Finanças</option>
                                <option value="Trabalho">Trabalho</option>
                                <option value="Educação">Educação</option>
                                <option value="Saúde">Saúde</option>
                                <option value="Automóveis">Automóveis</option>
                                <option value="Transporte">Transporte</option>
                                <option value="Tecnologia">Tecnologia</option>
                                <option value="Jardinagem">Jardinagem</option>
                                <option value="Moda">Moda</option>
                                <option value="Cuidados">Cuidados</option>
                                <option value="Manutenção">Manutenção</option>
                                <option value="Compras">Compras</option>
                            </div>
                        </select>
                    </div>

                    <div class="division">
                        <label for="foto">Foto</label>
                        <input type="file" id="foto" name="foto">
                    </div>
                </div>

                <button type="submit" name="cadastrar">Cadastrar-se</button>
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