<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . "/conexao.php";

$id_guia = $_GET['id'] ?? '';

$sql = "SELECT * FROM guia WHERE id_guia = '$id_guia' AND id_usuario = '{$_SESSION['id']}'";
$resultado = $conn->query($sql);
$guia = $resultado->fetch_assoc();

if (!$guia) {
    header("Location: meu_perfil.php");
    exit();
}

// Busca os materiais atuais.
$sql = "SELECT m.nome
        FROM materiais ma
        INNER JOIN material m ON ma.id_materiais = m.id_materiais
        WHERE ma.id_guia = '$id_guia'";
$resultadoMateriais = $conn->query($sql);

$materiaisAtuais = [];

while ($material = $resultadoMateriais->fetch_assoc()) {
    $materiaisAtuais[] = $material['nome'];
}

// Busca as etapas atuais.
$sql = "SELECT e.id_etapa, e.descricao, e.midia
        FROM etapas es
        INNER JOIN etapa e ON es.id_etapas = e.id_etapas
        WHERE es.id_guia = '$id_guia'
        ORDER BY e.id_etapa";
$resultadoEtapas = $conn->query($sql);

$etapasAtuais = [];

while ($etapa = $resultadoEtapas->fetch_assoc()) {
    $etapasAtuais[] = $etapa;
}

