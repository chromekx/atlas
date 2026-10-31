<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$erro = '';

if (isset($_POST['publicar'])) {
    include('conexao.php');

    // Faz o query retornar false quando houver erro, em vez de lançar exceção.
    mysqli_report(MYSQLI_REPORT_OFF);

    $idUsuario = $_SESSION['id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $categoria = $_POST['categoria'];
    $dificuldade = $_POST['dificuldade'];
    $video = trim($_POST['video'] ?? '');
    $materiais = $_POST['material'] ?? [];
    $etapas = $_POST['etapas'] ?? [];

    // Verifica o link do vídeo.
    if ($video == '' || !filter_var($video, FILTER_VALIDATE_URL)) {
        $erro = 'Insira um link válido para o vídeo.';
    }

    // Recebe e valida a capa da guia.
    $capa = '';
    $arquivoCapa = $_FILES['capa'] ?? null;

    if ($erro == '') {
        if (!$arquivoCapa || $arquivoCapa['error'] == UPLOAD_ERR_NO_FILE) {
            $erro = 'Selecione uma imagem para a capa.';
        } elseif ($arquivoCapa['error'] != UPLOAD_ERR_OK) {
            $erro = 'Ocorreu um erro ao enviar a capa.';
        } else {
            $tmpName = $arquivoCapa['tmp_name'];
            $tamanho = $arquivoCapa['size'];

            if (!is_uploaded_file($tmpName)) {
                $erro = 'Arquivo da capa inválido.';
            } elseif ($tamanho > (5 * 1024 * 1024)) {
                $erro = 'A capa deve ter no máximo 5 MB.';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $tipo = finfo_file($finfo, $tmpName);
                finfo_close($finfo);

                if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) {
                    $erro = 'A capa deve ser JPG, PNG, WebP ou GIF.';
                } else {
                    $capa = file_get_contents($tmpName);
                }
            }
        }
    }

    // Converte a capa para gravar no campo BLOB.
    $capaSQL = "0x" . bin2hex($capa);

    // Escapa os textos para que aspas não quebrem o comando SQL.
    $titulo = $conn->real_escape_string($titulo);
    $descricao = $conn->real_escape_string($descricao);
    $categoria = $conn->real_escape_string($categoria);
    $dificuldade = $conn->real_escape_string($dificuldade);
    $video = $conn->real_escape_string($video);

    // Cadastra a guia.
    if ($erro == '') {
        // A coluna foto continua NOT NULL no banco, então recebe um valor vazio.
        $sql = "INSERT INTO guia (id_usuario, titulo, descricao, categoria, dificuldade, capa, video)
                VALUES ('$idUsuario', '$titulo', '$descricao', '$categoria', '$dificuldade', $capaSQL, '$video')";

        if ($conn->query($sql)) {
            $idGuia = $conn->insert_id;
        } else {
            $erro = 'Erro ao cadastrar a guia: ' . $conn->error;
        }
    }

    // Cadastra o agrupamento dos materiais.
    if ($erro == '') {
        $sql = "INSERT INTO materiais (id_guia) VALUES ('$idGuia')";

        if ($conn->query($sql)) {
            $idMateriais = $conn->insert_id;
            global $idMateriais;
        } else {
            $erro = 'Erro ao cadastrar os materiais: ' . $conn->error;
        }
    }

    // Cadastra os materiais.
    if ($erro == '' && is_array($materiais)) {
        foreach ($materiais as $material) {
            $material = trim($material);

            if ($material == '') {
                continue;
            }

            $material = $conn->real_escape_string($material);
            $sql = "INSERT INTO material (id_materiais, nome)
                    VALUES ('$idMateriais', '$material')";

            if (!$conn->query($sql)) {
                $erro = 'Erro ao cadastrar um material: ' . $conn->error;
                break;
            }
        }
    }

    // Cadastra o agrupamento das etapas.
    if ($erro == '') {
        $sql = "INSERT INTO etapas (id_guia) VALUES ('$idGuia')";

        if ($conn->query($sql)) {
            $idEtapas = $conn->insert_id;
            global $idEtapas;
        } else {
            $erro = 'Erro ao cadastrar as etapas: ' . $conn->error;
        }
    }

    // Cadastra cada etapa e sua mídia opcional.
    if ($erro == '' && is_array($etapas)) {
        foreach ($etapas as $indice => $etapa) {
            $etapaDesc = trim($etapa['descricao'] ?? '');

            if ($etapaDesc == '') {
                continue;
            }

            $midia = '';
            $codigoErro = $_FILES['etapas']['error'][$indice]['midia'] ?? UPLOAD_ERR_NO_FILE;

            if ($codigoErro != UPLOAD_ERR_NO_FILE) {
                if ($codigoErro != UPLOAD_ERR_OK) {
                    $erro = 'Erro ao enviar a mídia da etapa.';
                    break;
                }

                $tmpName = $_FILES['etapas']['tmp_name'][$indice]['midia'];
                $tamanho = $_FILES['etapas']['size'][$indice]['midia'];

                if (!is_uploaded_file($tmpName)) {
                    $erro = 'Arquivo da etapa inválido.';
                } elseif ($tamanho > (5 * 1024 * 1024)) {
                    $erro = 'A foto de cada etapa deve ter no máximo 5 MB.';
                } else {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $tipo = finfo_file($finfo, $tmpName);
                    finfo_close($finfo);

                    if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) {
                        $erro = 'A foto da etapa deve ser JPG, PNG, WebP ou GIF.';
                    } else {
                        $midia = file_get_contents($tmpName);
                    }
                }
            }

            if ($erro != '') {
                break;
            }

            $midiaSQL = $midia == '' ? 'NULL' : "0x" . bin2hex($midia);
            $etapaDesc = $conn->real_escape_string($etapaDesc);
            $sql = "INSERT INTO etapa (id_etapas, descricao, midia)
                    VALUES ('$idEtapas', '$etapaDesc', $midiaSQL)";

            if (!$conn->query($sql)) {
                $erro = 'Erro ao cadastrar uma etapa: ' . $conn->error;
                break;
            }
        }
    }

    // Redireciona depois de cadastrar tudo sem erros.
    if ($erro == '') {
        header('Location: guias_' . $categoria);
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
    <title>ATLAS - Cadastrar Guia</title>
    <link rel="stylesheet" href="css/cadastro_guias.css">
    <link rel="icon" href="imgs_website/logoatlas.png" type="image/png">
</head>

<body>
    <header>
        <a class="logo" href="index.php"><img src="imgs_website/logotipoatlas.png"></a>

        <?php if (isset($_SESSION['id'])) { ?>
            <nav class="nav-options">
                <button onclick="window.location.href='cadastro_guias.php'" class="item">
                    <p>Cadastrar Guia</p>
                </button>
                <button onclick="window.location.href='sobrenos.php'" class="item">
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

    <main class="pagina-cadastro">
        <nav class="migalhas" aria-label="Você está aqui">
            <a href="index.php">Início</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Cadastrar guia</span>
        </nav>

        <section class="cabecalho-pagina">
            <h1>Cadastrar guia</h1>
            <p>Monte um guia passo a passo para ajudar outras pessoas a aprenderem algo novo.</p>
        </section>


        <section class="guia-capa preview-capa sem-capa" id="preview-capa">
            <img class="guia-capa-imagem" id="preview-capa-imagem" alt="Prévia da capa da guia" hidden>
            <div class="guia-capa-sombra"></div>
            <div class="guia-capa-conteudo">
                <span class="guia-categoria"><i class="fa-solid fa-book-open"></i> <span id="preview-categoria">Categoria</span></span>
                <h2 id="preview-titulo">Título da sua guia</h2>
                <p id="preview-descricao">A descrição da sua guia aparecerá aqui enquanto você preenche o formulário.</p>
                <div class="guia-capa-meta">
                    <span><i class="fa-solid fa-signal"></i> <span id="preview-dificuldade">Dificuldade</span></span>
                    <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($_SESSION['nome'] ?? 'Comunidade ATLAS', ENT_QUOTES, 'UTF-8') ?></span>
                    <span><i class="fa-regular fa-eye"></i> Prévia ao vivo</span>
                </div>
            </div>
            <div class="capa-placeholder" id="capa-placeholder"><i class="fa-solid fa-image"></i></div>
        </section>

        <form class="formulario-tarefa" id="formulario-tarefa" method="POST" enctype="multipart/form-data">
            <div class="guia-layout cadastro-layout">
                <div class="guia-conteudo formulario-coluna">
                    <section class="bloco-guia cartao-formulario">
                        <div class="cabecalho-bloco">
                            <span class="icone-bloco"><i class="fa-solid fa-pen-to-square"></i></span>
                            <div>
                                <h2>Informações gerais</h2>
                                <p>Apresente sua guia e escolha como ela será organizada.</p>
                            </div>
                        </div>

                        <div class="campo">
                            <label for="titulo">Título</label>
                            <input type="text" id="titulo" name="titulo" maxlength="200" placeholder="Ex.: Como fazer um bolo de chocolate" required>
                        </div>
                        <div class="campo">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" maxlength="255" rows="4" placeholder="Conte rapidamente o que essa guia ensina" required></textarea>
                        </div>
                        <div class="linha-campos">
                            <div class="campo">
                                <label for="categoria">Categoria</label>
                                <select id="categoria" name="categoria" required>
                                    <option value="" disabled selected>Selecione uma categoria</option>
                                    <option value="Automóveis">Automóveis</option>
                                    <option value="Casa">Casa</option>
                                    <option value="Compras">Compras</option>
                                    <option value="Cuidados">Cuidados</option>
                                    <option value="Culinária">Culinária</option>
                                    <option value="Educação">Educação</option>
                                    <option value="Finanças">Finanças</option>
                                    <option value="Jardinagem">Jardinagem</option>
                                    <option value="Limpeza">Limpeza</option>
                                    <option value="Manutenção">Manutenção</option>
                                    <option value="Moda">Moda</option>
                                    <option value="Saúde">Saúde</option>
                                    <option value="Tecnologia">Tecnologia</option>
                                    <option value="Trabalho">Trabalho</option>
                                    <option value="Transporte">Transporte</option>
                                </select>
                            </div>
                            <div class="campo">
                                <label for="dificuldade">Dificuldade</label>
                                <select id="dificuldade" name="dificuldade" required>
                                    <option value="" disabled selected>Selecione a dificuldade</option>
                                    <option value="Fácil">Fácil</option>
                                    <option value="Médio">Médio</option>
                                    <option value="Difícil">Difícil</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="bloco-guia cartao-formulario">
                        <div class="cabecalho-bloco">
                            <span class="icone-bloco"><i class="fa-solid fa-images"></i></span>
                            <div>
                                <h2>Imagens e vídeo</h2>
                            </div>
                        </div>
                        <div class="campo">
                            <label for="capa">Capa da guia</label>
                            <input type="file" class="campo-midia" name="capa" id="capa" accept="image/jpeg,image/png,image/webp,image/gif" required>
                            <small>JPG, PNG, WebP ou GIF. Até 5 MB.</small>
                        </div>
                        <div class="campo">
                            <label for="video">Vídeo da guia</label>
                            <input type="url" class="campo-midia" name="video" id="video" maxlength="500" placeholder="https://www.youtube.com/watch?v=..." autocomplete="url" required>
                            <small>Insira o link do vídeo (YouTube, Vimeo ou outro endereço válido).</small>
                        </div>
                    </section>

                    <section class="bloco-guia cartao-formulario">
                        <div class="cabecalho-bloco">
                            <span class="icone-bloco"><i class="fa-solid fa-toolbox"></i></span>
                            <div>
                                <h2>Materiais necessários</h2>
                                <p>Adicione os materiais usados para realizar a guia.</p>
                            </div>
                        </div>
                        <div class="entrada-materiais">
                            <input type="text" id="input-material" placeholder="Ex.: Farinha de trigo" aria-label="Nome do material">
                            <button type="button" id="botao-add-material" class="botao-secundario"><i class="fa-solid fa-plus"></i> Adicionar</button>
                        </div>
                        <ul class="lista-materiais" id="lista-materiais"></ul>
                    </section>

                    <section class="bloco-guia cartao-formulario">
                        <div class="cabecalho-bloco">
                            <span class="icone-bloco"><i class="fa-solid fa-list-ol"></i></span>
                            <div>
                                <h2>Passo a passo</h2>
                            </div>
                        </div>
                        <div class="lista-etapas" id="lista-etapas"></div>
                        <button type="button" id="botao-add-etapa" class="botao-secundario"><i class="fa-solid fa-plus"></i> Adicionar etapa</button>
                    </section>

                    <template id="template-etapa">
                        <article class="etapa bloco-etapa">
                            <div class="cabecalho-bloco cabecalho-secao">
                                <span class="icone-bloco numero-etapa"><span class="etapa-numero">1</span></span>
                                <h3>Etapa</h3>
                                <button type="button" class="botao-remover-etapa" aria-label="Remover etapa"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            <div class="campo">
                                <label>Descrição</label>
                                <textarea class="campo-etapa-descricao" rows="3" maxlength="500" placeholder="Explique o que fazer nesta etapa" required></textarea>
                            </div>
                            <div class="campo">
                                <label>Foto (opcional)</label>
                                <input type="file" class="campo-etapa-midia" accept="image/jpeg,image/png,image/webp,image/gif">
                            </div>
                            <div class="preview-midia etapa-preview" hidden></div>
                        </article>
                    </template>

                    <div class="acoes-formulario">
                        <button type="submit" class="botao-primario" name="publicar"><i class="fa-solid fa-cloud-arrow-up"></i> Publicar guia</button>
                    </div>
                </div>

                <aside class="guia-lateral">
                    <section class="cartao-resumo">
                        <h2>Prévia do conteúdo</h2>
                        <div><i class="fa-solid fa-layer-group"></i>
                            <p><small>Categoria</small><strong id="resumo-categoria">Ainda não selecionada</strong></p>
                        </div>
                        <div><i class="fa-solid fa-signal"></i>
                            <p><small>Dificuldade</small><strong id="resumo-dificuldade">Ainda não selecionada</strong></p>
                        </div>
                        <div><i class="fa-solid fa-list-check"></i>
                            <p><small>Etapas</small><strong id="resumo-etapas">0 passos</strong></p>
                        </div>
                        <div><i class="fa-solid fa-toolbox"></i>
                            <p><small>Materiais</small><strong id="resumo-materiais">0 materiais</strong></p>
                        </div>
                    </section>
                    <section class="cartao-dica">
                        <i class="fa-regular fa-lightbulb"></i>
                        <h3>Dica</h3>
                        <p>Use uma capa clara e escreva etapas objetivas. A prévia é atualizada conforme você preenche o formulário.</p>
                    </section>
                </aside>
            </div>
        </form>
    </main>

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

    <script src="js/header.js"></script>
    <script src="js/cadastro_guias.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (!empty($erro)): ?>
        <script>
            Swal.fire({
                title: "<?= $erro ?>",
                confirmButtonColor: "#263783",
                padding: '25px',
                confirmButtonText: "Ok",
            })
        </script>
    <?php endif; ?>
</body>

</html>