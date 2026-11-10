<?php
session_start();
require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION['id']) || $_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit();
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
    <link rel="stylesheet" href="css/admin.css">
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
                <a class="item" href="cadastro_guias.php">
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

    <h1 class="titulo"> Painel dos Administradores </h1>

    <div class="forms">
        <form class="form-pesquisar" method="POST">
            <label for="barraPesquisa">Pesquisar por ID</label>
            <input class="barra-pesquisa" type="number" min="1" id="barraPesquisa" name="barraPesquisa" placeholder="Digite o ID do usuário">
            <div class="botoes">
                <button class="btn-pesquisar" id="btnPesquisar" name="pesquisar">Pesquisar</button>
                <button class="btn-resetar" id="btnResetar" name="resetar">Resetar</button>
            </div>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Senha</th>
                <th>Preferência</th>
                <th>Nível</th>
                <th>Estado</th>
                <th>Data de Criação</th>
                <th>Ações</th>
            </tr>

            <?php
            if (!isset($_POST['pesquisar'])) {
                $sql = "SELECT id_usuario, foto, nome, email, senha, preferencia, nivel, estado, data_criacao FROM usuario";
            } else {
                $pesquisa = $_POST['barraPesquisa'];
                $sql = "SELECT id_usuario, foto, nome, email, senha, preferencia, nivel, estado, data_criacao FROM usuario WHERE id_usuario = '$pesquisa'";
            }

            $resultado = $conn->query($sql);

            if ($resultado->num_rows <= 0) {
                $erro = 'Não há usuários cadastrados.';
            } else {
                while ($linha = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td id="<?= $linha['id_usuario'] ?>"><?= $linha['id_usuario'] ?></td>
                        <td><img class="foto-bd" src="imgs_perfil/<?= !empty($linha['foto']) ? htmlspecialchars($linha['foto']) : 'foto_padrao.png' ?>"></td>
                        <td><?= htmlspecialchars($linha['nome']) ?></td>
                        <td><?= htmlspecialchars($linha['email']) ?></td>
                        <td><?= htmlspecialchars($linha['senha']) ?></td>
                        <td><?= htmlspecialchars($linha['preferencia']) ?></td>
                        <td><?= htmlspecialchars($linha['nivel']) ?></td>
                        <td><?= htmlspecialchars($linha['estado']) ?></td>
                        <td><?= htmlspecialchars($linha['data_criacao']) ?></td>
                        <td class="celula-acoes">
                            <div class="acoes-linha">
                                <button class="guias" onclick="window.location.href='admin_guias_usuario.php?id=' + <?= $linha['id_usuario'] ?>" title="Acessar guias">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </button>
                                <button class="editar" onclick="window.location.href='admin_editar_usuario.php?id=' + <?= $linha['id_usuario'] ?>" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <?php if ($linha['estado'] == "ativo") { ?>
                                    <button class='inativar' onclick="inativarUsuario(<?= $linha['id_usuario'] ?>)" title="Inativar">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                <?php } else { ?>
                                    <button class='reativar' onclick="reativarUsuario(<?= $linha['id_usuario'] ?>)" title="Reativar">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
            <?php }
            } ?>
        </table>

        <?php if (!empty($erro)) {
            echo $erro;
        } ?>
    </div>

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
    <script src="js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>