document.addEventListener('DOMContentLoaded', function () {
    console.log('Script cargado y DOM listo');

    const form = document.getElementById('form-cuento');
    const textarea = document.getElementById('texto_cuento');
    const contador = document.getElementById('contador-palabras');
    const botonGuardar = document.querySelector('button[type="submit"]');
    const errorDiv = document.createElement('div');
    errorDiv.id = 'error-message';
    form.parentNode.insertBefore(errorDiv, form);

    // Deshabilitar el botón de guardar por defecto
    botonGuardar.disabled = true;

    // Función para validar el rango de palabras y actualizar el contador
    function validarTexto() {
        const palabras = textarea.value.trim().split(/\s+/).filter(word => word.length > 0);

        // Actualizar el contador de palabras
        contador.textContent = `${palabras.length} palabras`;

        // Cambiar el color del contador y habilitar/deshabilitar el botón
        if (palabras.length >= 200 && palabras.length <= 600) {
            contador.style.color = 'green';
            botonGuardar.disabled = false; // Habilitar el botón
        } else {
            contador.style.color = 'red';
            botonGuardar.disabled = true; // Deshabilitar el botón
        }

        return palabras.length;
    }

    // Validar el formulario al enviarlo
    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Evitar el envío estándar del formulario

            const titulo = document.getElementById('titulo').value.trim();
            const tema = document.getElementById('tema').value.trim();
            const palabra_guia = document.getElementById('palabra_guia').value.trim();
            const pasos = parseInt(document.getElementById('pasos').value.trim());
            const texto_cuento = textarea.value.trim();

            // Validar que los campos no estén vacíos
            if (!titulo || !tema || !palabra_guia || !pasos || !texto_cuento) {
                errorDiv.textContent = 'Todos los campos son obligatorios.';
                errorDiv.style.color = 'red';
                return;
            }

            // Validar el rango de palabras
            const palabras = validarTexto();
            if (palabras < 200 || palabras > 600) {
                errorDiv.textContent = 'El texto debe tener entre 200 y 600 palabras.';
                errorDiv.style.color = 'red';
                return;
            }

            // Validar el rango de pasos
            if (isNaN(pasos) || pasos < 10 || pasos > 15) {
                errorDiv.textContent = 'El número de aportaciones debe estar entre 10 y 15.';
                errorDiv.style.color = 'red';
                return;
            }

            // Si todo es correcto, enviar el formulario
            errorDiv.textContent = ''; // Limpiar mensajes de error
            form.submit();
        });
    }

    // Actualizar el contador de palabras en tiempo real
    if (textarea) {
        textarea.addEventListener('input', validarTexto);
    } else {
        console.log('Textarea no encontrado');
    }
});

// Función para mostrar/ocultar el formulario de empezar cuento
function toggleCuentoForm() {
    const form = document.getElementById('empezar-cuento');
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
}