if (isset($_POST['publicar'])) {
    $erro = '';

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

    // Verifica a capa.
    $arquivoCapa = $_FILES['capa'] ?? null;
    $capaSQL = '';

    if ($erro == '' && $arquivoCapa && $arquivoCapa['error'] != UPLOAD_ERR_NO_FILE) {
        if ($arquivoCapa['error'] != UPLOAD_ERR_OK) {
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
                    $capaSQL = "0x" . bin2hex($capa);
                }
            }
        }
    }

    // Atualiza as informações da guia.
    if ($erro == '') {
        $sql = "UPDATE guia SET
                    titulo = '$titulo',
                    descricao = '$descricao',
                    categoria = '$categoria',
                    dificuldade = '$dificuldade',
                    video = '$video'";

        if ($capaSQL != '') {
            $sql .= ", capa = $capaSQL";
        }

        $sql .= " WHERE id_guia = '$id_guia'";

        if (!$conn->query($sql)) {
            $erro = 'Erro ao atualizar a guia: ' . $conn->error;
        }
    }

    // Atualiza os materiais.
    if ($erro == '') {
        $sql = "SELECT id_materiais FROM materiais WHERE id_guia = '$id_guia'";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            $registro = $resultado->fetch_assoc();
            $idMateriais = $registro['id_materiais'];

            $sql = "DELETE FROM material WHERE id_materiais = '$idMateriais'";
            if (!$conn->query($sql)) {
                $erro = 'Erro ao atualizar os materiais: ' . $conn->error;
            }
        } else {
            $sql = "INSERT INTO materiais (id_guia) VALUES ('$id_guia')";

            if ($conn->query($sql)) {
                $idMateriais = $conn->insert_id;
            } else {
                $erro = 'Erro ao cadastrar os materiais: ' . $conn->error;
            }
        }

        if ($erro == '' && is_array($materiais)) {
            foreach ($materiais as $material) {
                $material = trim($material);

                if ($material == '') {
                    continue;
                }

                $sql = "INSERT INTO material (id_materiais, nome)
                        VALUES ('$idMateriais', '$material')";

                if (!$conn->query($sql)) {
                    $erro = 'Erro ao atualizar um material: ' . $conn->error;
                    break;
                }
            }
        }
    }

    // Busca ou cria o agrupamento das etapas.
    if ($erro == '') {
        $sql = "SELECT id_etapas FROM etapas WHERE id_guia = '$id_guia'";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            $registro = $resultado->fetch_assoc();
            $idEtapas = $registro['id_etapas'];
        } else {
            $sql = "INSERT INTO etapas (id_guia) VALUES ('$id_guia')";

            if ($conn->query($sql)) {
                $idEtapas = $conn->insert_id;
            } else {
                $erro = 'Erro ao atualizar as etapas: ' . $conn->error;
            }
        }
    }

    // Atualiza, adiciona e remove as etapas.
    if ($erro == '' && is_array($etapas)) {
        $idsMantidos = [];

        foreach ($etapas as $indice => $etapa) {
            $etapaDesc = trim($etapa['descricao'] ?? '');
            $idEtapa = $etapa['id_etapa'] ?? '';

            if ($etapaDesc == '') {
                continue;
            }

            $midiaSQL = '';
            $codigoErro = $_FILES['etapas']['error'][$indice]['midia'] ?? UPLOAD_ERR_NO_FILE;

            // Verifica se foi enviada uma nova mídia.
            if ($codigoErro != UPLOAD_ERR_NO_FILE) {
                if ($codigoErro != UPLOAD_ERR_OK) {
                    $erro = 'Erro ao enviar a mídia da etapa.';
                    break;
                }

                $tmpName = $_FILES['etapas']['tmp_name'][$indice]['midia'];
                $tamanho = $_FILES['etapas']['size'][$indice]['midia'];

                if (!is_uploaded_file($tmpName)) {
                    $erro = 'Arquivo da etapa inválido.';
                } else if ($tamanho > (5 * 1024 * 1024)) {
                    $erro = 'A mídia de cada etapa deve ter no máximo 5 MB.';
                } else {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $tipo = finfo_file($finfo, $tmpName);
                    finfo_close($finfo);

                    if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4'])) {
                        $erro = 'A mídia da etapa deve ser uma imagem ou um vídeo MP4.';
                    } else {
                        $midia = file_get_contents($tmpName);
                        $midiaSQL = "0x" . bin2hex($midia);
                    }
                }
            }

            if ($erro != '') {
                break;
            }

            // Atualiza uma etapa existente.
            if ($idEtapa != '') {
                $sql = "UPDATE etapa SET descricao = '$etapaDesc'";

                if ($midiaSQL != '') {
                    $sql .= ", midia = $midiaSQL";
                }

                $sql .= " WHERE id_etapa = '$idEtapa' AND id_etapas = '$idEtapas'";

                if ($conn->query($sql)) {
                    $idsMantidos[] = $idEtapa;
                } else {
                    $erro = 'Erro ao atualizar uma etapa: ' . $conn->error;
                    break;
                }
            } else {
                // Cadastra uma nova etapa.
                if ($midiaSQL == '') {
                    $midiaSQL = 'NULL';
                }

                $sql = "INSERT INTO etapa (id_etapas, descricao, midia)
                        VALUES ('$idEtapas', '$etapaDesc', $midiaSQL)";

                if ($conn->query($sql)) {
                    $idsMantidos[] = $conn->insert_id;
                } else {
                    $erro = 'Erro ao cadastrar uma etapa: ' . $conn->error;
                    break;
                }
            }
        }

        // Remove as etapas que foram retiradas do formulário.
        if ($erro == '') {
            if (count($idsMantidos) > 0) {
                $ids = implode(',', $idsMantidos);

                $sql = "DELETE FROM etapa
                        WHERE id_etapas = '$idEtapas'
                        AND id_etapa NOT IN ($ids)";
            } else {
                $sql = "DELETE FROM etapa WHERE id_etapas = '$idEtapas'";
            }

            if (!$conn->query($sql)) {
                $erro = 'Erro ao remover etapas antigas: ' . $conn->error;
            }
        }
    }

    // Redireciona depois de atualizar tudo.
    if ($erro == '') {
        header("Location: guia.php?id=$id_guia");
        exit();
    }

    // Atualiza os dados exibidos caso tenha ocorrido algum erro.
    $guia['titulo'] = $titulo;
    $guia['descricao'] = $descricao;
    $guia['categoria'] = $categoria;
    $guia['dificuldade'] = $dificuldade;
    $guia['video'] = $video;
}

