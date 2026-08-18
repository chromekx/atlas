<?php
session_start();
include('conexao.php');
$sql = "SELECT id_usuario, nivel FROM usuarios WHERE id_usuario = " . $_SESSION['id'];

if (isset($_SESSION['id'])) {
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row['nivel'] != 1) {
        header("Location: index.php");
        exit();
    }
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
    <link rel="favicon" href="imgs/logoatlas.png" type="image/x-icon">
</head>

<body>
    <header>
        <a class="logo" href="index.php"><img src="imgs/logoatlas.png"></a>

        <nav class="nav-btns">
            <a class="icon" onclick="abrirPesquisa()"><i class="fa-solid fa-magnifying-glass"></i></a>

            <?php if (isset($_SESSION['id'])): ?>
                <div class="perfil" id="perfil">
                    <p>Olá, <?php echo $_SESSION['nome']; ?></p>
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
                <button class="login-btn entrar" onclick="window.location.href='login.php'">Entrar</button>
                <button class="login-btn cadastro" onclick="window.location.href='cadastro.php'">Cadastrar</button>
            <?php endif; ?>

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <h1 class="titulo"> Painel dos Administradores </h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Senha</th>
            <th>Preferência</th>
            <th>Nível</th>
            <th>Data de Criação</th>
            <th>Data Delete</th>
            <th>Editar</th>
            <th>Deletar</th>
        </tr>

        <?php
        $sql = "SELECT id_usuario, nome, email, senha, preferencia, nivel, data_criacao, data_delete FROM usuarios";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id_usuario'] ?></td>
                <td><?= $row['nome'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['senha'] ?></td>
                <td><?= $row['preferencia'] ?></td>
                <td><?= $row['nivel'] ?></td>
                <td><?= $row['data_criacao'] ?></td>
                <td><?= $row['data_delete'] ?></td>
                <td><button class="editar" onclick="editarUsuario(<?= $row['id_usuario'] ?>)">Editar</button></td>
                <td><button class="excluir" onclick="confirmarExclusao(<?= $row['id_usuario'] ?>)">Excluir</button></td>
            </tr>
        <?php } ?>
    </table>

    <script src="js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarExclusao(id) {
            Swal.fire({
                title: "Excluir Usuário",
                text: "Tem certeza que deseja excluir este usuário?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0a46a0",
                cancelButtonColor: "#dc3545",
                confirmButtonText: "Sim, excluir",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'excluir_usuario.php?id=' + id;
                }
            });
        }

        async function editarUsuario(id) {
            const {
                value: formValues
            } = await Swal.fire({
                title: "Atualizar Usuário",
                html: `
                    <input type = 'text' id="swal-input1" placeholder="Nome" class="swal2-input">
                    <input type = 'email' id="swal-input2" placeholder="Email" class="swal2-input">
                    <input type = 'password' id="swal-input3" placeholder="Senha" class="swal2-input">
                    <input type = 'password' id="swal-input4" placeholder="Confirmar Senha" class="swal2-input">
                    <select id="swal-input5" placeholder="Preferência" class="swal2-select" value>
                        <option value="esportes">Esportes</option>
                        <option value="música">Música</option>
                        <option value="cinema">Cinema</option>
                        <option value="livros">Livros</option>
                    </select>
                    `,
                focusConfirm: false,
                preConfirm: () => {
                    return [document.getElementById("swal-input1").value, document.getElementById("swal-input2").value];
                }
            });
            if (formValues) Swal.fire(JSON.stringify(formValues));
        }
    </script>
</body>

</html>