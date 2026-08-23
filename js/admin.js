function inativarUsuario(id) {
    Swal.fire({
        title: "Tem certeza que deseja inativar este usuário?",
        padding: "25px",
        color: '#000',
        showCancelButton: true,
        confirmButtonColor: "#1bb155",
        cancelButtonColor: "#cc2828",
        confirmButtonText: "Sim, inativar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'inativar_usuario.php?id=' + id;
        }
    });
}

function reativarUsuario(id) {
    Swal.fire({
        title: "Tem certeza que deseja reativar este usuário?",
        padding: "25px",
        color: '#000',
        showCancelButton: true,
        confirmButtonColor: "#1bb155",
        cancelButtonColor: "#cc2828",
        confirmButtonText: "Sim, reativar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'reativar_usuario.php?id=' + id;
        }
    });
}