const listaMateriais = document.getElementById('lista-materiais')
const inputMaterial = document.getElementById('input-material')
const botaoAddMaterial = document.getElementById('botao-add-material')

function addMaterial() {
    const valor = inputMaterial.value.trim()

    if (valor === '') {
        return
    }

    const item = document.createElement('li')
    item.className = 'material'

    const texto = document.createElement('span')
    texto.textContent = valor

    const campoOculto = document.createElement('input')
    campoOculto.type = 'hidden'
    campoOculto.name = 'materiais'
    campoOculto.value = valor

    const botaoRemover = document.createElement('button')
    botaoRemover.type = 'button'
    botaoRemover.setAttribute('aria-label', `Remover ${valor}`)
    botaoRemover.innerHTML = '<i class="fa-solid fa-xmark"></i>'
    botaoRemover.addEventListener('click', () => item.remove())

    item.append(texto, campoOculto, botaoRemover)
    listaMateriais.appendChild(item)

    inputMaterial.value = ''
    inputMaterial.focus()
}

botaoAddMaterial.addEventListener('click', addMaterial)

inputMaterial.addEventListener('keydown', (evento) => {
    if (evento.key === 'Enter') {
        evento.preventDefault()
        addMaterial()
    }
})

const listaEtapas = document.getElementById('lista-etapas')
const templateEtapa = document.getElementById('template-etapa')
const botaoAddEtapa = document.getElementById('botao-add-etapa')

function renumerarEtapas() {
    const etapas = listaEtapas.querySelectorAll('.etapa')

    etapas.forEach((etapa, indice) => {
        const numero = indice + 1

        etapa.querySelector('.etapa-numero').textContent = `Etapa ${numero}`

        const campoTitulo = etapa.querySelector('.campo-etapa-titulo')
        const campoDescricao = etapa.querySelector('.campo-etapa-descricao')
        const campoMidia = etapa.querySelector('.campo-etapa-midia')

        campoTitulo.name = `etapas[${indice}][titulo]`
        campoDescricao.name = `etapas[${indice}][descricao]`
        campoMidia.name = `etapas[${indice}][midia]`

        const botaoRemover = etapa.querySelector('.botao-remover-etapa')
        botaoRemover.disabled = etapas.length === 1
    })
}

function addEtapa() {
    const fragmento = templateEtapa.content.cloneNode(true)
    const etapa = fragmento.querySelector('.etapa')

    etapa.querySelector('.botao-remover-etapa').addEventListener('click', () => {
        etapa.remove()
        renumerarEtapas()
    })

    listaEtapas.appendChild(etapa)
    renumerarEtapas()
}

botaoAddEtapa.addEventListener('click', addEtapa)

// toda tarefa começa com uma etapa pronta pra preencher
adicionarEtapa()