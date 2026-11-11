document.addEventListener('DOMContentLoaded', () => {

    const $ = (seletor, contexto = document) => contexto.querySelector(seletor);

    const formulario = $('#formulario-tarefa');

    const titulo = $('#titulo');
    const descricao = $('#descricao');
    const categoria = $('#categoria');
    const dificuldade = $('#dificuldade');

    const capaInput = $('#capa');
    const capaPreview = $('#preview-capa');
    const capaImagem = $('.guia-capa-imagem');
    const placeholder = $('#capa-placeholder');

    const listaMateriais = $('#lista-materiais');
    const inputMaterial = $('#input-material');

    const listaEtapas = $('#lista-etapas');
    const templateEtapa = $('#template-etapa');

    let urlCapaAtual = null;
    const urlsEtapas = [];

    function atualizarTexto() {
        $('#preview-titulo').textContent = titulo.value;
        $('#preview-descricao').textContent = descricao.value;

        $('#preview-categoria').textContent = categoria.value || 'Categoria';
        $('#preview-dificuldade').textContent = dificuldade.value || 'Dificuldade';

        $('#resumo-categoria').textContent = categoria.value || 'Ainda não selecionada';
        $('#resumo-dificuldade').textContent = dificuldade.value || 'Ainda não selecionada';
    }

    [titulo, descricao, categoria, dificuldade].forEach(campo => {
        campo.addEventListener('input', atualizarTexto);
        campo.addEventListener('change', atualizarTexto);
    });

    // Prévia da capa.
    capaInput.addEventListener('change', () => {
        const arquivo = capaInput.files && capaInput.files[0];

        if (urlCapaAtual) {
            URL.revokeObjectURL(urlCapaAtual);
            urlCapaAtual = null;
        }

        if (!arquivo) return;

        if (!arquivo.type.startsWith('image/')) {
            capaInput.value = '';
            alert('Selecione um arquivo de imagem para a capa.');
            return;
        }

        urlCapaAtual = URL.createObjectURL(arquivo);

        capaImagem.src = urlCapaAtual;
        capaImagem.style.display = 'block';

        capaPreview.classList.remove('sem-capa');
        placeholder.hidden = true;
    });

    // Atualiza a quantidade de materiais e etapas.
    function atualizarContagem() {
        const materiais = listaMateriais.querySelectorAll('input[name="material[]"]');
        const etapas = listaEtapas.querySelectorAll('.bloco-etapa');

        $('#resumo-materiais').textContent =
            `${materiais.length} ${materiais.length === 1 ? 'material' : 'materiais'}`;

        $('#resumo-etapas').textContent =
            `${etapas.length} ${etapas.length === 1 ? 'passo' : 'passos'}`;
    }

    // Remove um material.
    function configurarRemocaoMaterial(item) {
        const botao = $('button', item);

        if (botao) {
            botao.addEventListener('click', () => {
                item.remove();
                atualizarContagem();
            });
        }
    }

    listaMateriais.querySelectorAll('li').forEach(configurarRemocaoMaterial);

    // Adiciona um material.
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

        item.append(icone, texto, input, remover);

        listaMateriais.append(item);

        configurarRemocaoMaterial(item);

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

    // Atualiza os números e nomes dos campos das etapas.
    function atualizarEtapas() {
        [...listaEtapas.querySelectorAll('.bloco-etapa')].forEach((etapa, indice) => {
            const numero = indice + 1;

            $('.etapa-numero', etapa).textContent = numero;
            $('.cabecalho-secao h3', etapa).textContent = `Etapa ${numero}`;

            $('.campo-etapa-id', etapa).name = `etapas[${indice}][id_etapa]`;
            $('.campo-etapa-descricao', etapa).name = `etapas[${indice}][descricao]`;
            $('.campo-etapa-midia', etapa).name = `etapas[${indice}][midia]`;
        });

        atualizarContagem();
    }

    // Configura uma etapa.
    function configurarEtapa(etapa) {
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

            if (!arquivo) return;

            if (!arquivo.type.startsWith('image/') && arquivo.type !== 'video/mp4') {
                inputArquivo.value = '';
                alert('Use uma imagem ou um vídeo MP4 na etapa.');
                return;
            }

            const url = URL.createObjectURL(arquivo);
            urlsEtapas.push(url);

            let midia;

            if (arquivo.type === 'video/mp4' || arquivo.name.toLowerCase().endsWith('.mp4')) {
                midia = document.createElement('video');
                midia.controls = true;
                midia.preload = 'metadata';
                midia.src = url;
            } else {
                midia = document.createElement('img');
                midia.alt = 'Prévia da mídia da etapa';
                midia.src = url;
            }

            wrapper.hidden = false;
            wrapper.append(midia);
        });
    }

    // Configura as etapas que já existem.
    listaEtapas.querySelectorAll('.bloco-etapa').forEach(configurarEtapa);

    // Adiciona uma nova etapa.
    function adicionarEtapa() {
        const fragmento = templateEtapa.content.cloneNode(true);
        const etapa = $('.bloco-etapa', fragmento);

        configurarEtapa(etapa);

        listaEtapas.append(fragmento);

        atualizarEtapas();
        $('.campo-etapa-descricao', etapa).focus();
    }

    $('#botao-add-etapa').addEventListener('click', adicionarEtapa);

    // Atualiza os nomes dos campos ao carregar.
    atualizarEtapas();
    atualizarTexto();
    atualizarContagem();

    formulario.addEventListener('submit', evento => {
        const descricoes = [...listaEtapas.querySelectorAll('.campo-etapa-descricao')];

        const etapaVazia = descricoes.find(campo => !campo.value.trim());

        if (etapaVazia) {
            evento.preventDefault();
            etapaVazia.focus();

            alert('Preencha a descrição de todas as etapas ou remova as etapas vazias.');
        }
    });

    window.addEventListener('beforeunload', () => {
        if (urlCapaAtual) {
            URL.revokeObjectURL(urlCapaAtual);
        }

        urlsEtapas.forEach(url => URL.revokeObjectURL(url));
    });

});