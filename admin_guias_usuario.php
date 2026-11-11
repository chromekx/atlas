<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/conexao.php';
$id_usuario = (int) $_GET['id'];
$sql = "SELECT nome FROM usuario WHERE id_usuario = '$id_usuario'";
$resultadoUsuario = $conn->query($sql);
$usuario = $resultadoUsuario->fetch_assoc();

$porPagina = 6;
$pagina = max(1, (int) ($_GET['pagina_guias'] ?? 1));
$inicioGuia = ($pagina - 1) * $porPagina;

$sql = "SELECT COUNT(*) AS total FROM guia WHERE id_usuario = '$id_usuario'";
$totalGuias = $conn->query($sql)->fetch_assoc()['total'];
$totalPaginasGuias = ceil($totalGuias / $porPagina);

$sql = "SELECT id_guia, id_usuario, titulo, descricao, categoria, dificuldade, data_criacao
        FROM guia
        WHERE id_usuario = '$id_usuario'
        ORDER BY data_criacao DESC
        LIMIT $porPagina OFFSET $inicioGuia";
$resultadoCriadas = $conn->query($sql);

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
    <link rel="stylesheet" href="css/admin_guias_usuario.css">
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
        <section class="cabecalho-pagina">
            <h1>Guias cadastradas por <?= h($usuario['nome']) ?></h1>
        </section>

        <section class="painel-perfil" id="painel-guias">
            <div class="lista-guias-perfil">
                <?php if ($resultadoCriadas->num_rows < 1) { ?>
                    <div class="estado-vazio">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <p><?= h($usuario['nome']) ?> ainda não publicou nenhum guia.</p>
                    </div>
                <?php } else { ?>
                    <?php while ($guia_criada = $resultadoCriadas->fetch_assoc()) { ?>
                        <article class="guia-card">
                            <div class="guia-capa">
                                <img
                                    src="exibir_imagem_guia.php?tipo=capa&id=<?= (int) $guia_criada['id_guia'] ?>"
                                    alt="Capa do guia <?= h($guia_criada['titulo']) ?>"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.classList.add('sem-imagem');">
                            </div>
                            <div class="guia-corpo">
                                <div class="guia-meta1">
                                    <span class="tag-dificuldade <?= h(mb_strtolower($guia_criada['dificuldade'], 'UTF-8')) ?>"><?= h($guia_criada['dificuldade']) ?></span>
                                    <a class="acao-icone editar" title="Visitar guia" href="guia.php?id=<?= (int) $guia_criada['id_guia'] ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                </div>
                                <h3><?= h($guia_criada['titulo']) ?></h3>
                                <p><?= h(mb_strimwidth(trim($guia_criada['descricao']), 0, 110, '…', 'UTF-8')) ?></p>
                                <div class="guia-meta">
                                    <span><i class="fa-regular fa-calendar"></i><?= h(date('d/m/Y', strtotime($guia_criada['data_criacao']))) ?></span>
                                    <div class="acoes-guia">
                                        <a class="acao-icone editar" href="editar_guia.php?id=<?= (int) $guia_criada['id_guia'] ?>" title="Editar guia" aria-label="Editar guia"><i class="fa-solid fa-pen"></i></a>
                                        <button onclick="deletarGuia(<?= $guia_criada['id_guia'] ?>, <?= $guia_criada['id_usuario'] ?>)" class="acao-icone excluir" title="Excluir guia" aria-label="Excluir guia"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                        </article>
                    <?php } ?>
                <?php } ?>
            </div>

            <nav class="paginacao">
                <?php if ($pagina > 1): ?>
                    <a href="?id=<?= $id_usuario ?>&pagina_guias=<?= $pagina - 1 ?>"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPaginasGuias; $p++): ?>
                    <a href="?id=<?= $id_usuario ?>&pagina_guias=<?= $p ?>" class="<?= $p == $pagina ? 'ativa' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginasGuias): ?><a href="?id=<?= $id_usuario ?>&pagina_guias=<?= $pagina + 1 ?>"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </nav>
        </section>

        <div class="voltar-guias">
            <a href="admin.php#<?= $id_usuario ?>"><i class="fa-solid fa-arrow-left"></i>Voltar para o Painel de Administradores</a>
        </div>
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
    <script>
        function deletarGuia(id_guia, id_usuario) {
            Swal.fire({
                title: "Tem certeza que deseja deletar a guia de <?= $usuario['nome'] ?>?",
                padding: "25px",
                color: '#000',
                showCancelButton: true,
                confirmButtonColor: "#1bb155",
                cancelButtonColor: "#cc2828",
                confirmButtonText: "Sim, deletar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = window.location.href = 'deletar_guia.php?id=' + id_guia + '&id_usuario=' + id_usuario;;
                }
            });
        }
    </script>
</body>

</html>