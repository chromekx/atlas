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
    <link rel="stylesheet" href="css/index.css">
    <link rel="favicon" href="imgs_website/logoatlas.png" type="image/x-icon">
</head>

<body>
    <header>
        <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>

        <?php if (isset($_SESSION['id'])) { ?>
            <nav class="nav-options">
                <button onclick="window.location.href='cadastro_guias.php'" class="item">
                    <p>Cadastrar Guia</p>
                </button>
                <button onclick="window.location.href='#sobrenos'" class="item">
                    <p>Sobre Nós</p>
                </button>
                <button onclick="window.location.href='suporte.php'" class="item">
                    <p>Suporte</p>
                </button>
            </nav>
        <?php } ?>

        <nav class="nav-btns">
            <?php if (isset($_SESSION['id'])): ?>
                <div class="perfil" id="perfil">
                    <p>
                        <img class="foto" src="imgs_perfil/<?= !empty($_SESSION['foto']) ? htmlspecialchars($_SESSION['foto']) : 'foto_padrao.png' ?>">
                        Olá, <?= $_SESSION['nome'] ?>
                    </p>
                    <i class="fa-solid fa-caret-up" id="seta"></i>

                    <div class="perfil-options" id="perfil-options">
                        <a class="perfil-option meu-perfil" id="perfil-option" href="meu_perfil.php">Meu Perfil</a>
                        <a class="perfil-option config" id="perfil-option" href="configuracoes.php">Configurações</a>
                        <a class="perfil-option sair" id="perfil-option" href="sair.php">Sair</a>
                        <?php if (isset($_SESSION['id']) && $_SESSION['nivel'] == 1): ?>
                            <a class="perfil-option admin" id="perfil-option" href="admin.php">Painel do Administrador</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <button class="entrar" onclick="window.location.href='login.php'"></button>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php if (!isset($_SESSION['id'])) { ?>
            <div class="hero-text">
                <h1>Um lugar para você criar sua independência.</h1>
                <p>Com o Atlas, você vai longe.</p>
                <button onclick="window.location.href='login.php'"></button>
            </div>
        <?php } else { ?>
            <div class="hero-text">
                <h1>Um lugar para você criar sua independência.</h1>
                <p>Crie uma guia para ajudar as pessoas na sua comunidade!</p>
                <button onclick="window.location.href='cadastro_guias.php'"></button>
            </div>
        <?php } ?>
    </main>

    <section id="secao-categorias">
        <h1 class="titulo">Categorias</h1>

        <div class="carrossel">
            <button class="carrossel-seta carrossel-seta-esquerda" id="setaEsquerda" type="button" aria-label="Categorias anteriores">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="categorias" id="categorias">
                <button onclick="window.location.href='guias_Automóveis.php'" class="categoria"><i class="fa-solid fa-car"></i>Automóveis</button>
                <button onclick="window.location.href='guias_Casa.php'" class="categoria"><i class="fa-solid fa-house"></i>Casa</button>
                <button onclick="window.location.href='guias_Compras.php'" class="categoria"><i class="fa-solid fa-cart-shopping"></i>Compras</button>
                <button onclick="window.location.href='guias_Cuidados.php'" class="categoria"><i class="fa-solid fa-baby"></i>Cuidados</button>
                <button onclick="window.location.href='guias_Culinária.php'" class="categoria"><i class="fa-solid fa-bowl-food"></i>Culinária</button>
                <button onclick="window.location.href='guias_Educação.php'" class="categoria"><i class="fa-solid fa-graduation-cap"></i>Educação</button>
                <button onclick="window.location.href='guias_Finanças.php'" class="categoria"><i class="fa-solid fa-money-bill-wave"></i>Finanças</button>
                <button onclick="window.location.href='guias_Jardinagem.php'" class="categoria"><i class="fa-solid fa-seedling"></i>Jardinagem</button>
                <button onclick="window.location.href='guias_Limpeza.php'" class="categoria"><i class="fa-solid fa-broom"></i>Limpeza</button>
                <button onclick="window.location.href='guias_Manutenção.php'" class="categoria"><i class="fa-solid fa-screwdriver-wrench"></i>Manutenção</button>
                <button onclick="window.location.href='guias_Moda.php'" class="categoria"><i class="fa-solid fa-shirt"></i>Moda</button>
                <button onclick="window.location.href='guias_Saúde.php'" class="categoria"><i class="fa-solid fa-heart-pulse"></i>Saúde</button>
                <button onclick="window.location.href='guias_Tecnologia.php'" class="categoria"><i class="fa-solid fa-computer"></i>Tecnologia</button>
                <button onclick="window.location.href='guias_Trabalho.php'" class="categoria"><i class="fa-solid fa-briefcase"></i>Trabalho</button>
                <button onclick="window.location.href='guias_Transporte.php'" class="categoria"><i class="fa-solid fa-bus"></i>Transporte</button>
            </div>

            <button class="carrossel-seta carrossel-seta-direita" id="setaDireita" type="button" aria-label="Próximas categorias">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <section id="preferencias">
        <h1 class="titulo" id="sobrenos">Sobre Nós</h1>

        <div class="cartoes" id="cartao">
            <div class="cartao" id="cartao">
                <div class="foto-card" style="background-color: #44c0fa;"></div>
                <div class="cartao-texto">
                    <h2>Cartão 1</h2>
                    <p>Desc do cartão 1</p>
                </div>
            </div>
    </section>

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

    <script src="js/index.js"></script>
    <script src="js/header.js"></script>
</body>

</html>