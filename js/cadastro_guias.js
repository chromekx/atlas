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
        $('#preview-titulo').textContent = textoSeguro(titulo.value) || 'Título da sua guia';
        $('#preview-descricao').textContent = textoSeguro(descricao.value) || 'A descrição da sua guia aparecerá aqui enquanto você preenche o formulário.';

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
