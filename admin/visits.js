/**
 * visits.js
 *
 * Mengelola aksi CRUD kunjungan di panel admin.
 * Mengambil data kunjungan dan mendukung tambah, ubah, serta hapus.
 */

const visitsTable = document.getElementById("visitsTable");
const paymentVisitSelect = document.getElementById("paymentVisit");
const visitForm = document.getElementById("visitForm");
let editingVisit = null;

/**
 * Ambil semua kunjungan dan render di tabel.
 * Juga mengisi dropdown pembayaran dengan data kunjungan.
 */
async function fetchVisits() {
  const data = await apiRequest(apiPath("visits"));
  visitsTable.innerHTML = "";
  paymentVisitSelect.innerHTML = '<option value="">Pilih kunjungan</option>';

  if (!data) return;
  data.forEach((visit) => {
    const row = document.createElement("tr");
    const editBtn = createButton("Edit", "button", "btn-primary");
    const deleteBtn = createButton("Hapus", "button", "btn-danger");

    editBtn.addEventListener("click", () => {
      editingVisit = visit.id_kunjungan;
      document.getElementById("visitAnimal").value = visit.id_hewan;
      document.getElementById("visitDate").value = visit.tanggal_kunjungan;
      document.getElementById("visitComplaint").value = visit.keluhan || "";
      document.getElementById("visitDiagnosis").value = visit.diagnosa || "";
      document.getElementById("visitTreatment").value = visit.tindakan || visit.tindakan_medis || "";
      visitForm.querySelector('button[type="submit"]').textContent = "Update";
      visitForm.scrollIntoView({ behavior: "smooth" });
    });

    deleteBtn.addEventListener("click", async () => {
      if (confirm("Yakin ingin menghapus?")) {
        await apiRequest(apiPath("visits", visit.id_kunjungan), { method: "DELETE" });
        fetchVisits();
        fetchPayments();
      }
    });

    row.innerHTML = `
      <td>${visit.id_kunjungan}</td>
      <td>${visit.tanggal_kunjungan}</td>
      <td>${visit.nama_hewan}</td>
      <td>${visit.nama_pemilik || "-"}</td>
      <td>${visit.diagnosa || "-"}</td>
      <td>${visit.tindakan || visit.tindakan_medis || "-"}</td>
      <td></td>
    `;
    const actionCell = row.querySelector("td:last-child");
    actionCell.appendChild(editBtn);
    actionCell.appendChild(deleteBtn);
    visitsTable.appendChild(row);

    const option = document.createElement("option");
    option.value = visit.id_kunjungan;
    option.textContent = `${visit.nama_hewan || "Hewan"} - ${visit.nama_pemilik || "Pemilik"}`;
    paymentVisitSelect.appendChild(option);
  });
}

/**
 * Tangani submit form kunjungan untuk tambah atau ubah.
 * Menggunakan status editingVisit untuk menentukan POST atau PUT.
 */
visitForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const animalId = document.getElementById("visitAnimal").value;
  const tanggal = document.getElementById("visitDate").value;

  if (!animalId) {
    alert("Pilih hewan dulu!");
    return;
  }
  if (!tanggal) {
    alert("Tanggal kunjungan tidak boleh kosong!");
    return;
  }

  const payload = {
    id_hewan: animalId,
    tanggal_kunjungan: tanggal,
    keluhan: document.getElementById("visitComplaint").value.trim(),
    diagnosa: document.getElementById("visitDiagnosis").value.trim(),
    tindakan: document.getElementById("visitTreatment").value.trim(),
  };

  console.log("Submitting visit:", payload);

  const result = await apiRequest(apiPath("visits", editingVisit), {
    method: editingVisit ? "PUT" : "POST",
    body: JSON.stringify(payload),
  });

  if (result) {
    alert(editingVisit ? "Kunjungan berhasil diupdate!" : "Kunjungan berhasil ditambahkan!");
    cancelEditing("visitForm");
    fetchVisits();
  } else {
    alert("Gagal menyimpan kunjungan. Cek console untuk error details.");
  }
});
