<?php
session_start();
require_once __DIR__ . '/conexao.php';

$idGuia = (int)($_GET['id'] ?? 0);
if (!$idGuia || $idGuia < 1) {
    http_response_code(400);
    exit('Guia inválido.');
}

$sql = "SELECT id_guia, id_usuario, titulo, descricao, categoria, dificuldade,
               data_criacao, LENGTH(foto) AS foto_tamanho, nome AS autor
        FROM guia
        WHERE id_guia = $idGuia";
$resultadoGuia = mysqli_query($conn, $sql);
$guia = mysqli_fetch_assoc($resultadoGuia);

if (!$guia) {
    http_response_code(404);
    exit('Este guia não foi encontrado.');
}

$sqlMateriais = "SELECT m.nome
     FROM materiais ms
     INNER JOIN material m ON m.id_materiais = ms.id_materiais
     WHERE ms.id_guia = $idGuia
     ORDER BY m.id_material";
$resultadoMateriais = mysqli_query($conn, $sqlMateriais);
$materiais = mysqli_fetch_all($resultadoMateriais, MYSQLI_ASSOC);

$sqlEtapas = "SELECT e.id_etapa, e.descricao, e.midia
     FROM etapas es
     INNER JOIN etapa e ON e.id_etapas = es.id_etapas
     WHERE es.id_guia = $idGuia
     ORDER BY e.id_etapa";
$resultadoEtapas = mysqli_query($conn, $sqlEtapas);
$etapas = mysqli_fetch_all($resultadoEtapas, MYSQLI_ASSOC);

