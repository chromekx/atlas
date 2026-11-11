<?php
session_start();
require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION['id']) || $_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit();
} else if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id_editar = $_GET['id'];
$sql = "SELECT id_usuario, foto, nome, email, preferencia, nivel FROM usuario WHERE id_usuario = '$id_editar'";
$resultado = $conn->query($sql);
$usuario = $resultado->fetch_assoc();

if (isset($_POST['editar'])) {
    $novoNome = $_POST['nome'];
    $novoEmail = $_POST['email'];
    $novaSenha = $_POST['senha'];
    $novoNivel = $_POST['nivel'];
    $novaFoto = $usuario['foto'];
    $erro = '';

    // Verifica se uma nova foto foi enviada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $novaFoto = time() . '_' . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], 'imgs_perfil/' . $novaFoto);
    }

    if (!empty($novaSenha) && strlen($novaSenha) < 8) {
        $erro = "A senha precisa ter pelo menos 8 caracteres.";
    } else {
        if (!empty($novaSenha)) {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuario SET foto = '$novaFoto', nome = '$novoNome', email = '$novoEmail', senha = '$senhaHash', nivel = '$novoNivel' WHERE id_usuario = '$id_editar'";
        } else {
            $sql = "UPDATE usuario SET foto = '$novaFoto', nome = '$novoNome', email = '$novoEmail', nivel = '$novoNivel' WHERE id_usuario = '$id_editar'";
        }
        $query = $conn->query($sql);

        if ($query) {
            header('Location: admin.php#id:' . $id_editar);
            exit();
        } else {
            $erro = "Houve um erro ao atualizar o usuário.";
        }
    }
}

function h($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
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
    <link rel="stylesheet" href="css/admin_editar_usuario.css">
    <link rel="favicon" href="imgs_website/logoatlas.png" type="image/x-icon">
</head>

<body>
    <header>
        <a class="logo" href="index.php" aria-label="Página inicial">
            <img src="imgs_website/logotipoatlas.png" alt="ATLAS">
        </a>

        <nav class="nav-options" aria-label="Navegação principal">
            <a class="item" href="guias.php">
                <p>Explorar Guias</p>
            </a>

            <?php if (isset($_SESSION['id'])): ?>
                <a class="item" href="cadastrar_guia.php">
                    <p>Cadastrar Guia</p>
                </a>
            <?php endif; ?>

            <a class="item" href="index.php#sobre-atlas">
                <p>Sobre o ATLAS</p>
            </a>

            <a class="item" href="suporte.php">
                <p>Suporte</p>
            </a>
        </nav>

        <nav class="nav-btns" aria-label="Conta">
            <?php if (isset($_SESSION['id'])): ?>

                <div class="perfil" id="perfil">
                    <p>
                        <img class="foto" src="imgs_perfil/<?= !empty($_SESSION['foto']) ? h($_SESSION['foto']) : 'foto_padrao.png' ?>">
                        Olá, <?= h($_SESSION['nome']) ?>
                    </p>

                    <i class="fa-solid fa-caret-up" id="seta"></i>

                    <div class="perfil-options" id="perfil-options">
                        <a class="perfil-option" href="meu_perfil.php">Meu perfil</a>


                        <?php if (($_SESSION['nivel'] ?? '') == 1): ?>
                            <a class="perfil-option" href="admin.php">Painel do administrador</a>
                        <?php endif; ?>

                        <a class="perfil-option" href="sair.php">Sair</a>
                    </div>
                </div>

            <?php else: ?>
                <a class="entrar" href="login.php">Entrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <form class="editar-form" method="POST" enctype="multipart/form-data">
            <h2>Editar Usuário</h2>

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
                        <input type="number" id="nivel" name="nivel" min="1" max="3" required value="<?= $usuario['nivel']; ?>">
                    </div>

                    <div class="division">
                        <label for="foto">Foto</label>
                        <input type="file" id="foto" name="foto" value="<?= $usuario['foto']; ?>">
                    </div>
                </div>
                <button type="submit" name="editar">Atualizar Usuário</button>
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
                <a href="cadastrar_guia.php">Cadastrar Guia</a>
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
                confirmButtonColor: "#006eff",
                padding: '25px',
                confirmButtonText: "Ok",
            })
        </script>
    <?php endif; ?>
</body>

</html>