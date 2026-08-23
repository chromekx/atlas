const perfil = document.getElementById('perfil')
const perfilOptions = document.getElementById('perfil-options')
const seta = document.getElementById('seta')
perfil.addEventListener('click', () => {
    perfilOptions.classList.toggle('active')
    seta.classList.toggle('active')
})