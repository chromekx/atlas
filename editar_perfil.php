<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT id_usuario, foto, nome, email, preferencia FROM usuario WHERE id_usuario = '{$_SESSION['id']}'";
$resultado = $conn->query($sql);
$usuario = $resultado->fetch_assoc();

if (isset($_POST['editar'])) {
    $novoNome = $_POST['nome'];
    $novoEmail = $_POST['email'];
    $novaSenha = $_POST['senha'];
    $novaPreferencia = $_POST['preferencias'];
    $novaFoto = $usuario['foto'];
    $erro = '';

    // Verifica se uma nova foto foi enviada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $novaFoto = time() . '_' . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], 'imgs_banco/' . $novaFoto);
    }

    if (!empty($novaSenha) && strlen($novaSenha) < 8) {
        $erro = "A senha precisa ter pelo menos 8 caracteres.";
    } else {
        if (!empty($novaSenha)) {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuario SET foto = '$novaFoto', nome = '$novoNome', email = '$novoEmail', preferencia = '$novaPreferencia', senha = '$senhaHash' WHERE id_usuario = " . $_SESSION['id'];
        } else {
            $sql = "UPDATE usuario SET foto = '$novaFoto', nome = '$novoNome', email = '$novoEmail', preferencia = '$novaPreferencia' WHERE id_usuario = " . $_SESSION['id'];
        }
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $query = $conn->query($sql);

        $_SESSION['foto'] = $novaFoto;
        $_SESSION['nome'] = $novoNome;
        $_SESSION['email'] = $novoEmail;
        $_SESSION['senha'] = $senhaHash;
        $_SESSION['preferencia'] = $novaPreferencia;

        if ($query) {
            header('Location: meu_perfil.php');
            exit();
        } else {
            $erro = "Houve um erro ao atualizar o usuário.";
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
    <link rel="stylesheet" href="css/editar_perfil.css">
</head>

<body>
    <header>
        <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>

        <nav class="nav-options">
            <button onclick="window.location.href='cadastro_guias.php'" class="item"><p>Cadastrar guia</p></button>
            <button onclick="window.location.href='item2.php'" class="item"><p>Item 2</p></button>
            <button onclick="window.location.href='item3.php'" class="item"><p>Item 3</p></button>
            <button onclick="window.location.href='item4.php'" class="item"><p>Item 4</p></button>
        </nav>

        <nav class="nav-btns">
            <a class="icon" onclick="abrirPesquisa()"><i class="fa-solid fa-magnifying-glass"></i></a>

            <?php if (isset($_SESSION['id'])): ?>
                <div class="perfil" id="perfil">
                    <p>
                        <img class="foto" src="imgs_banco/<?= !empty($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : 'foto_padrao.png' ?>">
                        Olá, <?= $_SESSION['nome'] ?>
                    </p>
                    <i class="fa-solid fa-caret-up" id="seta"></i>

                    <div class="perfil-options" id="perfil-options">
                        <a class="perfil-option meu-perfil" id="perfil-option" href="meu_perfil.php">Meu Perfil</a>
                        <a class="perfil-option config" id="perfil-option" href="configuracoes.php">Configurações</a>
                        <a class="perfil-option sair" id="perfil-option" href="sair.php">Sair</a>
                        <?php if (isset($_SESSION['id']) && $_SESSION['nivel'] == 1): ?>
                            <a class="perfil-option admin" id="perfil-option" href="admin.php">Painel do Administrador</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <button class="entrar" onclick="window.location.href='login.php'"></button>
            <?php endif; ?>

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main>
        <form class="perfil-form" method="POST" enctype="multipart/form-data" autocomplete="off">
            <h2>Editar Perfil</h2>

            <div class="editar-perfil">
                <div class="text">
                    <div class="division">
                        <label for="nome">Nome de Usuário:</label>
                        <input type="text" id="nome" value="<?= htmlspecialchars($_SESSION['nome']) ?>" name="nome" minlength="3" maxlength="100" autocomplete="off" required>
                    </div>

                    <div class="division">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" name="email" maxlength="150" autocomplete="off" required>
                    </div>

                    <div class="division">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" minlength="8" maxlength="255" autocomplete="new-password">
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

                <button type="submit" name="editar">Salvar Perfil</button>
            </div>
        </form>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <a class="footer-logo" href="index.php" aria-label="Voltar para o início">
                    <img src="imgs_website/logoatlas.png" alt="Atlas">
                </a>
                <p>Ideias, oportunidades e caminhos para você criar sua independência.</p>
                <div class="footer-socials" aria-label="Redes sociais">
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="mailto:contato@atlas.com" aria-label="Enviar e-mail"><i class="fa-regular fa-envelope"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Explorar</h3>
                <a href="#categorias">Categorias</a>
                <a href="#aspectos">Descubra o Atlas</a>
                <a href="meu_perfil.php">Meu perfil</a>
            </div>

            <div class="footer-section footer-contact">
                <h3>Vamos conversar?</h3>
                <p>Tem uma dúvida ou uma ideia? Fale com a gente.</p>
                <a href="mailto:contato@atlas.com">contato@atlas.com <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Atlas. Feito para ir longe.</p>
            <div>
                <a href="#">Privacidade</a>
                <a href="#">Termos de uso</a>
            </div>
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