document.addEventListener("DOMContentLoaded", function(){
    //Obtención de parámetros de la URL.
    const urlParams = new URLSearchParams(window.location.search);
    const mensaje = urlParams.get("mensaje");
    const tipo = urlParams.get("tipo");

    //Mostrar mensaje si existe.
    if(mensaje) {
        const mensajeDiv = document.getElementById("mensaje");

        //Establecer mensaje en el div.
        mensajeDiv.textContent = decodeURIComponent(mensaje);

        //Limpiar cualquier clase anterior.
        mensajeDiv.classList.remove('success', 'error', 'message');

        //Cambio de estilo según el mensaje (error o éxito).
        if (tipo === "error") {
            mensajeDiv.classList.add('error');
        } else if (tipo === "exito") {
            mensajeDiv.classList.add('success');
        }

        //Hacer visible el div.
        mensajeDiv.style.display = "block";
    }
});