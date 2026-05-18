// =========================
// ELEMENT
// =========================

const appointmentForm =
    document.getElementById("appointmentForm");

const myAppointmentBtn =
    document.getElementById("myAppointmentBtn");

const createAppointmentBtn =
    document.getElementById("createAppointmentBtn");

const formCard =
    document.querySelector(".appointment-form-card");

const appointmentList =
    document.querySelector(".my-appointment-card");

// =========================
// NAVIGATION SWITCH
// =========================

myAppointmentBtn.addEventListener("click", () => {

    appointmentList.style.display = "flex";
    formCard.style.display = "none";

    myAppointmentBtn.classList.add("active");
    createAppointmentBtn.classList.remove("active");

});

createAppointmentBtn.addEventListener("click", () => {

    appointmentList.style.display = "flex";
    formCard.style.display = "block";

    createAppointmentBtn.classList.add("active");
    myAppointmentBtn.classList.remove("active");

});

// =========================
// CREATE APPOINTMENT
// =========================

appointmentForm.addEventListener("submit", (e) => {

    e.preventDefault();

    alert("Appointment berhasil dibuat!");

    appointmentForm.reset();

});

// =========================
// CANCEL APPOINTMENT
// =========================

const cancelButtons =
    document.querySelectorAll(".cancel-btn");

cancelButtons.forEach((button) => {

    button.addEventListener("click", () => {

        const confirmDelete =
            confirm(
                "Yakin ingin membatalkan appointment?"
            );

        if (confirmDelete) {

            const appointmentItem =
                button.closest(".appointment-item");

            appointmentItem.remove();

        }

    });

});

// =========================
// RESCHEDULE
// =========================

const rescheduleButtons =
    document.querySelectorAll(".reschedule-btn");

rescheduleButtons.forEach((button) => {

    button.addEventListener("click", () => {

        alert(
            "Fitur reschedule akan segera tersedia"
        );

    });

});