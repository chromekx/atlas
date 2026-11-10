<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

include('conexao.php');

$id_usuario = (int)$_SESSION['id'];

$sql = "SELECT id_usuario, foto, nome, email, preferencia, data_criacao FROM usuario WHERE id_usuario = '$id_usuario'";
$query = $conn->query($sql);
$usuario = $query->fetch_assoc();

$preferencias = [
    'Culinária' => ['nome' => 'Culinária', 'icone' => 'fa-bowl-food'],
    'Casa' => ['nome' => 'Casa', 'icone' => 'fa-house'],
    'Limpeza' => ['nome' => 'Limpeza', 'icone' => 'fa-broom'],
    'Finanças' => ['nome' => 'Finanças', 'icone' => 'fa-money-bill-wave'],
    'Trabalho' => ['nome' => 'Trabalho', 'icone' => 'fa-briefcase'],
    'Educação' => ['nome' => 'Educação', 'icone' => 'fa-graduation-cap'],
    'Saúde' => ['nome' => 'Saúde', 'icone' => 'fa-heart-pulse'],
    'Automóveis' => ['nome' => 'Automóveis', 'icone' => 'fa-car'],
    'Transporte' => ['nome' => 'Transporte', 'icone' => 'fa-bus'],
    'Tecnologia' => ['nome' => 'Tecnologia', 'icone' => 'fa-computer'],
    'Jardinagem' => ['nome' => 'Jardinagem', 'icone' => 'fa-seedling'],
    'Moda' => ['nome' => 'Moda', 'icone' => 'fa-shirt'],
    'Cuidados' => ['nome' => 'Cuidados', 'icone' => 'fa-baby'],
    'Manutenção' => ['nome' => 'Manutenção', 'icone' => 'fa-screwdriver-wrench'],
    'Compras' => ['nome' => 'Compras', 'icone' => 'fa-cart-shopping'],
];

$preferenciaUsuario = $preferencias[$usuario['preferencia']] ?? null; // se o valor da esquerda não existir, use null

$meses = [
    1 => 'janeiro',
    2 => 'fevereiro',
    3 => 'março',
    4 => 'abril',
    5 => 'maio',
    6 => 'junho',
    7 => 'julho',
    8 => 'agosto',
    9 => 'setembro',
    10 => 'outubro',
    11 => 'novembro',
    12 => 'dezembro',
];

$membroDesde = null;
if (!empty($usuario['data_criacao'])) {
    $data = new DateTime($usuario['data_criacao']);
    $membroDesde = $data->format('d') . ' de ' . $meses[(int) $data->format('n')] . ' de ' . $data->format('Y');
}

// PAGINAÇÃO
$porPagina = 6;

$paginaGuias = max(1, (int) ($_GET['pagina_guias'] ?? 1));
$paginaFavoritos = max(1, (int) ($_GET['pagina_favoritos'] ?? 1));
$paginaHistorico = max(1, (int) ($_GET['pagina_historico'] ?? 1));

$inicioGuias = ($paginaGuias - 1) * $porPagina;
$inicioFavoritos = ($paginaFavoritos - 1) * $porPagina;
$inicioHistorico = ($paginaHistorico - 1) * $porPagina;


// guias criados
$sql = "SELECT COUNT(*) AS total FROM guia WHERE id_usuario = '$id_usuario'";
$totalGuias = $conn->query($sql)->fetch_assoc()['total'];
$totalPaginasGuias = ceil($totalGuias / $porPagina);

$sql = "SELECT id_guia, id_usuario, titulo, descricao, categoria, dificuldade, data_criacao
        FROM guia
        WHERE id_usuario = '$id_usuario'
        ORDER BY data_criacao DESC
        LIMIT $porPagina OFFSET $inicioGuias";
$resultadoCriadas = $conn->query($sql);


// favoritos
$sql = "SELECT COUNT(*) AS total FROM favoritos WHERE id_usuario = '$id_usuario'";
$totalFavoritos = $conn->query($sql)->fetch_assoc()['total'];
$totalPaginasFavoritos = ceil($totalFavoritos / $porPagina);

$sql = "SELECT f.id_usuario, g.id_guia, g.id_usuario, g.titulo, g.descricao,
               g.categoria, g.dificuldade, g.data_criacao, u.nome AS autor
        FROM favoritos f
        LEFT JOIN guia g ON f.id_guia = g.id_guia
        LEFT JOIN usuario u ON g.id_usuario = u.id_usuario
        WHERE f.id_usuario = '$id_usuario'
        LIMIT $porPagina OFFSET $inicioFavoritos";
$resultadoFavoritas = $conn->query($sql);


// historico
$sql = "SELECT h.data_visualizacao, h.id_usuario, g.id_guia, g.id_usuario,
               g.titulo, g.descricao, g.categoria, g.dificuldade,
               g.data_criacao, u.nome AS autor
        FROM historico_guias h
        LEFT JOIN guia g ON h.id_guia = g.id_guia
        LEFT JOIN usuario u ON g.id_usuario = u.id_usuario
        WHERE h.id_usuario = '$id_usuario'
        ORDER BY h.data_visualizacao DESC
        LIMIT 6";
