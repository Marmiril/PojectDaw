document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('form-preferencias');
    const mensajeDiv = document.getElementById('mensaje');

    //Array con un listado de colores.
    const coloresPermitidos = [
        "rojo", "azul", "verde", "amarillo", "naranja", "morado", "rosa", "blanco", 
        "negro", "gris", "turquesa", "café", "beige", "violeta", "celeste", "dorado", 
        "plateado", "marrón", "lila", "fucsia", "aguamarina", "aqua", "salmón", "lavanda", "ocre", 
        "cobalto", "indigo", "esmeralda", "caqui", "ámbar", "topacio", "perla", "marfil", "champán", 
        "rojo carmesí", "azul marino", "oliva", "pistacho", "menta", "caramelo"
    ];

    //Validar el formulario al enviarlo.
    form.addEventListener("submit", function(event) {
        let isValid = true;
        let mensaje = "" ;
        let tipo = "";

        //Obtener los valores de los campos.
        const colorFavorito = document.getElementById('color_favorito').value.trim().toLowerCase();
        const edad = document.getElementById('edad').value.trim();
        const altura = document.getElementById('altura').value.trim();
        const peso = document.getElementById('peso').value.trim();
        const genero = document.getElementById('genero').value;

        //Validar el campo "Color favorito" (debe ser uno de los colores del array).
        if(!coloresPermitidos.includes(colorFavorito)) {
            isValid = false;
            mensaje += "Color no permitido, prueba otro a ver.<br>";
            tipo = "error";
        }

        //Validar el campo edad.
        if(isNaN(edad) || edad <= 0) {
            isValid = false;
            mensaje += "Por favor, ingresa una edad válida.<br>";
            tipo = "error";
        }

        //Validar campo altura.
        if(isNaN(altura) || altura <= 0) {
            isValid = false;
            mensaje = "Por favor, indica una altura correcta.<br>";
            tipo = "error";
        }

        //Validar campo peso.
        if(isNaN(peso) || peso <= 0) {
            isValid = false;
            mensaje += "Por favor, indica un peso válido.<br>";
            tipo = "error";
        }

        //Validar el campo género.
        if(!genero) {
            isValid = false;
            mensaje += "Por favor, selecciona tu género.<br>";
            tipo = "error";
        }

        //Si alguno de los campos es inválido se previene del envío del formulario.
        if(!isValid) {
            event.preventDefault(); // Evita que el formulario se envíe.

            //Muestra de mensaje de error.
            mensajeDiv.innerHTML = mensaje;
            mensajeDiv.classList.remove('success', 'message');
            mensajeDiv.classList.add(tipo);
            mensajeDiv.style.display = "block";
        }
    });
});