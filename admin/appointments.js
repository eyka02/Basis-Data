/**
 * appointments.js
 *
 * Mengelola aksi CRUD appointment di panel admin.
 * Mendukung edit dan hapus appointment dari tabel.
 */

const appointmentsTable = document.getElementById("appointmentsTable");
const appointmentForm = document.getElementById("appointmentForm");
let editingAppointment = null;

/**
 * Ambil data appointment dan tampilkan di tabel.
 */
async function fetchAppointments() {
  const data = await apiRequest(apiPath("appointments"));
  appointmentsTable.innerHTML = "";

  if (!data) return;
  data.forEach((item) => {
    const row = document.createElement("tr");
    const editBtn = createButton("Edit", "button", "btn-primary");
    const deleteBtn = createButton("Hapus", "button", "btn-danger");

    editBtn.addEventListener("click", () => {
      editingAppointment = item.id_appointment;
      document.getElementById("appointmentAnimal").value = item.id_hewan;
      document.getElementById("appointmentDate").value = item.tanggal;
      document.getElementById("appointmentTime").value = item.jam;
      document.getElementById("appointmentComplaint").value = item.keluhan || "";
      appointmentForm.querySelector('button[type="submit"]').textContent = "Update";
      appointmentForm.scrollIntoView({ behavior: "smooth" });
    });

    deleteBtn.addEventListener("click", async () => {
      if (confirm("Yakin ingin menghapus?")) {
        await apiRequest(apiPath("appointments", item.id_appointment), { method: "DELETE" });
        fetchAppointments();
      }
    });

    row.innerHTML = `
      <td>${item.id_appointment}</td>
      <td>${item.tanggal}</td>
      <td>${item.jam}</td>
      <td>${item.nama_hewan || "-"}</td>
      <td>${item.nama_pemilik || "-"}</td>
      <td>${item.status || "-"}</td>
      <td></td>
    `;
    const actionCell = row.querySelector("td:last-child");
    actionCell.appendChild(editBtn);
    actionCell.appendChild(deleteBtn);
    appointmentsTable.appendChild(row);
  });
}

/**
 * Tangani submit form appointment untuk tambah atau ubah.
 */
appointmentForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const animalId = document.getElementById("appointmentAnimal").value;
  const tanggal = document.getElementById("appointmentDate").value;
  const jam = document.getElementById("appointmentTime").value;

  if (!animalId) {
    alert("Pilih hewan dulu!");
    return;
  }
  if (!tanggal) {
    alert("Tanggal appointment tidak boleh kosong!");
    return;
  }
  if (!jam) {
    alert("Jam appointment tidak boleh kosong!");
    return;
  }

  const payload = {
    id_hewan: animalId,
    tanggal: tanggal,
    jam: jam,
    keluhan: document.getElementById("appointmentComplaint").value.trim(),
  };

  console.log("Submitting appointment:", payload);

  const result = await apiRequest(apiPath("appointments", editingAppointment), {
    method: editingAppointment ? "PUT" : "POST",
    body: JSON.stringify(payload),
  });

  if (result) {
    alert(editingAppointment ? "Appointment berhasil diupdate!" : "Appointment berhasil ditambahkan!");
    cancelEditing("appointmentForm");
    fetchAppointments();
  } else {
    alert("Gagal menyimpan appointment. Cek console untuk error details.");
  }
});
