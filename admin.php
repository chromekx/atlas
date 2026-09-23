<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id']) || $_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit();
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
                <button class="entrar" onclick="window.location.href='login.php'">Entrar</button>
            <?php endif; ?>

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
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
                        <td id="id:<?= $linha['id_usuario'] ?>"><?= $linha['id_usuario'] ?></td>
                        <td><img class="foto-bd" src="imgs_banco/<?= !empty($linha['foto']) ? htmlspecialchars($linha['foto']) : 'foto_padrao.png' ?>"></td>
                        <td><?= htmlspecialchars($linha['nome']) ?></td>
                        <td><?= htmlspecialchars($linha['email']) ?></td>
                        <td><?= htmlspecialchars($linha['senha']) ?></td>
                        <td><?= htmlspecialchars($linha['preferencia']) ?></td>
                        <td><?= htmlspecialchars($linha['nivel']) ?></td>
                        <td><?= htmlspecialchars($linha['estado']) ?></td>
                        <td><?= htmlspecialchars($linha['data_criacao']) ?></td>
                        <td class="celula-acoes">
                            <div class="acoes-linha">
                                <button class="editar" onclick="window.location.href='editar_admin.php?id=' + <?= $linha['id_usuario'] ?>" title="Editar">
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
    <script src="js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>