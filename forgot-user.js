// =========================
// FORM
// =========================

const form =
    document.getElementById("resetPasswordForm");

form.addEventListener("submit", (e) => {

    e.preventDefault();

    const email =
        document.getElementById("email")
        .value
        .trim();

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
    // SUCCESS
    // =========================

    alert(
        "Link reset password berhasil dikirim ke email Anda"
    );

    console.log({
        email
    });

    form.reset();

});