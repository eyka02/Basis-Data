'use strict';

/* =========================
   TOGGLE PASSWORD
========================= */

const togglePassword =
document.getElementById(
    'togglePassword'
);

const password =
document.getElementById(
    'password'
);

if (
    togglePassword &&
    password
){

    togglePassword.addEventListener(
        'click',
        () => {

            const type =
            password.getAttribute(
                'type'
            ) === 'password'
                ? 'text'
                : 'password';

            password.setAttribute(
                'type',
                type
            );

            togglePassword.classList.toggle(
                'fa-eye'
            );

            togglePassword.classList.toggle(
                'fa-eye-slash'
            );
        }
    );
}

/* =========================
   LOGIN FORM
========================= */

const loginForm =
document.getElementById(
    'loginForm'
);

if (loginForm){

    loginForm.addEventListener(
        'submit',
        (e) => {

            e.preventDefault();

            const email =
            document.getElementById(
                'email'
            ).value;

            const passwordValue =
            password.value;

            /* VALIDASI SIMPLE */

            if (
                !email ||
                !passwordValue
            ){

                alert(
                    'Isi email dan password'
                );

                return;
            }

            /* SIMULASI LOGIN */

            console.log(
                'Login success'
            );

            /* REDIRECT */

            window.location.href =
            'dashboard-user.html';
        }
    );
}