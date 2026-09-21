const abas = document.querySelectorAll('.aba-perfil')
const paineis = {
    tarefas: document.getElementById('painel-tarefas'),
    favoritos: document.getElementById('painel-favoritos'),
    historico: document.getElementById('painel-historico'),
}

abas.forEach((aba) => {
    aba.addEventListener('click', () => {
        abas.forEach((outraAba) => outraAba.classList.remove('ativa'))
        aba.classList.add('ativa')

        Object.values(paineis).forEach((painel) => painel.hidden = true)
        paineis[aba.dataset.aba].hidden = false
    })
})