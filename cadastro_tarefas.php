<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Cadastrar Tarefa - ATLAS</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/cadastro_tarefas.css">
    <link rel="icon" href="imgs_website/logoatlas.png">
</head>

<body>

    <header>
        <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>

        <nav class="nav-options">
            <div class="item"><a href="cadastro_tarefas.php">
                    <p>Cadastrar Tarefa</p>
                </a></div>
            <div class="item"><a href="item2.php">
                    <p>Item 2</p>
                </a></div>
            <div class="item"><a href="item3.php">
                    <p>Item 3</p>
                </a></div>
            <div class="item"><a href="item4.php">
                    <p>Item 4</p>
                </a></div>
        </nav>

        <nav class="nav-btns">
            <a class="icon" onclick="abrirPesquisa()"><i class="fa-solid fa-magnifying-glass"></i></a>

            <?php if (isset($_SESSION['id'])): ?>
                <div class="perfil" id="perfil">
                    <p><img class="foto" src="imgs_banco/<?= !empty($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : 'foto_padrao.png' ?>">Olá, <?= htmlspecialchars($_SESSION['nome']) ?></p>
                    <i class="fa-solid fa-caret-up" id="seta"></i>
                    <div class="perfil-options" id="perfil-options">
                        <a class="perfil-option" href="meuperfil.php">Meu Perfil</a>
                        <a class="perfil-option" href="configuracoes.php">Configurações</a>
                        <a class="perfil-option sair" href="sair.php">Sair</a>
                        <?php if ($_SESSION['nivel'] == 1): ?><a class="perfil-option" href="admin.php">Painel do Administrador</a><?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <button class="entrar" onclick="window.location.href='login.php'"></button>
            <?php endif; ?>

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main class="cadastro-tarefa">
        <div class="cabecalho-pagina">
            <div>
                <p class="pagina-label">ATLAS / TAREFAS</p>
                <h1>Cadastrar tarefa</h1>
                <p class="descricao-pagina">Preencha as informações abaixo.</p>
            </div>
        </div>

        <form class="formulario-tarefa" method="POST">

            <div class="secao-formulario">
                <h2>Informações</h2>

                <div class="campo">
                    <label>Título</label>
                    <input type="text" name="titulo" required maxlength="150">
                </div>

                <div class="campo">
                    <label>Descrição</label>
                    <textarea name="descricao" required maxlength="1000"></textarea>
                </div>

                <div class="campos-duplos">
                    <div class="campo">
                        <label>Categoria</label>
                        <select name="categoria" required>
                            <option disabled selected>Selecione</option>
                            <option>Programação</option>
                            <option>Design</option>
                            <option>Banco</option>
                            <option>Web</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label>Dificuldade</label>
                        <select name="dificuldade" required>
                            <option disabled selected>Selecione</option>
                            <option>Fácil</option>
                            <option>Médio</option>
                            <option>Difícil</option>
                        </select>
                    </div>
                </div>

                <div class="campos-duplos">
                    <div class="campo">
                        <label>Prioridade</label>
                        <select name="prioridade" required>
                            <option disabled selected>Selecione</option>
                            <option>Baixa</option>
                            <option>Média</option>
                            <option>Alta</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label>Data</label>
                        <input type="date" name="data_entrega">
                    </div>
                </div>
            </div>

            <div class="secao-formulario">
                <h2>Materiais</h2>
                <div class="campo">
                    <label>Materiais</label>
                    <textarea name="materiais"></textarea>
                </div>
            </div>

            <div class="acoes-formulario">
                <a href="index.php" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-cadastrar"><i class="fa-solid fa-plus"></i>Cadastrar</button>
            </div>

        </form>
    </main>

    <script src="js/header.js"></script>
</body>

</html>