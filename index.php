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
            <button onclick="window.location.href='item2.php'" class="item">
                <p>Item 2</p>
            </button>
            <button onclick="window.location.href='item3.php'" class="item">
                <p>Item 3</p>
            </button>
            <button onclick="window.location.href='item4.php'" class="item">
                <p>Item 4</p>
            </button>
        </nav>
        <?php } ?>

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

            <a class="icon" onclick="mudarTema()"><i class="fa-solid fa-circle-half-stroke"></i></a>
        </nav>
    </header>

    <main>
        <div class="hero-text">
            <h1>Um lugar para você criar sua independência.</h1>
            <p>Com o Atlas, você vai longe.</p>
            <button></button>
        </div>
    </main>

    <section id="secao-categorias">
        <h1 class="titulo">Categorias</h1>

        <div class="carrossel">
            <button class="carrossel-seta carrossel-seta-esquerda" id="setaEsquerda" type="button" aria-label="Categorias anteriores">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="categorias" id="categorias">
                <button onclick="window.location.href='guias/guias_culinaria/guias.php'" class="categoria"><i class="fa-solid fa-bowl-food"></i>Culinária</button>
                <button onclick="window.location.href='guias/guias_casa/guias.php'" class="categoria"><i class="fa-solid fa-house"></i>Casa</button>
                <button onclick="window.location.href='guias/guias_limpeza/guias.php'" class="categoria"><i class="fa-solid fa-broom"></i>Limpeza</button>
                <button onclick="window.location.href='guias/guias_financas/guias.php'" class="categoria"><i class="fa-solid fa-money-bill-wave"></i>Finanças</button>
                <button onclick="window.location.href='guias/guias_trabalho/guias.php'" class="categoria"><i class="fa-solid fa-briefcase"></i>Trabalho</button>
                <button onclick="window.location.href='guias/guias_educacao/guias.php'" class="categoria"><i class="fa-solid fa-graduation-cap"></i>Educação</button>
                <button onclick="window.location.href='guias/guias_saude/guias.php'" class="categoria"><i class="fa-solid fa-heart-pulse"></i>Saúde</button>
                <button onclick="window.location.href='guias/guias_automoveis/guias.php'" class="categoria"><i class="fa-solid fa-car"></i>Automóveis</button>
                <button onclick="window.location.href='guias/guias_transporte/guias.php'" class="categoria"><i class="fa-solid fa-bus"></i>Transporte</button>
                <button onclick="window.location.href='guias/guias_tecnologia/guias.php'" class="categoria"><i class="fa-solid fa-computer"></i>Tecnologia</button>
                <button onclick="window.location.href='guias/guias_jardinagem/guias.php'" class="categoria"><i class="fa-solid fa-seedling"></i>Jardinagem</button>
                <button onclick="window.location.href='guias/guias_moda/guias.php'" class="categoria"><i class="fa-solid fa-shirt"></i>Moda</button>
                <button onclick="window.location.href='guias/guias_cuidados/guias.php'" class="categoria"><i class="fa-solid fa-baby"></i>Cuidados</button>
                <button onclick="window.location.href='guias/guias_manutencao/guias.php'" class="categoria"><i class="fa-solid fa-screwdriver-wrench"></i>Manutenção</button>
                <button onclick="window.location.href='guias/guias_compras/guias.php'" class="categoria"><i class="fa-solid fa-cart-shopping"></i>Compras</button>
            </div>

            <button class="carrossel-seta carrossel-seta-direita" id="setaDireita" type="button" aria-label="Próximas categorias">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <?php if (isset($_SESSION['id'])) { ?>
        <section id="preferencias">
            <h1 class="titulo">Porque você gosta de <?= htmlspecialchars($_SESSION['preferencia']) ?>:</h1>

            <div class="cartoes" id="cartao">
                <div class="cartao" id="cartao">
                    <div class="foto-card" style="background-color: #44c0fa;"></div>
                    <div class="cartao-texto">
                        <h2>Cartão 1</h2>
                        <p>Desc do cartão 1</p>
                    </div>
                </div>

                <div class="cartao" id="cartao">
                    <div class="foto-card" style="background-color: #ffcb3d;"></div>
                    <div class="cartao-texto">
                        <h2>Cartão 2</h2>
                        <p>Desc do cartão 2</p>
                    </div>
                </div>

                <div class="cartao" id="cartao">
                    <div class="foto-card" style="background-color: #ff6464ff;"></div>
                    <div class="cartao-texto">
                        <h2>Cartão 3</h2>
                        <p>Desc do cartão 3</p>
                    </div>
                </div>

                <div class="cartao" id="cartao">
                    <div class="foto-card" style="background-color: #b0da98ff;"></div>
                    <div class="cartao-texto">
                        <h2>Cartão 4</h2>
                        <p>Desc do cartão 4</p>
                    </div>
                </div>

                <div class="cartao" id="cartao">
                    <div class="foto-card" style="background-color: #3b56cf;"></div>
                    <div class="cartao-texto">
                        <h2>Cartão 3</h2>
                        <p>Desc do cartão 3</p>
                    </div>
                </div>

                <div class="cartao" id="cartao">
                    <div class="foto-card" style="background-color: #635ada;"></div>
                    <div class="cartao-texto">
                        <h2>Cartão 4</h2>
                        <p>Desc do cartão 4</p>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>

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

    <script src="js/index.js"></script>
    <script src="js/header.js"></script>
</body>

</html>