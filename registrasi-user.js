// =========================
// TOGGLE PASSWORD
// =========================

const toggleButtons = document.querySelectorAll(".toggle-password");

toggleButtons.forEach((button) => {

    button.addEventListener("click", () => {

        const targetId = button.dataset.target;

        const input =
            document.getElementById(targetId);

        const isPassword =
            input.getAttribute("type") === "password";

        input.setAttribute(
            "type",
            isPassword ? "text" : "password"
        );

        button.classList.toggle("fa-eye");
        button.classList.toggle("fa-eye-slash");

    });

});


// =========================
// FORM VALIDATION
// =========================

const registerForm =
    document.getElementById("registerForm");

registerForm.addEventListener("submit", (e) => {

    e.preventDefault();

    // INPUT VALUE
    const name =
        document.getElementById("name").value.trim();

    const email =
        document.getElementById("email").value.trim();

    const phone =
        document.getElementById("phone").value.trim();

    const password =
        document.getElementById("password").value;

    const confirmPassword =
        document.getElementById("confirmPassword").value;

    // =========================
    // VALIDASI NAMA
    // =========================

    if (name.length < 3) {

        alert("Nama minimal 3 karakter");
        return;

    }

    // =========================
    // VALIDASI EMAIL
    // =========================

    const emailPattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(email)) {

        alert("Format email tidak valid");
        return;

    }

    // =========================
    // VALIDASI PHONE
    // =========================

    const phonePattern =
        /^[0-9]{10,15}$/;

    if (!phonePattern.test(phone)) {

        alert(
            "Nomor telepon harus 10-15 digit angka"
        );

        return;

    }

    // =========================
    // VALIDASI PASSWORD
    // =========================

    if (password.length < 8) {

        alert(
            "Password minimal 8 karakter"
        );

        return;

    }

    // =========================
    // CONFIRM PASSWORD
    // =========================

    if (password !== confirmPassword) {

        alert(
            "Konfirmasi password tidak cocok"
        );

        return;

    }

    // =========================
    // SUCCESS
    // =========================

    alert("Registrasi berhasil!");
    window.location.href = "login-user.html";

    // Simulasi submit
    console.log({
        name,
        email,
        phone,
        password
    });

    // Reset form
    registerForm.reset();

});