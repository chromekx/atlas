const guias = document.querySelectorAll('.guia-card') //querySelector para php

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

guias.forEach((guia) => {
    observador.observe(guia)
})