$resultadoHistorico = $conn->query($sql);

function h($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
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
    <title>Meu Perfil - ATLAS</title>
    <link rel="stylesheet" href="css/meu_perfil.css">
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

    <main>
        <section class="cabecalho-perfil">
            <img class="foto-grande" src="imgs_perfil/<?= !empty($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : 'foto_padrao.png' ?>">

            <div class="info-perfil">
                <h1><?= htmlspecialchars($_SESSION['nome']) ?></h1>
                <p class="email-perfil"><?= htmlspecialchars($_SESSION['email']) ?></p>

                <div class="detalhes-perfil">
                    <?php if ($membroDesde): ?>
                        <span class="data-pref"><i class="fa-solid fa-calendar-days"></i> Usuário desde <?= $membroDesde ?></span>
                    <?php endif; ?>

                    <?php if ($preferenciaUsuario): ?>
                        <span class="data-pref"><i class="fa-solid <?= htmlspecialchars($preferenciaUsuario['icone']) ?>"></i> Gosta de <?= htmlspecialchars($preferenciaUsuario['nome']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <a class="botao-primario" href="editar_perfil.php">
                <i class="fa-solid fa-pen"></i> Editar perfil
            </a>
        </section>

        <nav class="abas-perfil" id="abas-perfil">
            <button class="aba-perfil ativa" data-aba="guias">Guias</button>
            <button class="aba-perfil" data-aba="favoritos">Favoritos</button>
            <button class="aba-perfil" data-aba="historico">Histórico</button>
        </nav>

        <section class="painel-perfil" id="painel-guias">
            <div class="lista-guias-perfil">
                <?php if ($resultadoCriadas->num_rows < 1) { ?>
                    <div class="estado-vazio">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <p>Você ainda não publicou nenhum guia.</p>
                        <a class="botao-primario" href="cadastro_guias.php">Cadastrar Guia</a>
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
                                        <button type="submit" onclick="deletarGuia(<?= $guia_criada['id_guia'] ?>)" class="acao-icone excluir" title="Excluir guia" aria-label="Excluir guia"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php } ?>
                <?php } ?>
            </div>

            <nav class="paginacao">
                <?php if ($paginaGuias > 1): ?>
                    <a href="?pagina_guias=<?= $paginaGuias - 1 ?>&pagina_favoritos=<?= $paginaFavoritos ?>&pagina_historico=<?= $paginaHistorico ?>&aba=guias">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPaginasGuias; $p++): ?>
                    <a href="?pagina_guias=<?= $p ?>&pagina_favoritos=<?= $paginaFavoritos ?>&pagina_historico=<?= $paginaHistorico ?>&aba=guias"
                        class="<?= $p == $paginaGuias ? 'ativa' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($paginaGuias < $totalPaginasGuias): ?>
                    <a href="?pagina_guias=<?= $paginaGuias + 1 ?>&pagina_favoritos=<?= $paginaFavoritos ?>&pagina_historico=<?= $paginaHistorico ?>&aba=guias">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </nav>
        </section>

        <section class="painel-perfil" id="painel-favoritos" hidden>
            <div class="lista-guias-perfil">
                <?php if ($resultadoFavoritas->num_rows == 0) { ?>
                    <div class="estado-vazio">
                        <i class="fa-solid fa-heart"></i>
                        <p>Você ainda não favoritou nenhum guia.</p>
                        <a class="botao-primario" href="guias.php">Visitar Guias</a>
                    </div>
                <?php } else { ?>
                    <?php while ($guia_favorita = $resultadoFavoritas->fetch_assoc()) { ?>
                        <article class="guia-card">
                            <div class="guia-capa">
                                <img
                                    src="exibir_imagem_guia.php?tipo=capa&id=<?= (int) $guia_favorita['id_guia'] ?>"
                                    alt="Capa do guia <?= h($guia_favorita['titulo']) ?>"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.classList.add('sem-imagem');">
                            </div>
                            <div class="guia-corpo">
                                <div class="guia-meta1">
                                    <span class="tag-dificuldade <?= h(mb_strtolower($guia_favorita['dificuldade'], 'UTF-8')) ?>"><?= h($guia_favorita['dificuldade']) ?></span>
                                    <a class="acao-icone editar" title="Visitar guia" href="guia.php?id=<?= (int) $guia_favorita['id_guia'] ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                </div>
                                <h3><?= h($guia_favorita['titulo']) ?></h3>
                                <p><?= h(mb_strimwidth(trim($guia_favorita['descricao']), 0, 110, '…', 'UTF-8')) ?></p>
                                <div class="guia-meta">
                                    <span><i class="fa-regular fa-user"></i> <?= h($guia_favorita['autor'] ?: 'Comunidade ATLAS') ?></span>
                                    <span><i class="fa-regular fa-calendar"></i><?= h(date('d/m/Y', strtotime($guia_favorita['data_criacao']))) ?></span>
                                    <div class="acoes-guia">
                                        <button type="submit" onclick="desfavoritarGuia(<?= $guia_favorita['id_guia'] ?>)" class="acao-icone desfavoritar" title="Remover dos favoritos" aria-label="Remover dos favoritos"><i class="fa-solid fa-heart-crack"></i></button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php } ?>
                <?php } ?>
            </div>

            <nav class="paginacao">
                <?php if ($paginaFavoritos > 1): ?>
                    <a href="?pagina_guias=<?= $paginaGuias ?>&pagina_favoritos=<?= $paginaFavoritos - 1 ?>&pagina_historico=<?= $paginaHistorico ?>&aba=favoritos">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPaginasFavoritos; $p++): ?>
                    <a href="?pagina_guias=<?= $paginaGuias ?>&pagina_favoritos=<?= $p ?>&pagina_historico=<?= $paginaHistorico ?>&aba=favoritos"
                        class="<?= $p == $paginaFavoritos ? 'ativa' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($paginaFavoritos < $totalPaginasFavoritos): ?>
                    <a href="?pagina_guias=<?= $paginaGuias ?>&pagina_favoritos=<?= $paginaFavoritos + 1 ?>&pagina_historico=<?= $paginaHistorico ?>&aba=favoritos">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </nav>
        </section>

        <section class="painel-perfil" id="painel-historico" hidden>
            <div class="lista-guias-perfil">
                <?php if ($resultadoHistorico->num_rows == 0) { ?>
                    <div class="estado-vazio">
                        <i class="fa-solid fa-eye"></i>
                        <p>Você ainda não visitou nenhum guia.</p>
                        <a class="botao-primario" href="guias.php">Visitar Guias</a>
                    </div>
                <?php } else { ?>
                    <?php while ($guia_historico = $resultadoHistorico->fetch_assoc()) { ?>
                        <article class="guia-card">
                            <div class="guia-capa">
                                <img
                                    src="exibir_imagem_guia.php?tipo=capa&id=<?= (int) $guia_historico['id_guia'] ?>"
                                    alt="Capa do guia <?= h($guia_historico['titulo']) ?>"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.classList.add('sem-imagem');">
                            </div>
                            <div class="guia-corpo">
                                <div class="guia-meta1">
                                    <span class="tag-dificuldade <?= h(mb_strtolower($guia_historico['dificuldade'], 'UTF-8')) ?>"><?= h($guia_historico['dificuldade']) ?></span>
                                    <a class="acao-icone editar" title="Visitar guia" href="guia.php?id=<?= (int) $guia_historico['id_guia'] ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                </div>
                                <h3><?= h($guia_historico['titulo']) ?></h3>
                                <p><?= h(mb_strimwidth(trim($guia_historico['descricao']), 0, 110, '…', 'UTF-8')) ?></p>
                                <div class="guia-meta">
                                    <span><i class="fa-regular fa-user"></i> <?= h($guia_historico['autor'] ?: 'Comunidade ATLAS') ?></span>
                                    <span><i class="fa-regular fa-calendar"></i><?= h(date('d/m/Y', strtotime($guia_historico['data_visualizacao']))) ?></span>
                                    <div class="acoes-guia">
                                        <button type="submit" onclick="excluirGuia(<?= $guia_historico['id_guia'] ?>)" class="acao-icone remover" title="Remover do histórico" aria-label="Remover do histórico"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>
        <button onclick="deletarPerfil()" class="btn-denunciar">Deletar perfil<i class="fa-solid fa-trash"></i></button>
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
    <script src="js/meuperfil.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deletarGuia(id) {
            Swal.fire({
                title: "Tem certeza que deseja deletar esta guia?",
                padding: "25px",
                color: '#000',
                showCancelButton: true,
                confirmButtonColor: "#1bb155",
                cancelButtonColor: "#cc2828",
                confirmButtonText: "Sim, deletar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'deletar_guia.php?id=' + id;
                }
            });
        }

        function excluirGuia(id) {
            Swal.fire({
                title: "Tem certeza que deseja remover esta guia do histórico?",
                padding: "25px",
                color: '#000',
                showCancelButton: true,
                confirmButtonColor: "#1bb155",
                cancelButtonColor: "#cc2828",
                confirmButtonText: "Sim, remover",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'excluir_guia_hist.php?id=' + id;
                }
            });
        }

        function desfavoritarGuia(id) {
            Swal.fire({
                title: "Tem certeza que deseja desfavoritar esta guia?",
                padding: "25px",
                color: '#000',
                showCancelButton: true,
                confirmButtonColor: "#1bb155",
                cancelButtonColor: "#cc2828",
                confirmButtonText: "Sim, desfavoritar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'desfavoritar_guia.php?id=' + id;
                }
            });
        }

        function deletarPerfil(id) {
            Swal.fire({
                title: "Tem certeza que deseja deletar seu perfil?",
                padding: "25px",
                color: '#000',
                showCancelButton: true,
                cancelButtonColor: "#263783",
                confirmButtonColor: "#cc2828",
                confirmButtonText: "Sim, deletar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'deletar_perfil.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>