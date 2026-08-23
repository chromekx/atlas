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
    <link rel="favicon" href="imgs/logoatlas.png" type="image/x-icon">
</head>

<body>
    <header>
        <a class="logo" href="index.php"><img src="imgs/logoatlas.png"></a>

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
                    <p>Olá, <?= $_SESSION['nome']; ?></p>
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

    <main>
        <div class="hero-text">
            <h1>Um lugar para você criar sua independência.</h1>
            <p>Com o Atlas, você vai longe.</p>
            <button></button>
        </div>
    </main>

    <section>
        <h1 class="titulo">Categorias</h1>

        <div class="carrossel">
            <div class="categorias" id="categorias">
                <div class="categoria">
                    <i class="fa-solid fa-baby"></i>
                    <p>Categoria 1</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-hammer"></i>
                    <p>Categoria 2</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-car"></i>
                    <p>Categoria 3</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-leaf"></i>
                    <p>Categoria 4</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-computer"></i>
                    <p>Categoria 5</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-broom"></i>
                    <p>Categoria 6</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-book"></i>
                    <p>Categoria 7</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-bowl-food"></i>
                    <p>Categoria 8</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-shirt"></i>
                    <p>Categoria 9</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <p>Categoria 10</p>
                </div>

                <div class="categoria">
                    <i class="fa-solid fa-bus"></i>
                    <p>Categoria 11</p>
                </div>
            </div>
        </div>
    </section>

    <section>
        <h1 class="titulo">Aspectos interessantes</h1>

        <div class="cartoes" id="cartao">
            <div class="cartao" id="cartao">
                <div class="foto" style="background-color: #44c0fa;"></div>
                <div class="cartao-texto">
                    <h2>Cartão 1</h2>
                    <p>Desc do cartão 1</p>
                </div>
            </div>

            <div class="cartao" id="cartao">
                <div class="foto" style="background-color: #ffcb3d;"></div>
                <div class="cartao-texto">
                    <h2>Cartão 2</h2>
                    <p>Desc do cartão 2</p>
                </div>
            </div>

            <div class="cartao" id="cartao">
                <div class="foto" style="background-color: #ff6464ff;"></div>
                <div class="cartao-texto">
                    <h2>Cartão 3</h2>
                    <p>Desc do cartão 3</p>
                </div>
            </div>

            <div class="cartao" id="cartao">
                <div class="foto" style="background-color: #b0da98ff;"></div>
                <div class="cartao-texto">
                    <h2>Cartão 4</h2>
                    <p>Desc do cartão 4</p>
                </div>
            </div>

            <div class="cartao" id="cartao">
                <div class="foto" style="background-color: #3b56cf;"></div>
                <div class="cartao-texto">
                    <h2>Cartão 3</h2>
                    <p>Desc do cartão 3</p>
                </div>
            </div>

            <div class="cartao" id="cartao">
                <div class="foto" style="background-color: #635ada;"></div>
                <div class="cartao-texto">
                    <h2>Cartão 4</h2>
                    <p>Desc do cartão 4</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="footer-wave"></div>

        <div class="footer-inner">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="imgs/logoatlas.png" alt="Logo Atlas">
                    <span>ATLAS</span>
                </div>
                <p>Conectando oportunidades, ideias e crescimento em um só lugar.</p>

                <div class="social-links">
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <h4>Explore</h4>
                <a href="#">Categorias</a>
                <a href="#">Novidades</a>
                <a href="#">Destaques</a>
                <a href="#">Para você</a>
            </div>

            <div class="footer-links">
                <h4>Empresa</h4>
                <a href="#">Sobre nós</a>
                <a href="#">Contato</a>
                <a href="#">Carreiras</a>
                <a href="#">Parcerias</a>
            </div>

            <div class="footer-newsletter">
                <h4>Receba novidades</h4>
                <p>Fique por dentro das melhores oportunidades e lançamentos.</p>

                <form class="newsletter-form">
                    <input type="email" placeholder="Seu e-mail" aria-label="Seu e-mail">
                    <button type="submit">Inscrever</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2026 Atlas. Todos os direitos reservados.</p>
            <div class="legal-links">
                <a href="#">Termos</a>
                <a href="#">Privacidade</a>
            </div>
        </div>
    </footer>

    <script src="js/index.js"></script>
    <script src="js/header.js"></script>
</body>

</html>