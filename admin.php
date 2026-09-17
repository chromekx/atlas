<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id']) || $_SESSION['nivel'] != 1) {
    header("Location: index.php");
    exit();
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
    <title>ATLAS</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="favicon" href="imgs_website/logoatlas.png" type="image/x-icon">
</head>

<body>
    <header>
        <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>

        <nav class="nav-btns">
            <a class="icon" onclick="abrirPesquisa()"><i class="fa-solid fa-magnifying-glass"></i></a>

            <?php if (isset($_SESSION['id'])): ?>
                <div class="perfil" id="perfil">
                    <p>
                        <img class="foto" src="imgs_banco/<?= !empty($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : 'foto_padrao.png' ?>">
                        Olá, <?= $_SESSION['nome'] ?>
                    </p>
                    <i class="fa-solid fa-caret-up" id="seta"></i>

                    <div class="perfil-options" id="perfil-options">
                        <a class="perfil-option meu-perfil" id="perfil-option" href="meuperfil.php">Meu Perfil</a>
                        <a class="perfil-option config" id="perfil-option" href="configuracoes.php">Configurações</a>
                        <a class="perfil-option sair" id="perfil-option" href="sair.php">Sair</a>
                        <?php if (isset($_SESSION['id']) && $_SESSION['nivel'] == 1): ?>
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

    <h1 class="titulo"> Painel dos Administradores </h1>

    <div class="forms">
        <form class="form-pesquisar" method="POST">
            <p>Pesquisar por ID:</p>
            <div class="division">
                <input class="barra-pesquisa" type="number" min="1" id="barraPesquisa" name="barraPesquisa" placeholder="Digite o ID do usuário">
                <div class="botoes">
                    <button class="btn-pesquisar" id="btnPesquisar" name="pesquisar">Pesquisar</button>
                    <button class="btn-resetar" id="btnResetar" name="resetar">Resetar</button>
                </div>
            </div>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Senha</th>
                <th>Preferência</th>
                <th>Nível</th>
                <th>Estado</th>
                <th>Data de Criação</th>
                <th>Editar</th>
                <th>Ativação</th>
            </tr>

            <?php
            if (!isset($_POST['pesquisar'])) {
                $sql = "SELECT id_usuario, foto, nome, email, senha, preferencia, nivel, estado, data_criacao FROM usuario";
            } else {
                $pesquisa = $_POST['barraPesquisa'];
                $sql = "SELECT id_usuario, foto, nome, email, senha, preferencia, nivel, estado, data_criacao FROM usuario WHERE id_usuario = '$pesquisa'";
            }

            $resultado = $conn->query($sql);

            if ($resultado->num_rows <= 0) {
                $erro = 'Não há usuários cadastrados.';
            } else {
                while ($linha = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td id="id:<?= $linha['id_usuario'] ?>"><?= $linha['id_usuario'] ?></td>
                        <td><img class="foto-bd" src="imgs_banco/<?= !empty($linha['foto']) ? htmlspecialchars($linha['foto']) : 'foto_padrao.png' ?>"></td>
                        <td><?= htmlspecialchars($linha['nome']) ?></td>
                        <td><?= htmlspecialchars($linha['email']) ?></td>
                        <td><?= htmlspecialchars($linha['senha']) ?></td>
                        <td><?= htmlspecialchars($linha['preferencia']) ?></td>
                        <td><?= htmlspecialchars($linha['nivel']) ?></td>
                        <td><?= htmlspecialchars($linha['estado']) ?></td>
                        <td><?= htmlspecialchars($linha['data_criacao']) ?></td>
                        <td><button class="editar" onclick="window.location.href='editar_usuario.php?id=' + <?= $linha['id_usuario'] ?>">Editar</button></td>
                        <?php if ($linha['estado'] == "ativo") { ?>
                            <td><button class='inativar' onclick="inativarUsuario(<?= $linha['id_usuario'] ?>)">Inativar</button></td>
                        <?php } else { ?>
                            <td><button class='reativar' onclick="reativarUsuario(<?= $linha['id_usuario'] ?>)">Reativar</button></td>
                        <?php } ?>
                    </tr>
            <?php }
            } ?>
        </table>

        <?php if (!empty($erro)) {
            echo $erro;
        } ?>
    </div>

    <script src="js/header.js"></script>
    <script src="js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>