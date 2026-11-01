<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/conexao.php";

$sql = "SELECT id_usuario, foto, nome, email, preferencia, data_criacao FROM usuario WHERE id_usuario = " . $_SESSION['id'];
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

$sql = "SELECT id_guia, data_favorito FROM favoritos WHERE id_usuario = " . $_SESSION['id'];
$resultado = $conn->query($sql);

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
                        <a class="perfil-option" href="configuracoes.php">Configurações</a>

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

            <a class="botao-secundario" href="editar_perfil.php">
                <i class="fa-solid fa-pen"></i> Editar perfil
            </a>
        </section>

        <nav class="abas-perfil" id="abas-perfil">
            <button class="aba-perfil ativa" data-aba="tarefas">Minhas tarefas</button>
            <button class="aba-perfil" data-aba="favoritos">Favoritos</button>
            <button class="aba-perfil" data-aba="historico">Histórico</button>
        </nav>

        <section class="painel-perfil" id="painel-tarefas">
            <div class="estado-vazio">
                <i class="fa-solid fa-clipboard-list"></i>
                <p>Você ainda não publicou nenhuma tarefa.</p>
                <a class="botao-primario" href="cadastro_guias.php">Cadastrar Guia</a>
            </div>
        </section>

        <section class="painel-perfil" id="painel-favoritos" hidden>
            <?php if ($resultado->num_rows == 0) { ?>
                <div class="estado-vazio">
                    <i class="fa-solid fa-heart"></i>
                    <p>Você ainda não favoritou nenhuma tarefa.</p>
                </div>
            <?php } else { ?>
                <section class="painel-perfil" id="painel-favoritos" hidden>
                    <div class="lista-guias-perfil">
                        <article class="card-guia-perfil">
                            <div class="card-guia-capa">
                                <img src="imgs_guias/exemplo.jpg" alt="Capa da guia">
                            </div>

                            <div class="card-guia-conteudo">
                                <span class="card-guia-categoria">Culinária</span>
                                <h3 class="card-guia-titulo">Como preparar receitas simples</h3>
                                <p class="card-guia-descricao">Aprenda a preparar receitas práticas para o dia a dia.</p>
                                <div class="card-guia-detalhes">
                                    <span><i class="fa-solid fa-signal"></i>Fácil</span>
                                    <span><i class="fa-solid fa-user"></i>Nome do autor</span>
                                </div>
                            </div>

                            <div class="card-guia-acoes">
                                <a href="guia.php?id=1" class="card-guia-link">
                                    Ver guia
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                                <button type="button"class="botao-favorito favoritado"aria-label="Remover dos favoritos"><i class="fa-solid fa-heart"></i></button>
                            </div>
                        </article>
                    </div>
                </section>
            <?php } ?>
        </section>

        <section class="painel-perfil" id="painel-historico" hidden>
            <div class="estado-vazio">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <p>Nenhuma tarefa visualizada recentemente.</p>
            </div>
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
</body>

</html>