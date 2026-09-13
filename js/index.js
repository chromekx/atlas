const categorias = document.getElementById('categorias')
const cartao = document.querySelectorAll('.cartao')

const observador = new IntersectionObserver((entradas) => {
    entradas.forEach((entrada) => {
        if (entrada.isIntersecting) {
            console.log(entrada.target)
            entrada.target.classList.add('entrou')
        } else {
            entrada.target.classList.remove('entrou')
        }
    })
}, {}) // isso é muito legal

observador.observe(categorias)
cartao.forEach((card) => {
    observador.observe(card)
})

const setaEsquerda = document.getElementById('setaEsquerda')
const setaDireita = document.getElementById('setaDireita')

function atualizarSetas() {
    const scrollMaximo = categorias.scrollWidth - categorias.clientWidth
    setaEsquerda.disabled = categorias.scrollLeft <= 0
    setaDireita.disabled = categorias.scrollLeft >= scrollMaximo - 1
}

function rolarCategorias(direcao) {
    const distancia = categorias.clientWidth * 0.8
    categorias.scrollBy({ left: direcao * distancia, behavior: 'smooth' })
}

if (setaEsquerda && setaDireita) {
    setaEsquerda.addEventListener('click', () => rolarCategorias(-1))
    setaDireita.addEventListener('click', () => rolarCategorias(1))
    categorias.addEventListener('scroll', atualizarSetas)
    window.addEventListener('resize', atualizarSetas)
    atualizarSetas()
}

const perfil = document.getElementById('perfil')
const perfilOptions = document.getElementById('perfil-options')
const seta = document.getElementById('seta')
perfil.addEventListener('click', () => {
    perfilOptions.classList.toggle('active')
    seta.classList.toggle('active')
})