// Existing JavaScript content

// Login Form Handling
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const loader = document.getElementById('form-loader');
            if (loader) {
                loader.classList.remove('hidden');
            }
            document.getElementById('global-loader').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
    }
});
