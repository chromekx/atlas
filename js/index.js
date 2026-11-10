const hero = document.getElementById('hero')
const categorias = document.getElementById('categorias')
const sobrenos = document.getElementById('equipe-introducao')
const integrantes = document.querySelectorAll('.integrante-card')

const observador = new IntersectionObserver((entradas) => { // isso é muito legal
    entradas.forEach((entrada) => {
        if (entrada.isIntersecting) {
            console.log(entrada.target)
            entrada.target.classList.add('entrou')
        } else {
            entrada.target.classList.remove('entrou') 
            console.log(entrada.target + "apareceu")
        }
    })
}, { threshold: 0.2 }) // só aparece se aparecer pelo menos 20% do elemento, e vice-versa

observador.observe(hero)
observador.observe(categorias)
observador.observe(sobrenos)

integrantes.forEach((integrante) => {
    observador.observe(integrante)
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