const perfil = document.getElementById('perfil')
const perfilOptions = document.getElementById('perfil-options')
const seta = document.getElementById('seta')
perfil.addEventListener('click', () => {
    perfilOptions.classList.toggle('active')
    seta.classList.toggle('active')
})

function confirmarExclusao(id) {
    Swal.fire({
        title: "Excluir Usuário",
        text: "Tem certeza que deseja excluir este usuário?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#0a46a0",
        cancelButtonColor: "#dc3545",
        confirmButtonText: "Sim, excluir",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'excluirUsuario.php?id=' + id;
        }
    });
}