function h($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATLAS - <?= h($guia['titulo']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/guia.css">
    <link rel="icon" href="imgs_website/logoatlas.png" type="image/png">
</head>
<body>
<header>
    <a class="logo" href="index.php" aria-label="Página inicial"><img src="imgs_website/logotipoatlas.png" alt="ATLAS"></a>
    <nav class="nav-options" aria-label="Navegação principal">
        <a class="item" href="guias.php"><p>Explorar guias</p></a>
        <?php if (isset($_SESSION['id'])): ?><a class="item" href="cadastro_guias.php"><p>Cadastrar guia</p></a><?php endif; ?>
        <a class="item" href="index.php#aspectos"><p>Sobre o ATLAS</p></a>
    </nav>
    <nav class="nav-btns" aria-label="Conta">
        <?php if (isset($_SESSION['id'])): ?>
            <div class="perfil" id="perfil"><p>Olá, <?= h($_SESSION['nome'] ?? 'usuário') ?></p><i class="fa-solid fa-caret-down" id="seta"></i><div class="perfil-options" id="perfil-options"><a class="perfil-option" href="meu_perfil.php">Meu perfil</a><a class="perfil-option" href="configuracoes.php">Configurações</a><?php if (($_SESSION['nivel'] ?? '') == 1): ?><a class="perfil-option" href="admin.php">Painel do administrador</a><?php endif; ?><a class="perfil-option" href="sair.php">Sair</a></div></div>
        <?php else: ?><a class="entrar" href="login.php">Entrar</a><?php endif; ?>
    </nav>
</header>

<main class="pagina-guia">
    <nav class="migalhas" aria-label="Você está aqui">
        <a href="index.php">Início</a><i class="fa-solid fa-chevron-right"></i><a href="guias.php">Guias -  <?= $categoria ?></a><i class="fa-solid fa-chevron-right"></i></i><span><?= h($guia['titulo']) ?></span>
    </nav>

    <section class="guia-capa">
        <img class="guia-capa-imagem" src="exibir_midia_guia.php?tipo=capa&id=<?= (int)$idGuia ?>" alt="" onerror="this.style.display='none';this.parentElement.classList.add('sem-capa')">
        <div class="guia-capa-sombra"></div>
        <div class="guia-capa-conteudo">
            <span class="guia-categoria"><i class="fa-solid fa-book-open"></i> <?= h($guia['categoria']) ?></span>
            <h1><?= h($guia['titulo']) ?></h1>
            <p><?= h($guia['descricao']) ?></p>
            <div class="guia-capa-meta">
                <span><i class="fa-solid fa-signal"></i> <?= h($guia['dificuldade']) ?></span>
                <span><i class="fa-regular fa-user"></i> <?= h($guia['autor'] ?: 'Comunidade ATLAS') ?></span>
                <span><i class="fa-regular fa-calendar"></i> <?= h(date('d/m/Y', strtotime($guia['data_criacao']))) ?></span>
            </div>
        </div>
        <div class="capa-placeholder"><i class="fa-solid fa-book-open"></i></div>
    </section>

    <div class="guia-layout">
        <article class="guia-conteudo">
            <section class="bloco-guia introducao">
                <h2>Descrição da guia</h2>
                <p><?= nl2br(h($guia['descricao'])) ?></p>
            </section>

            <?php if ((int)($guia['foto_tamanho'] ?? 0) > 0): ?>
                <section class="foto-destaque">
                    <img src="exibir_midia_guia.php?tipo=foto&id=<?= (int)$idGuia ?>" alt="Imagem complementar do guia" loading="lazy">
                </section>
            <?php endif; ?>

            <?php if ($materiais): ?>
                <section class="bloco-guia materiais-guia">
                    <div class="cabecalho-bloco"><span class="icone-bloco"><i class="fa-solid fa-toolbox"></i></span><div><h2>Materiais necessários</h2></div></div>
                    <ul><?php foreach ($materiais as $material): ?><li><i class="fa-solid fa-check"></i><span><?= h($material['nome']) ?></span></li><?php endforeach; ?></ul>
                </section>
            <?php endif; ?>

            <section class="bloco-guia etapas-guia">
                <div class="cabecalho-bloco"><span class="icone-bloco"><i class="fa-solid fa-list-ol"></i></span><div><h2>Passo a passo</h2></div></div>
                <?php if (!$etapas): ?>
                    <p class="sem-etapas">Este guia ainda não tem etapas cadastradas.</p>
                <?php else: ?>
                    <div class="linha-etapas">
                        <?php foreach ($etapas as $indice => $etapa): ?>
                            <article class="etapa-guia">
                                <div class="numero-etapa"><?= $indice + 1 ?></div>
                                <div class="etapa-corpo">
                                    <h3>Etapa <?= $indice + 1 ?></h3>
                                    <p><?= nl2br(h($etapa['descricao'])) ?></p>
                                    <?php if (!empty($etapa['midia'])): ?>
                                        <?php
                                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                                        $mimeEtapa = $finfo->buffer($etapa['midia']) ?: '';
                                        ?>
                                        <?php if (str_starts_with($mimeEtapa, 'image/')): ?>
                                            <img class="midia-etapa" src="exibir_midia_guia.php?tipo=etapa&id=<?= (int)$etapa['id_etapa'] ?>" alt="Imagem da etapa <?= $indice + 1 ?>" loading="lazy">
                                        <?php elseif (str_starts_with($mimeEtapa, 'video/')): ?>
                                            <video class="midia-etapa" controls preload="metadata"><source src="exibir_midia_guia.php?tipo=etapa&id=<?= (int)$etapa['id_etapa'] ?>" type="<?= h($mimeEtapa) ?>">Seu navegador não suporta vídeo.</video>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <?php
            $finfoVideo = new finfo(FILEINFO_MIME_TYPE);
            $videoDisponivel = false;
            // A consulta abaixo verifica se há vídeo sem carregar o arquivo completo na página.
            $resultadoVideo = mysqli_query($conn, "SELECT LENGTH(video) AS tamanho FROM guia WHERE id_guia = $idGuia");
            $videoDisponivel = (int)(mysqli_fetch_assoc($resultadoVideo)['tamanho'] ?? 0) > 0;
            ?>
            <?php if ($videoDisponivel): ?>
                <section class="bloco-guia video-guia">
                    <div class="cabecalho-bloco"><span class="icone-bloco"><i class="fa-solid fa-circle-play"></i></span><div><span class="sobretitulo">CONTEÚDO EM VÍDEO</span><h2>Veja como fazer</h2></div></div>
                    <video controls preload="metadata"><source src="exibir_midia_guia.php?tipo=video&id=<?= (int)$idGuia ?>" type="video/mp4">Seu navegador não suporta vídeo.</video>
                </section>
            <?php endif; ?>
        </article>

        <aside class="guia-lateral">
            <div class="cartao-resumo">
                <span class="sobretitulo">RESUMO DO GUIA</span>
                <div><i class="fa-solid fa-layer-group"></i><p><small>Categoria</small><strong><?= h($guia['categoria']) ?></strong></p></div>
                <div><i class="fa-solid fa-signal"></i><p><small>Dificuldade</small><strong><?= h($guia['dificuldade']) ?></strong></p></div>
                <div><i class="fa-solid fa-list-check"></i><p><small>Etapas</small><strong><?= count($etapas) ?> <?= count($etapas) === 1 ? 'passo' : 'passos' ?></strong></p></div>
                <a href="guias.php?categoria=<?= urlencode($guia['categoria']) ?>">Mais guias de <?= h($guia['categoria']) ?> <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </aside>
    </div>

    <div class="voltar-guias"><a href="guias.php"><i class="fa-solid fa-arrow-left"></i> Voltar para todos os guias</a></div>
</main>

<footer>
    <div class="footer-content">
        <div class="footer-brand"><a class="footer-logo" href="index.php" aria-label="Voltar para o início"><img src="imgs_website/logoatlas.png" alt="ATLAS"></a><p>Ideias, oportunidades e caminhos para você criar sua independência.</p><div class="footer-socials"><a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a><a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a><a href="mailto:contato@atlas.com" aria-label="Enviar e-mail"><i class="fa-regular fa-envelope"></i></a></div></div>
        <div class="footer-section"><h3>Explorar</h3><a href="guias.php">Guias</a><a href="guias.php">Categorias</a><a href="meu_perfil.php">Meu perfil</a></div>
        <div class="footer-section footer-contact"><h3>Vamos conversar?</h3><p>Tem uma dúvida ou uma ideia? Fale com a gente.</p><a href="mailto:contato@atlas.com">contato@atlas.com <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div>
    </div>
    <div class="footer-bottom"><p>&copy; <?= date('Y') ?> Atlas. Feito para ir longe.</p><div><a href="#">Privacidade</a><a href="#">Termos de uso</a></div></div>
</footer>
<script src="js/header.js"></script>
</body>
</html>
<?php $conn->close(); ?>
