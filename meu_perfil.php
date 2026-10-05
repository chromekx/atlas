<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

include('conexao.php');

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
        <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>

        <nav class="nav-options">
            <button onclick="window.location.href='cadastro_guias.php'" class="item"><p>Cadastrar Guia</p></button>
            <button onclick="window.location.href='item2.php'" class="item"><p>Item 2</p></button>
            <button onclick="window.location.href='item3.php'" class="item"><p>Item 3</p></button>
            <button onclick="window.location.href='item4.php'" class="item"><p>Item 4</p></button>
        </nav>

        <nav class="nav-btns">
            <a class="icon" onclick="abrirPesquisa()"><i class="fa-solid fa-magnifying-glass"></i></a>

            <div class="perfil" id="perfil">
                <p>
                    <img class="foto" src="imgs_banco/<?= !empty($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : 'foto_padrao.png' ?>">
                    Olá, <?= htmlspecialchars($_SESSION['nome']) ?>
                </p>
                <i class="fa-solid fa-caret-up" id="seta"></i>

                <div class="perfil-options" id="perfil-options">
                    <a class="perfil-option meu-perfil" id="perfil-option" href="meu_perfil.php">Meu Perfil</a>
                    <a class="perfil-option config" id="perfil-option" href="configuracoes.php">Configurações</a>
                    <a class="perfil-option sair" id="perfil-option" href="sair.php">Sair</a>
                    <?php if ($_SESSION['nivel'] == 1): ?>
                        <a class="perfil-option admin" id="perfil-option" href="admin.php">Painel do Administrador</a>
                    <?php endif; ?>
                </div>
            </div>

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main>
        <section class="cabecalho-perfil">
            <img class="foto-grande" src="imgs_banco/<?= !empty($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : 'foto_padrao.png' ?>">

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
            <div class="estado-vazio">
                <i class="fa-solid fa-heart"></i>
                <p>Você ainda não favoritou nenhuma tarefa.</p>
            </div>
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
    <script src="js/meuperfil.js"></script>
</body>

</html>