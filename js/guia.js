const capa = document.querySelector('.guia-capa') //querySelector para php
const sections = document.querySelectorAll('.bloco-guia')

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
}, { threshold: 0.1 }) // só aparece se aparecer pelo menos 20% do elemento, e vice-versa

sections.forEach((section) => {
    observador.observe(section)
})
observador.observe(capa)