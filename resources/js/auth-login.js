document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('[data-login-ui]');

    if (! loginForm) {
        return;
    }

    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const iconEye = document.getElementById('iconEye');
    const iconEyeOff = document.getElementById('iconEyeOff');
    const submitButton = document.getElementById('loginSubmitButton');
    const submitText = document.getElementById('loginSubmitText');
    const submitArrow = document.getElementById('loginArrow');
    const spinner = document.getElementById('loginSpinner');
    const capsLockHint = document.getElementById('capsLockHint');

    const updateCapsLockState = (event) => {
        if (! capsLockHint || typeof event.getModifierState !== 'function') {
            return;
        }

        capsLockHint.classList.toggle('hidden', ! event.getModifierState('CapsLock'));
    };

    if (togglePassword && passwordInput && iconEye && iconEyeOff) {
        togglePassword.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            iconEye.classList.toggle('hidden', isHidden);
            iconEyeOff.classList.toggle('hidden', ! isHidden);
            togglePassword.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('keydown', updateCapsLockState);
        passwordInput.addEventListener('keyup', updateCapsLockState);
        passwordInput.addEventListener('blur', () => {
            if (capsLockHint) {
                capsLockHint.classList.add('hidden');
            }
        });
    }

    loginForm.addEventListener('submit', () => {
        if (submitButton) {
            submitButton.disabled = true;
        }

        if (submitText) {
            submitText.textContent = 'Validando acceso...';
        }

        if (submitArrow) {
            submitArrow.classList.add('hidden');
        }

        if (spinner) {
            spinner.classList.remove('hidden');
        }
    });
});
