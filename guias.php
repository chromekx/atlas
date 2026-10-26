<?php
session_start();
require_once __DIR__ . '/conexao.php';

$categoria = $_GET['categoria'];
$busca = $_GET['busca'] ?? '';
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = 9;
$inicio = ($pagina - 1) * $porPagina;

$buscaSQL = $conn->real_escape_string($busca);

// Contar o total de guias
$sql = "SELECT COUNT(*) AS total FROM guia WHERE titulo LIKE '%$buscaSQL%' OR descricao LIKE '%$buscaSQL%'";

$resultado = $conn->query($sql);
$total = $resultado->fetch_assoc()['total'];

$totalPaginas = max(1, ceil($total / $porPagina));

// Buscar os guias da página atual
$sql = "SELECT id_guia, titulo, descricao, categoria, dificuldade, data_criacao FROM guia WHERE titulo 
        ORDER BY data_criacao DESC LIMIT $porPagina OFFSET $inicio";
$resultado = $conn->query($sql);

function h($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function linkPagina($pagina): string
{
    return 'guias.php?' . http_build_query([
        'busca' => $_GET['busca'] ?? '',
        'pagina' => $pagina
    ]);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATLAS - Guias</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/guias.css">
    <link rel="icon" href="imgs_website/logoatlas.png" type="image/png">
</head>

<body>
    <header>
        <a class="logo" href="index.php" aria-label="Página inicial"><img src="imgs_website/logotipoatlas.png" alt="ATLAS"></a>
        <nav class="nav-options" aria-label="Navegação principal">
            <a class="item" href="guias.php">
                <p>Explorar guias</p>
            </a>
            <?php if (isset($_SESSION['id'])): ?>
                <a class="item" href="cadastro_guias.php">
                    <p>Cadastrar guia</p>
                </a>
            <?php endif; ?>
            <a class="item" href="index.php#aspectos">
                <p>Sobre o ATLAS</p>
            </a>
        </nav>
        <nav class="nav-btns" aria-label="Conta">
            <?php if (isset($_SESSION['id'])): ?>
                <div class="perfil" id="perfil">
                    <p>
                        <?php if (!empty($_SESSION['foto'])): ?>
                            <img class="foto" src="imgs_perfil/<?= h($_SESSION['foto']) ?>" alt="">
                        <?php endif; ?>
                        Olá, <?= h($_SESSION['nome'] ?? 'usuário') ?>
                    </p>
                    <i class="fa-solid fa-caret-down" id="seta"></i>
                    <div class="perfil-options" id="perfil-options">
                        <a class="perfil-option" href="meu_perfil.php">Meu perfil</a>
                        <a class="perfil-option" href="configuracoes.php">Configurações</a>
                        <?php if (($_SESSION['nivel'] ?? '') == 1): ?><a class="perfil-option" href="admin.php">Painel do administrador</a><?php endif; ?>
                        <a class="perfil-option" href="sair.php">Sair</a>
                    </div>
                </div>
            <?php else: ?>
                <a class="entrar" href="login.php">Entrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="pagina-guias">
        <section class="hero-guias">
            <div class="hero-conteudo">
                <span class="etiqueta-hero"><i class="fa-solid fa-compass"></i> Aprenda fazendo</span>
                <h1>Conhecimento que vira prática.</h1>
                <p>Encontre guias passo a passo criados por pessoas que querem compartilhar o que sabem.</p>
                <form class="busca-guias" action="guias.php" method="get">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" name="busca" value="<?= h($busca) ?>" placeholder="O que você quer aprender?" aria-label="Buscar guias">
                    <button type="submit">Buscar</button>
                </form>
            </div>
            <div class="hero-ilustracao" aria-hidden="true">
                <div class="hero-circulo"><i class="fa-solid fa-lightbulb"></i></div>
                <div class="hero-card hero-card-um"><i class="fa-solid fa-list-check"></i><span>Passo a passo</span></div>
                <div class="hero-card hero-card-dois"><i class="fa-solid fa-wand-magic-sparkles"></i><span>Novas habilidades</span></div>
                <span class="hero-ponto ponto-um"></span><span class="hero-ponto ponto-dois"></span>
            </div>
        </section>

        <section class="resultados-guias">
            <div class="titulo-secao titulo-resultados">
                <div>
                    <h2><?= $categoria ?></h2>
                </div>
                <span class="contador-guias"><?= $total ?> <?= $total === 1 ? 'guia encontrado' : 'guias encontrados' ?></span>
            </div>

            <?php if ($total === 0): ?>
                <div class="estado-vazio">
                    <i class="fa-regular fa-folder-open"></i>
                    <h3>Nenhum guia encontrado</h3>
                    <p>Tente buscar por outro termo.</p>
                    <a href="guias.php">Limpar filtros</a>
                </div>
            <?php else: ?>
                <div class="grade-guias">
                    <?php while ($guia = $resultado->fetch_assoc()): ?>
                        <a class="cartao-guia" href="guia.php?id=<?= (int)$guia['id_guia'] ?>">
                            <div class="imagem-cartao">
                                <img src="exibir_midia_guia.php?tipo=capa&id=<?= (int)$guia['id_guia'] ?>" alt="" loading="lazy"
                                    onerror="this.style.display='none';this.parentElement.classList.add('sem-imagem')">
                                <span class="categoria-selo"><?= h($guia['categoria']) ?></span>
                                <span class="icone-sem-imagem"><i class="fa-solid fa-book-open"></i></span>
                            </div>
                            <div class="conteudo-cartao">
                                <div class="metadados-cartao">
                                    <span><i class="fa-solid fa-signal"></i> <?= h($guia['dificuldade']) ?></span>
                                    <span><i class="fa-regular fa-calendar"></i> <?= h(date('d/m/Y', strtotime($guia['data_criacao']))) ?></span>
                                </div>
                                <h3><?= h($guia['titulo']) ?></h3>
                                <p><?= h(mb_strimwidth(trim($guia['descricao']), 0, 118, '…', 'UTF-8')) ?></p>
                                <div class="rodape-cartao">
                                    <span class="autor-guia"><i class="fa-regular fa-user"></i> <?= h($guia['autor'] ?: 'Comunidade ATLAS') ?></span>
                                    <span class="abrir-guia" aria-label="Abrir guia"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
                <?php if ($totalPaginas > 1): ?>
                    <nav class="paginacao" aria-label="Paginação">
                        <?php if ($pagina > 1): ?><a href="<?= h(linkPagina($pagina - 1)) ?>" aria-label="Página anterior"><i class="fa-solid fa-chevron-left"></i></a><?php endif; ?>
                        <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                            <a class="<?= $p === $pagina ? 'ativa' : '' ?>" href="<?= h(linkPagina($p)) ?>"><?= $p ?></a>
                        <?php endfor; ?>
                        <?php if ($pagina < $totalPaginas): ?><a href="<?= h(linkPagina($pagina + 1)) ?>" aria-label="Próxima página"><i class="fa-solid fa-chevron-right"></i></a><?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <?php if (!isset($_SESSION['id'])): ?>
            <section class="convite-criar">
                <div><span class="sobretitulo">COMPARTILHE O QUE SABE</span>
                    <h2>Uma boa ideia merece ser compartilhada.</h2>
                    <p>Entre na sua conta para publicar um guia e ajudar outras pessoas.</p>
                </div>
                <a href="login.php">Entrar para publicar <i class="fa-solid fa-arrow-right"></i></a>
            </section>
        <?php else: ?>
            <section class="convite-criar">
                <div><span class="sobretitulo">SUA VEZ DE ENSINAR</span>
                    <h2>Transforme sua experiência em um guia.</h2>
                    <p>Organize suas dicas em etapas simples e compartilhe com a comunidade.</p>
                </div>
                <a href="cadastro_guias.php">Criar um guia <i class="fa-solid fa-arrow-right"></i></a>
            </section>
        <?php endif; ?>
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
                <h3>Explorar</h3><a href="guias.php">Guias</a><a href="guias.php">Categorias</a><a href="meu_perfil.php">Meu perfil</a>
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
</body>

</html>
<?php
$conn->close();
?>