<?php
session_start();
require_once __DIR__ . '/conexao.php';

function h($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Lista de categorias válidas: rótulo exibido => valor gravado em guia.categoria
// (o valor de "Manutenção" está com erro de digitação no banco: "Manuntenção")
$categoriasValidas = [
    'Automóveis' => ['icone' => 'fa-car', 'valor' => 'Automóveis'],
    'Casa' => ['icone' => 'fa-house', 'valor' => 'Casa'],
    'Compras' => ['icone' => 'fa-cart-shopping', 'valor' => 'Compras'],
    'Cuidados' => ['icone' => 'fa-baby', 'valor' => 'Cuidados'],
    'Culinária' => ['icone' => 'fa-bowl-food', 'valor' => 'Culinária'],
    'Educação' => ['icone' => 'fa-graduation-cap', 'valor' => 'Educação'],
    'Finanças' => ['icone' => 'fa-money-bill-wave', 'valor' => 'Finanças'],
    'Jardinagem' => ['icone' => 'fa-seedling', 'valor' => 'Jardinagem'],
    'Limpeza' => ['icone' => 'fa-broom', 'valor' => 'Limpeza'],
    'Manutenção' => ['icone' => 'fa-screwdriver-wrench', 'valor' => 'Manuntenção'],
    'Moda' => ['icone' => 'fa-shirt', 'valor' => 'Moda'],
    'Saúde' => ['icone' => 'fa-heart-pulse', 'valor' => 'Saúde'],
    'Tecnologia' => ['icone' => 'fa-computer', 'valor' => 'Tecnologia'],
    'Trabalho' => ['icone' => 'fa-briefcase', 'valor' => 'Trabalho'],
    'Transporte' => ['icone' => 'fa-bus', 'valor' => 'Transporte'],
];

// A categoria chega pelo rótulo (sem o erro de digitação) e só é aceita se estiver na lista acima.
$categoriaLabel = $_GET['categoria'] ?? '';
$categoriaValida = isset($categoriasValidas[$categoriaLabel]) ? $categoriaLabel : null;
$categoriaValor = $categoriaValida ? $categoriasValidas[$categoriaValida]['valor'] : null;

$busca = trim($_GET['busca'] ?? '');
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 9;
$inicio = ($pagina - 1) * $porPagina;

// Monta o WHERE de acordo com categoria/busca presentes (sem tratar injeção agora, igual ao restante do projeto).
$condicoes = [];
if ($categoriaValor !== null) {
    $condicoes[] = "categoria = '$categoriaValor'";
}

if ($busca !== '') {
    $condicoes[] = "(titulo LIKE '%$busca%' OR descricao LIKE '%$busca%')";
}

$whereSql = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

$sqlTotal = "SELECT COUNT(*) AS total FROM guia $whereSql";
$resultadoTotal = $conn->query($sqlTotal);
$total = (int) mysqli_fetch_assoc($resultadoTotal)['total'];
$totalPaginas = max(1, (int) ceil($total / $porPagina));

$sql = "SELECT g.id_guia, g.titulo, g.descricao, g.categoria, g.dificuldade,
               g.data_criacao, u.nome AS autor
        FROM guia g
        LEFT JOIN usuario u ON u.id_usuario = g.id_usuario
        $whereSql
        ORDER BY g.data_criacao DESC
        LIMIT $porPagina OFFSET $inicio";
$resultado = $conn->query($sql);

// Mantém categoria + busca na URL de paginação e no campo escondido do formulário de busca.
function linkPagina($pagina, $categoriaValida, $busca): string
{
    return 'guias.php?' . http_build_query([
        'categoria' => $categoriaValida ?? '',
        'busca' => $busca,
        'pagina' => $pagina,
    ]);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATLAS - <?= $categoriaValida ? h($categoriaValida) : 'Guias' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/guias.css">
    <link rel="icon" href="imgs_website/logoatlas.png" type="image/png">
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

    <main class="pagina-guias">
        <div class="categ-div">
            <span class="categorias-label">Categorias</span>
            <nav class="categorias-lista">
                <a class="categoria-link <?= $categoriaValida === null ? 'ativa' : '' ?>" href="guias.php">
                    <i class="fa-solid fa-layer-group"></i> Todas
                </a>
                <?php foreach ($categoriasValidas as $label => $dados): ?>
                    <a class="categoria-link <?= $categoriaValida === $label ? 'ativa' : '' ?>" href="guias.php?categoria=<?= urlencode($label) ?>">
                        <i class="fa-solid <?= $dados['icone'] ?>"></i> <?= h($label) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <section class="lado-guias">
            <div class="cabecalho-lista">
                <nav class="breadcrumbs">
                    <a href="index.php">Início</a>
                    <i class="fa-solid fa-chevron-right"></i>
                    <span><?= $categoriaValida ? h($categoriaValida) : 'Todos os guias' ?></span>
                </nav>

                <?php if (isset($_SESSION['id'])): ?>
                    <a class="add-guia" href="cadastrar_guia.php"><i class="fa-solid fa-plus"></i> Adicionar Guia</a>
                <?php else: ?>
                    <a class="add-guia" href="login.php">Entre para adicionar um guia</a>
                <?php endif; ?>
            </div>

            <form class="busca-guias" action="guias.php" method="get">
                <input type="hidden" name="categoria" value="<?= h($categoriaValida ?? '') ?>">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" name="busca" value="<?= h($busca) ?>" placeholder="O que você quer aprender?" aria-label="Buscar guias">
                <button type="submit">Buscar</button>
            </form>

            <div class="titulo-resultados">
                <h1><?= $categoriaValida ? h($categoriaValida) : 'Todos os guias' ?></h1>
                <span class="contador-guias"><?= $total ?> <?= $total === 1 ? 'guia encontrado' : 'guias encontrados' ?></span>
            </div>

            <?php if ($total === 0): ?>
                <div class="estado-vazio">
                    <i class="fa-regular fa-folder-open"></i>
                    <h3>Nenhum guia encontrado</h3>
                    <p>Tente buscar por outro termo ou outra categoria.</p>
                    <a href="guias.php">Limpar filtros</a>
                </div>
            <?php else: ?>
                <div class="guias">
                    <?php while ($guia = $resultado->fetch_assoc()): ?>
                        <a class="guia-card" href="guia.php?id=<?= (int) $guia['id_guia'] ?>" id="guia">
                            <div class="guia-capa">
                                <img
                                    src="exibir_imagem_guia.php?tipo=capa&id=<?= (int) $guia['id_guia'] ?>"
                                    alt="Capa do guia <?= h($guia['titulo']) ?>"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.classList.add('sem-imagem');">
                            </div>
                            <div class="guia-corpo">
                                <span class="tag-dificuldade <?= h(mb_strtolower($guia['dificuldade'], 'UTF-8')) ?>"><?= h($guia['dificuldade']) ?></span>
                                <h3><?= h($guia['titulo']) ?></h3>
                                <p><?= h(mb_strimwidth(trim($guia['descricao']), 0, 110, '…', 'UTF-8')) ?></p>
                                <div class="guia-meta">
                                    <span><i class="fa-regular fa-user"></i> <?= h($guia['autor'] ?: 'Comunidade ATLAS') ?></span>
                                    <span><i class="fa-regular fa-calendar"></i> <?= h(date('d/m/Y', strtotime($guia['data_criacao']))) ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>

                <?php if ($totalPaginas > 1): ?>
                    <nav class="paginacao" aria-label="Paginação">
                        <?php if ($pagina > 1): ?>
                            <a href="<?= h(linkPagina($pagina - 1, $categoriaValida, $busca)) ?>" aria-label="Página anterior"><i class="fa-solid fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                            <a class="<?= $p === $pagina ? 'ativa' : '' ?>" href="<?= h(linkPagina($p, $categoriaValida, $busca)) ?>"><?= $p ?></a>
                        <?php endfor; ?>
                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="<?= h(linkPagina($pagina + 1, $categoriaValida, $busca)) ?>" aria-label="Próxima página"><i class="fa-solid fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </section>
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
                <a href="suporte.php">Suporte</a>
            </div>
            <div class="footer-section footer-contact">
                <h3>Vamos conversar?</h3>
                <p>Tem uma dúvida ou uma ideia? Fale com a gente.</p>
                <a href="mailto:contato@atlas.com">contato@atlas.com <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Atlas. Feito para ir longe.</p>
            <div><a href="#">Privacidade</a><a href="#">Termos de uso</a></div>
        </div>
    </footer>

    <script src="js/header.js"></script>
    <script src="js/guias.js"></script>
</body>

</html>
<?php $conn->close(); ?>