const botaoAddMaterial = document.getElementById("botao-add-material")
const listaMateriais = document.getElementById("lista-materiais")
const nomeMaterial = document.getElementById("input-material").value

function addMaterial(nomeMaterial) {
    let material = "<div class='material'><p>" + nomeMaterial + "</p><button onclick='removerMaterial'><i class='fa-solid fa-trash-can'></button></div>"
    console.log(material)
}

function removerMaterial() {

}