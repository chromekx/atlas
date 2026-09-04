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
    <title>ATLAS</title>
    <link rel="stylesheet" href="../css/tarefas.css">
    <link rel="favicon" href="imgs_website/logoatlas.png" type="image/x-icon">
</head>

<body>
    <header>
        <a class="logo" href="../index.php"><img src="../imgs_website/logotipoatlas.png"></a>

        <nav class="nav-options">
            <div class="item">
                <p>Item 1</p>
            </div>

            <div class="item">
                <p>Item 2</p>
            </div>

            <div class="item">
                <p>Item 3</p>
            </div>

            <div class="item">
                <p>Item 4</p>
            </div>
        </nav>

        <nav class="nav-btns">
            <a class="icon" onclick="abrirPesquisa()"><i class="fa-solid fa-magnifying-glass"></i></a>

            <?php if (isset($_SESSION['id'])): ?>
                <div class="perfil" id="perfil">
                    <p>
                        <img class="foto" src="../imgs_banco/<?= !empty($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : 'foto_padrao.png' ?>">
                        Olá, <?= $_SESSION['nome'] ?>
                    </p>
                    <i class="fa-solid fa-caret-up" id="seta"></i>

                    <div class="perfil-options" id="perfil-options">
                        <a class="perfil-option meu-perfil" id="perfil-option" href="../meuperfil.php">Meu Perfil</a>
                        <a class="perfil-option config" id="perfil-option" href="../configuracoes.php">Configurações</a>
                        <a class="perfil-option sair" id="perfil-option" href="../sair.php">Sair</a>
                        <?php if (isset($_SESSION['id']) && $_SESSION['nivel'] == 1): ?>
                            <a class="perfil-option admin" id="perfil-option" href="../admin.php">Painel do Administrador</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <button class="entrar" onclick="window.location.href='../login.php'"></button>
            <?php endif; ?>

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main>
        <div class="categ-div">
            <label for="categorias" class="categorias-label">Categorias</label>
            <section class="categorias" id="categorias">
                <button class="categoria" onclick="window.location.href='../tarefas_culinaria/tarefas.php'">Culinária</button>
                <button class="categoria" onclick="window.location.href='../tarefas_esporte/tarefas.php'">Esporte</button>
                <button class="categoria" onclick="window.location.href='../tarefas_lazer/tarefas.php'">Lazer</button>
            </section>
        </div>
        <section class="lado-tarefas">
            <div class="breadcrumbs">
                <a href="../index.php">Início</a>
                <i class="fa-solid fa-arrow-right"></i>
                <a href="../tarefas_culinaria/tarefas.php">Culinária</a>
            </div>
            <button class="add-tarefa" onclick="window.location.href='../cadastro_tarefas.php'">Adicionar Tarefa</button>
            <div class="tarefas">

            </div>
        </section>
    </main>

    <script src="../js/header.js"></script>
    <script src="../js/tarefas.js"></script>
</body>

</html>