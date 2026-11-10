<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . "/conexao.php";
$id_guia = $_GET['id'];

$sql = "SELECT id_guia, titulo, descricao, categoria, dificuldade, capa FROM guia WHERE id_guia = '$id_guia'";
$resultado = $conn->query($sql);
$guia = $resultado->fetch_assoc();

if (isset($_POST['publicar'])) {
    $erro = '';

    // Faz o query retornar false quando houver erro, em vez de lançar exceção.
    mysqli_report(MYSQLI_REPORT_OFF);

    $id_usuario = $_SESSION['id'];
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
        $sql = "INSERT INTO guia (id_usuario, titulo, descricao, categoria, dificuldade, capa, video)
                VALUES ('$id_usuario', '$titulo', '$descricao', '$categoria', '$dificuldade', $capaSQL, '$video')";

        if ($conn->query($sql)) {
            $id_guia = $conn->insert_id;
        } else {
            $erro = 'Erro ao cadastrar a guia: ' . $conn->error;
        }
    }

    // Cadastra o agrupamento dos materiais.
    if ($erro == '') {
        $sql = "UPDATE INTO materiais (id_guia) VALUES ('$id_guia')";

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
        $sql = "INSERT INTO etapas (id_guia) VALUES ('$id_guia')";

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

function h($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
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

            <?php if (isset($_SESSION['id'])): ?>
                <a class="item" href="cadastro_guias.php">
                    <p>Cadastrar Guia</p>
                </a>
            <?php endif; ?>

            <a class="item" href="index.php#sobre-atlas">
                <p>Sobre o ATLAS</p>
            </a>

            <a class="item" href="suporte.php">
                <p>Suporte</p>
            </a>
        </nav>

        <nav class="nav-btns" aria-label="Conta">
            <?php if (isset($_SESSION['id'])): ?>

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

            <?php else: ?>
                <a class="entrar" href="login.php">Entrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="pagina-cadastro">
        <section class="cabecalho-pagina">
            <h1>Editar Guia</h1>
        </section>


        <section class="guia-capa preview-capa sem-capa" id="preview-capa">
            <img class="guia-capa-imagem"
                src="exibir_imagem_guia.php?tipo=capa&id=<?= (int)$id_guia ?>"
                loading="eager"
                onerror="this.style.display='none';this.parentElement.classList.add('sem-capa')">
            <div class="guia-capa-sombra"></div>
            <div class="guia-capa-conteudo">
                <span class="guia-categoria"><i class="fa-solid fa-book-open"></i> <span id="preview-categoria">Categoria</span></span>
                <h2 id="preview-titulo"></h2>
                <p id="preview-descricao"></p>
                <div class="guia-capa-meta">
                    <span><i class="fa-solid fa-signal"></i> <span id="preview-dificuldade">Dificuldade</span></span>
                    <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($_SESSION['nome'] ?? 'Comunidade ATLAS', ENT_QUOTES, 'UTF-8') ?></span>
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
                            </div>
                        </div>

                        <div class="campo">
                            <label for="titulo">Título</label>
                            <input type="text" value="<?= $guia['titulo'] ?>" id="titulo" name="titulo" maxlength="200" placeholder="Ex.: Como fazer um bolo de chocolate" required>
                        </div>
                        <div class="campo">
                            <label for="descricao">Descrição</label>
                            <input type="text" id="descricao" value="<?= $guia['descricao'] ?>" name="descricao" maxlength="255" rows="4" placeholder="Conte rapidamente o que essa guia ensina" required></textarea>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (!empty($erro)): ?>
        <script>
            Swal.fire({
                title: "<?= $erro ?>",
                confirmButtonColor: "#263783",
                padding: '25px',
                confirmButtonText: "Ok",
            })

            document.addEventListener('DOMContentLoaded', () => {
                const $ = (seletor, contexto = document) => contexto.querySelector(seletor);
                const formulario = $('#formulario-tarefa');
                if (!formulario) return;

                const titulo = $('#titulo');
                const descricao = $('#descricao');
                const categoria = $('#categoria');
                const dificuldade = $('#dificuldade');
                const capaInput = $('#capa');
                const capaPreview = $('#preview-capa');
                const capaImagem = $('#preview-capa-imagem');
                const placeholder = $('#capa-placeholder');
                const listaMateriais = $('#lista-materiais');
                const inputMaterial = $('#input-material');
                const listaEtapas = $('#lista-etapas');
                const templateEtapa = $('#template-etapa');
                let urlCapaAtual = null;
                const urlsEtapas = [];

                function textoSeguro(valor) {
                    return valor && valor.trim() ? valor.trim() : '';
                }

                function atualizarTexto() {
                    $('#preview-titulo').textContent = textoSeguro(titulo.value) || "<?= $guia['titulo'] ?>";
                    $('#preview-descricao').textContent = textoSeguro(descricao.value) || "<?= $guia['descricao'] ?>";

                    const cat = categoria.value || 'Categoria';
                    const dif = dificuldade.value || 'Dificuldade';

                    $('#preview-categoria').textContent = cat;
                    $('#preview-dificuldade').textContent = dif;
                    $('#resumo-categoria').textContent = categoria.value || 'Ainda não selecionada';
                    $('#resumo-dificuldade').textContent = dificuldade.value || 'Ainda não selecionada';
                }

                [titulo, descricao, categoria, dificuldade].forEach(campo => {
                    campo.addEventListener('input', atualizarTexto);
                    campo.addEventListener('change', atualizarTexto);
                });

                capaInput.addEventListener('change', () => {
                    const arquivo = capaInput.files && capaInput.files[0];

                    if (urlCapaAtual) {
                        URL.revokeObjectURL(urlCapaAtual);
                        urlCapaAtual = null;
                    }

                    if (!arquivo) {
                        capaImagem.removeAttribute('src');
                        capaImagem.hidden = true;
                        capaPreview.classList.add('sem-capa');
                        placeholder.hidden = false;
                        return;
                    }

                    if (!arquivo.type.startsWith('image/')) {
                        capaInput.value = '';
                        alert('Selecione um arquivo de imagem para a capa.');
                        return;
                    }

                    urlCapaAtual = URL.createObjectURL(arquivo);
                    capaImagem.src = urlCapaAtual;
                    capaImagem.hidden = false;
                    capaPreview.classList.remove('sem-capa');
                    placeholder.hidden = true;
                });

                function atualizarContagem() {
                    const materiais = listaMateriais.querySelectorAll('input[name="material[]"]');
                    const etapas = listaEtapas.querySelectorAll('.bloco-etapa');

                    $('#resumo-materiais').textContent = `${materiais.length} ${materiais.length === 1 ? 'material' : 'materiais'}`;
                    $('#resumo-etapas').textContent = `${etapas.length} ${etapas.length === 1 ? 'passo' : 'passos'}`;
                }

                function adicionarMaterial() {
                    const nome = inputMaterial.value.trim();
                    if (!nome) {
                        inputMaterial.focus();
                        return;
                    }

                    const item = document.createElement('li');
                    const icone = document.createElement('i');
                    icone.className = 'fa-solid fa-check';

                    const texto = document.createElement('span');
                    texto.textContent = nome;

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'material[]';
                    input.value = nome;

                    const remover = document.createElement('button');
                    remover.type = 'button';
                    remover.setAttribute('aria-label', 'Remover material');
                    remover.innerHTML = '<i class="fa-solid fa-trash"></i>';
                    remover.addEventListener('click', () => {
                        item.remove();
                        atualizarContagem();
                    });

                    item.append(icone, texto, input, remover);
                    listaMateriais.append(item);
                    inputMaterial.value = '';
                    inputMaterial.focus();
                    atualizarContagem();
                }

                $('#botao-add-material').addEventListener('click', adicionarMaterial);
                inputMaterial.addEventListener('keydown', evento => {
                    if (evento.key === 'Enter') {
                        evento.preventDefault();
                        adicionarMaterial();
                    }
                });

                function atualizarEtapas() {
                    [...listaEtapas.querySelectorAll('.bloco-etapa')].forEach((etapa, indice) => {
                        const numero = indice + 1;
                        $('.etapa-numero', etapa).textContent = numero;
                        $('.cabecalho-secao h3', etapa).textContent = `Etapa ${numero}`;
                        $('.campo-etapa-descricao', etapa).name = `etapas[${indice}][descricao]`;
                        $('.campo-etapa-midia', etapa).name = `etapas[${indice}][midia]`;
                    });

                    atualizarContagem();
                }

                function adicionarEtapa() {
                    const fragmento = templateEtapa.content.cloneNode(true);
                    const etapa = $('.bloco-etapa', fragmento);
                    const textarea = $('.campo-etapa-descricao', etapa);
                    const inputArquivo = $('.campo-etapa-midia', etapa);

                    $('.botao-remover-etapa', etapa).addEventListener('click', () => {
                        etapa.remove();
                        atualizarEtapas();
                    });

                    inputArquivo.addEventListener('change', () => {
                        const wrapper = $('.etapa-preview', etapa);
                        wrapper.replaceChildren();

                        const arquivo = inputArquivo.files && inputArquivo.files[0];
                        wrapper.hidden = !arquivo;
                        if (!arquivo) return;

                        const url = URL.createObjectURL(arquivo);
                        urlsEtapas.push(url);
                        let midia;

                        if (arquivo.type.startsWith('video/') || arquivo.name.toLowerCase().endsWith('.mp4')) {
                            midia = document.createElement('video');
                            midia.controls = true;
                            midia.preload = 'metadata';
                            midia.src = url;
                        } else if (arquivo.type.startsWith('image/')) {
                            midia = document.createElement('img');
                            midia.alt = 'Prévia da mídia da etapa';
                            midia.src = url;
                        } else {
                            wrapper.hidden = true;
                            inputArquivo.value = '';
                            alert('Use uma imagem ou um vídeo MP4 na etapa.');
                            return;
                        }

                        wrapper.append(midia);
                    });

                    listaEtapas.append(fragmento);
                    atualizarEtapas();
                    textarea.focus();
                }

                $('#botao-add-etapa').addEventListener('click', adicionarEtapa);

                formulario.addEventListener('submit', evento => {
                    const descricoes = [...listaEtapas.querySelectorAll('.campo-etapa-descricao')];
                    const etapaVazia = descricoes.find(campo => !campo.value.trim());

                    if (etapaVazia) {
                        evento.preventDefault();
                        etapaVazia.focus();
                        alert('Preencha a descrição de todas as etapas adicionadas ou remova as etapas vazias.');
                    }
                });

                window.addEventListener('beforeunload', () => {
                    if (urlCapaAtual) URL.revokeObjectURL(urlCapaAtual);
                    urlsEtapas.forEach(url => URL.revokeObjectURL(url));
                });

                atualizarTexto();
                atualizarContagem();
                adicionarEtapa();
            });
        </script>
    <?php endif; ?>
</body>

</html>