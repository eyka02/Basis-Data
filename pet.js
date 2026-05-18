// =========================
// ELEMENT
// =========================

const openModal =
    document.getElementById("openModal");

const modal =
    document.getElementById("petModal");

const closeModal =
    document.getElementById("closeModal");

const petForm =
    document.getElementById("petForm");

const petGrid =
    document.querySelector(".pet-grid");

// =========================
// OPEN MODAL
// =========================

openModal.addEventListener("click", () => {
    modal.style.display = "flex";
});

// =========================
// CLOSE MODAL
// =========================

closeModal.addEventListener("click", () => {
    modal.style.display = "none";
});

// klik luar modal
window.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});

// =========================
// SUBMIT FORM
// =========================

petForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const inputs = petForm.querySelectorAll("input, select, textarea");

    const name = inputs[1].value;
    const type = inputs[2].value;
    const breed = inputs[3].value;
    const weight = inputs[5].value;
    const gender = inputs[7].value;
    const allergy = inputs[8].value || "Tidak ada";

    const newCard = document.createElement("div");
    newCard.classList.add("pet-card");

    newCard.innerHTML = `
        <div class="pet-image-wrap">
            <img src="pet1.jpg">
            <span class="badge warning">Vaksin Pending</span>
        </div>

        <div class="pet-info">
            <h3>${name}</h3>
            <p>Jenis: ${type}</p>
            <p>Ras: ${breed}</p>
            <p>Berat: ${weight} kg</p>
            <p>Kelamin: ${gender}</p>
            <p>Alergi: ${allergy}</p>
        </div>
    `;

    petGrid.insertBefore(newCard, openModal);

    modal.style.display = "none";
    petForm.reset();
});