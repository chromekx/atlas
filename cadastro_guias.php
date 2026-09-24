<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
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
    <title>ATLAS - Cadastrar Guia</title>
    <link rel="stylesheet" href="css/cadastro_guias.css">
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

            <?php if (isset($_SESSION['id'])): ?>
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
            <?php else: ?>
                <button class="entrar" onclick="window.location.href='login.php'">Entrar</button>
            <?php endif; ?>

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main>
        <div class="cabecalho-pagina">
            <h1>Cadastrar Guia</h1>
            <p>Monte um guia passo a passo pra ajudar outras pessoas a aprenderem algo novo.</p>
        </div>

        <form class="formulario-tarefa" id="formulario-tarefa" method="POST" enctype="multipart/form-data">
            <section class="cartao-formulario">
                <h2>Informações gerais</h2>

                <div class="campo">
                    <label for="titulo">Título</label>
                    <input type="text" id="titulo" name="titulo" maxlength="200" placeholder="Ex: Como fazer um bolo de chocolate" required>
                </div>

                <div class="linha-campos">
                    <div class="campo">
                        <label for="categoria">Categoria</label>
                        <select id="categoria" name="id_categoria" required>
                            <option value="" disabled selected>Selecione uma categoria</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="dificuldade">Dificuldade</label>
                        <select id="dificuldade" name="id_dificuldade" required>
                            <option value="" disabled selected>Selecione a dificuldade</option>
                            <option value="1">Fácil</option>
                            <option value="2">Médio</option>
                            <option value="3">Difícil</option>
                        </select>
                    </div>
                </div>

                <div class="campo">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4" placeholder="Conte rapidamente o que essa tarefa ensina" required></textarea>
                </div>
            </section>

            <section class="cartao-formulario">
                <h2>Materiais necessários</h2>
                <p class="dica-campo">Digite um material e aperte Enter (ou clique em Adicionar).</p>

                <div class="entrada-materiais">
                    <input type="text" id="input-material" placeholder="Ex: Farinha de trigo">
                    <button type="button" id="botao-add-material" class="botao-secundario">Adicionar</button>
                </div>

                <ul class="lista-materiais" id="lista-materiais"></ul>
            </section>

            <section class="cartao-formulario">
                <div class="cabecalho-secao">
                    <h2>Etapas</h2>
                    <button type="button" id="botao-add-etapa" class="botao-secundario">
                        <i class="fa-solid fa-plus"></i> Adicionar etapa
                    </button>
                </div>

                <div class="lista-etapas" id="lista-etapas"></div>
            </section>

            <template id="template-etapa">
                <div class="etapa">
                    <div class="cabecalho-secao">
                        <span class="etapa-numero">Etapa</span>
                        <button type="button" class="botao-remover-etapa" aria-label="Remover etapa">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>

                    <div class="campo">
                        <label>Título da etapa (opcional)</label>
                        <input type="text" class="campo-etapa-titulo" placeholder="Ex: Misture os ingredientes secos">
                    </div>

                    <div class="campo">
                        <label>Descrição</label>
                        <textarea class="campo-etapa-descricao" rows="3" placeholder="Explique o que fazer nessa etapa" required></textarea>
                    </div>

                    <div class="campo">
                        <label>Foto ou vídeo (opcional)</label>
                        <input type="file" class="campo-etapa-midia" accept="image/*,video/*">
                    </div>
                </div>
            </template>

            <div class="acoes-formulario">
                <button type="submit" class="botao-primario">Publicar tarefa</button>
            </div>
        </form>
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
    <script src="js/cadastro_guias.js"></script>
</body>

</html>