function h($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
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

    <title>ATLAS - Editar Guia</title>
    <link rel="stylesheet" href="css/editar_guia.css">
    <link rel="icon" href="imgs_website/logoatlas.png" type="image/png">
</head>

<body>

    <header>
        <a class="logo" href="index.php" aria-label="Página inicial">
            <img src="imgs_website/logotipoatlas.png" alt="ATLAS">
        </a>

        <nav class="nav-options" aria-label="Navegação principal">
            <a class="item" href="guias.php">
                <p>Explorar Guias</p>
            </a>

            <a class="item" href="cadastrar_guia.php">
                <p>Cadastrar Guia</p>
            </a>

            <a class="item" href="index.php#sobre-atlas">
                <p>Sobre o ATLAS</p>
            </a>

            <a class="item" href="suporte.php">
                <p>Suporte</p>
            </a>
        </nav>

        <nav class="nav-btns" aria-label="Conta">
            <div class="perfil" id="perfil">
                <p>
                    <img class="foto" src="imgs_perfil/<?= !empty($_SESSION['foto']) ? h($_SESSION['foto']) : 'foto_padrao.png' ?>">
                    Olá, <?= h($_SESSION['nome']) ?>
                </p>

                <i class="fa-solid fa-caret-up" id="seta"></i>

                <div class="perfil-options" id="perfil-options">
                    <a class="perfil-option" href="meu_perfil.php">Meu perfil</a>

                    <?php if (($_SESSION['nivel'] ?? '') == 1): ?>
                        <a class="perfil-option" href="admin.php">Painel do administrador</a>
                    <?php endif; ?>

                    <a class="perfil-option" href="sair.php">Sair</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="pagina-cadastro">

        <section class="cabecalho-pagina">
            <h1>Editar Guia</h1>
        </section>

        <section class="guia-capa preview-capa" id="preview-capa">
            <img class="guia-capa-imagem"
                src="exibir_imagem_guia.php?tipo=capa&id=<?= (int) $id_guia ?>"
                loading="eager"
                onerror="this.style.display='none';this.parentElement.classList.add('sem-capa')">

            <div class="guia-capa-sombra"></div>

            <div class="guia-capa-conteudo">
                <span class="guia-categoria">
                    <i class="fa-solid fa-book-open"></i>
                    <span id="preview-categoria"><?= h($guia['categoria']) ?></span>
                </span>

                <h2 id="preview-titulo"><?= h($guia['titulo']) ?></h2>
                <p id="preview-descricao"><?= h($guia['descricao']) ?></p>

                <div class="guia-capa-meta">
                    <span>
                        <i class="fa-solid fa-signal"></i>
                        <span id="preview-dificuldade"><?= h($guia['dificuldade']) ?></span>
                    </span>

                    <span>
                        <i class="fa-regular fa-user"></i>
                        <?= h($_SESSION['nome']) ?>
                    </span>
                </div>
            </div>

            <div class="capa-placeholder" id="capa-placeholder">
                <i class="fa-solid fa-image"></i>
            </div>
        </section>

        <form class="formulario-tarefa" id="formulario-tarefa" method="POST" enctype="multipart/form-data">

            <div class="guia-layout cadastro-layout">

                <div class="guia-conteudo formulario-coluna">

                    <section class="bloco-guia cartao-formulario">
                        <div class="cabecalho-bloco">
                            <span class="icone-bloco"><i class="fa-solid fa-pen-to-square"></i></span>
                            <div>
                                <h2>Informações gerais</h2>
                            </div>
                        </div>

                        <div class="campo">
                            <label for="titulo">Título</label>
                            <input type="text" value="<?= h($guia['titulo']) ?>" id="titulo" name="titulo" maxlength="200" placeholder="Ex.: Como fazer um bolo de chocolate" required>
                        </div>

                        <div class="campo">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" maxlength="255" rows="4" placeholder="Conte rapidamente o que essa guia ensina" required><?= h($guia['descricao']) ?></textarea>
                        </div>

                        <div class="linha-campos">
                            <div class="campo">
                                <label for="categoria">Categoria</label>

                                <select id="categoria" name="categoria" required>
                                    <option value="" disabled>Selecione uma categoria</option>

                                    <?php
                                    $categorias = [
                                        'Automóveis',
                                        'Casa',
                                        'Compras',
                                        'Cuidados',
                                        'Culinária',
                                        'Educação',
                                        'Finanças',
                                        'Jardinagem',
                                        'Limpeza',
                                        'Manutenção',
                                        'Moda',
                                        'Saúde',
                                        'Tecnologia',
                                        'Trabalho',
                                        'Transporte'
                                    ];

                                    foreach ($categorias as $categoria) {
                                    ?>
                                        <option value="<?= h($categoria) ?>" <?= $guia['categoria'] == $categoria ? 'selected' : '' ?>>
                                            <?= h($categoria) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <label for="dificuldade">Dificuldade</label>

                                <select id="dificuldade" name="dificuldade" required>
                                    <option value="" disabled>Selecione a dificuldade</option>
                                    <option value="Fácil" <?= $guia['dificuldade'] == 'Fácil' ? 'selected' : '' ?>>Fácil</option>
                                    <option value="Médio" <?= $guia['dificuldade'] == 'Médio' ? 'selected' : '' ?>>Médio</option>
                                    <option value="Difícil" <?= $guia['dificuldade'] == 'Difícil' ? 'selected' : '' ?>>Difícil</option>
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
                            <input type="file" class="campo-midia" name="capa" id="capa" accept="image/jpeg,image/png,image/webp,image/gif">
                            <small>JPG, PNG, WebP ou GIF. Até 5 MB. Deixe vazio para manter a capa atual.</small>
                        </div>

                        <div class="campo">
                            <label for="video">Vídeo da guia</label>
                            <input type="url" class="campo-midia" name="video" id="video" value="<?= h($guia['video']) ?>" maxlength="500" placeholder="https://www.youtube.com/watch?v=..." required>
                            <small>Insira o link do vídeo.</small>
                        </div>
                    </section>

                    <section class="bloco-guia cartao-formulario">
                        <div class="cabecalho-bloco">
                            <span class="icone-bloco"><i class="fa-solid fa-toolbox"></i></span>
                            <div>
                                <h2>Materiais necessários</h2>
                            </div>
                        </div>

                        <div class="entrada-materiais">
                            <input type="text" id="input-material" placeholder="Ex.: Farinha de trigo" aria-label="Nome do material">
                            <button type="button" id="botao-add-material" class="botao-secundario">
                                <i class="fa-solid fa-plus"></i> Adicionar
                            </button>
                        </div>

                        <ul class="lista-materiais" id="lista-materiais">
                            <?php foreach ($materiaisAtuais as $material) { ?>
                                <li>
                                    <i class="fa-solid fa-check"></i>
                                    <span><?= h($material) ?></span>

                                    <input type="hidden" name="material[]" value="<?= h($material) ?>">

                                    <button type="button" aria-label="Remover material">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </li>
                            <?php } ?>
                        </ul>
                    </section>

                    <section class="bloco-guia cartao-formulario">
                        <div class="cabecalho-bloco">
                            <span class="icone-bloco"><i class="fa-solid fa-list-ol"></i></span>
                            <div>
                                <h2>Passo a passo</h2>
                            </div>
                        </div>

                        <div class="lista-etapas" id="lista-etapas">

                            <?php foreach ($etapasAtuais as $indice => $etapa) { ?>
                                <article class="etapa bloco-etapa">

                                    <div class="cabecalho-bloco cabecalho-secao">
                                        <span class="icone-bloco numero-etapa">
                                            <span class="etapa-numero"><?= $indice + 1 ?></span>
                                        </span>

                                        <h3>Etapa <?= $indice + 1 ?></h3>

                                        <button type="button" class="botao-remover-etapa" aria-label="Remover etapa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>

                                    <input type="hidden" class="campo-etapa-id" value="<?= (int) $etapa['id_etapa'] ?>">

                                    <div class="campo">
                                        <label>Descrição</label>
                                        <textarea class="campo-etapa-descricao" rows="3" maxlength="500" placeholder="Explique o que fazer nesta etapa" required><?= h($etapa['descricao']) ?></textarea>
                                    </div>

                                    <div class="campo">
                                        <label>Mídia (opcional)</label>
                                        <input type="file" class="campo-etapa-midia" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4">
                                    </div>
                                </article>
                            <?php } ?>

                        </div>

                        <button type="button" id="botao-add-etapa" class="botao-secundario">
                            <i class="fa-solid fa-plus"></i> Adicionar etapa
                        </button>
                    </section>

                    <template id="template-etapa">
                        <article class="etapa bloco-etapa">

                            <div class="cabecalho-bloco cabecalho-secao">
                                <span class="icone-bloco numero-etapa">
                                    <span class="etapa-numero">1</span>
                                </span>

                                <h3>Etapa</h3>

                                <button type="button" class="botao-remover-etapa" aria-label="Remover etapa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>

                            <input type="hidden" class="campo-etapa-id">

                            <div class="campo">
                                <label>Descrição</label>
                                <textarea class="campo-etapa-descricao" rows="3" maxlength="500" placeholder="Explique o que fazer nesta etapa" required></textarea>
                            </div>

                            <div class="campo">
                                <label>Mídia (opcional)</label>
                                <input type="file" class="campo-etapa-midia" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4">
                            </div>

                            <div class="preview-midia etapa-preview" hidden></div>

                        </article>
                    </template>

                    <div class="acoes-formulario">
                        <button type="submit" class="botao-primario" name="publicar">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Salvar alterações
                        </button>
                    </div>

                </div>

                <aside class="guia-lateral">

                    <section class="cartao-resumo">
                        <h2>Prévia do conteúdo</h2>

                        <div>
                            <i class="fa-solid fa-layer-group"></i>
                            <p>
                                <small>Categoria</small>
                                <strong id="resumo-categoria"><?= h($guia['categoria']) ?></strong>
                            </p>
                        </div>

                        <div>
                            <i class="fa-solid fa-signal"></i>
                            <p>
                                <small>Dificuldade</small>
                                <strong id="resumo-dificuldade"><?= h($guia['dificuldade']) ?></strong>
                            </p>
                        </div>

                        <div>
                            <i class="fa-solid fa-list-check"></i>
                            <p>
                                <small>Etapas</small>
                                <strong id="resumo-etapas"><?= count($etapasAtuais) ?> passos</strong>
                            </p>
                        </div>

                        <div>
                            <i class="fa-solid fa-toolbox"></i>
                            <p>
                                <small>Materiais</small>
                                <strong id="resumo-materiais"><?= count($materiaisAtuais) ?> materiais</strong>
                            </p>
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
                <a class="footer-logo" href="index.php" aria-label="Voltar para o início">
                    <img src="imgs_website/logoatlas.png" alt="ATLAS">
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
                <a href="index.php">Início</a>
                <a href="cadastrar_guia.php">Cadastrar Guia</a>
                <a href="suporte.php">Suporte</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/editar_guia.js"></script>
    <?php if (!empty($erro)): ?>
        <script>
            Swal.fire({
                title: <?= json_encode($erro) ?>,
                confirmButtonColor: "#263783",
                padding: '25px',
                confirmButtonText: "Ok"
            });
        </script>
    <?php endif; ?>
</body>

</html>