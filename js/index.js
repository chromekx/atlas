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

const perfil = document.getElementById('perfil')
const perfilOptions = document.getElementById('perfil-options')
const seta = document.getElementById('seta')
perfil.addEventListener('click', () => {
    perfilOptions.classList.toggle('active')
    seta.classList.toggle('active')
})