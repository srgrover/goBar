file_input = document.getElementById('appbundle_usuario_imagenPerfil');

file_input.addEventListener('change', function() {
    getNameFile(this.value)
});

function getNameFile(fic) {
    fic = fic.split('\\');
    document.getElementById('name_file').innerHTML = fic[fic.length-1];
}
