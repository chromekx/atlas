function confirmarExclusao(id) {
    Swal.fire({
        title: "Tem certeza que deseja excluir este usuário?",
        padding: "25px",
        showCancelButton: true,
        confirmButtonColor: "#1bb155",
        cancelButtonColor: "#dc3545",
        confirmButtonText: "Sim, excluir",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'excluir_usuario.php?id=' + id;
        }
    });
}

async function editarUsuario(id) {
    const {
        value: formValues
    } = await Swal.fire({
        title: "Atualizar Usuário",
        html: `
                        <input type='text' id="swal-input1" placeholder="Nome" class="swal2-input" name="nome" value="">
                        <input type='email' id="swal-input2" placeholder="Email" class="swal2-input" name="email" value="">
                        <input type='password' id="swal-input3" placeholder="Senha" class="swal2-input" name="senha" value="">
                        <input type='password' id="swal-input4" placeholder="Confirmar Senha" class="swal2-input" name="confirmarSenha" value="">
                        <select id="swal-input5" placeholder="Preferência" class="swal2-select" name="preferencias" value="">
                            <option value="esportes">Esportes</option>
                            <option value="música">Música</option>
                            <option value="cinema">Cinema</option>
                            <option value="livros">Livros</option>
                        </select>
                        <input type= 'number' id="swal-input6" placeholder="Nível" class="swal2-input" name="nivel" value="">
                    `,
        focusConfirm: false,
        preConfirm: () => {
        }
    })
}