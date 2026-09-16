<?php
session_start();
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
    <title>ATLAS - Cadastrar Tarefa</title>
    <link rel="stylesheet" href="css/cadastro_tarefas.css">
    <link rel="favicon" href="imgs_website/logoatlas.png" type="image/x-icon">
</head>

<body>
    <header>
        <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>

        <nav class="nav-options">
            <div class="item">
                <a href="cadastro_tarefas.php"><p>Cadastrar Tarefa</p></a>
            </div>

            <div class="item">
                <a href="item2.php"><p>Item 2</p></a>
            </div>

            <div class="item">
                <a href="item3.php"><p>Item 3</p></a>
            </div>

            <div class="item">
                <a href="item4.php"><p>Item 4</p></a>
            </div>
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
                        <a class="perfil-option meu-perfil" id="perfil-option" href="meuperfil.php">Meu Perfil</a>
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
            <h1>Cadastrar tarefa</h1>
            <p>Monte um guia passo a passo pra ajudar outras pessoas a aprenderem algo novo.</p>
        </div>

        <form class="formulario-tarefa" id="formulario-tarefa" method="POST" action="cadastro_tarefas.php" enctype="multipart/form-data">

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

    <script src="js/header.js"></script>
    <script src="js/cadastro_tarefas.js"></script>
</body>

</html>