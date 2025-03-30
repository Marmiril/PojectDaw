document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-login');

    if (form) {
        form.addEventListener('submit', function (event) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                event.preventDefault();
                alert("Por favor, completa todos los campos.");
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                event.preventDefault();
                alert("Por favor, indique un correo válido.");
            }
        });
    }
});
