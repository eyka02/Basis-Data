/**
 * animals.js
 *
 * Mengelola aksi CRUD hewan di panel admin.
 * Mengambil data hewan dan mengisi dropdown terkait kunjungan dan appointment.
 */

const animalsTable = document.getElementById("animalsTable");
const visitAnimalSelect = document.getElementById("visitAnimal");
const appointmentAnimalSelect = document.getElementById("appointmentAnimal");
const animalForm = document.getElementById("animalForm");
let editingAnimal = null;

/**
 * Ambil daftar hewan dan render baris tabel.
 * Juga mengisi dropdown hewan untuk form kunjungan dan appointment.
 */
async function fetchAnimals() {
  const data = await apiRequest(apiPath("animals"));
  animalsTable.innerHTML = "";
  visitAnimalSelect.innerHTML = '<option value="">Pilih hewan</option>';
  appointmentAnimalSelect.innerHTML = '<option value="">Pilih hewan</option>';

  if (!data) return;
  data.forEach((animal) => {
    const row = document.createElement("tr");
    const editBtn = createButton("Edit", "button", "btn-primary");
    const deleteBtn = createButton("Hapus", "button", "btn-danger");

    editBtn.addEventListener("click", () => {
      editingAnimal = animal.id_hewan;
      document.getElementById("animalOwner").value = animal.id_pemilik;
      document.getElementById("animalName").value = animal.nama_hewan;
      document.getElementById("animalType").value = animal.jenis || animal.jenis_hewan || "";
      document.getElementById("animalBreed").value = animal.ras || "";
      document.getElementById("animalDob").value = animal.tanggal_lahir ? animal.tanggal_lahir.split("T")[0] : "";
      document.getElementById("animalWeight").value = animal.berat || "";
      document.getElementById("animalColor").value = animal.warna || "";
      document.getElementById("animalGender").value = animal.jenis_kelamin || "";
      document.getElementById("animalAllergy").value = animal.alergi || "";
      document.getElementById("animalVaccineStatus").value = animal.status_vaksin || "";
      document.getElementById("animalPhoto").value = animal.foto || "";
      animalForm.querySelector('button[type="submit"]').textContent = "Update";
      animalForm.scrollIntoView({ behavior: "smooth" });
    });

    deleteBtn.addEventListener("click", async () => {
      if (confirm("Yakin ingin menghapus?")) {
        await apiRequest(apiPath("animals", animal.id_hewan), { method: "DELETE" });
        fetchAnimals();
      }
    });

    row.innerHTML = `
      <td>${animal.id_hewan}</td>
      <td>${animal.nama_hewan}</td>
      <td>${animal.jenis || animal.jenis_hewan || "-"}</td>
      <td>${animal.ras || "-"}</td>
      <td>${animal.tanggal_lahir ? animal.tanggal_lahir.split("T")[0] : "-"}</td>
      <td>${animal.nama_pemilik || "-"}</td>
      <td></td>
    `;
    const actionCell = row.querySelector("td:last-child");
    actionCell.appendChild(editBtn);
    actionCell.appendChild(deleteBtn);
    animalsTable.appendChild(row);

    const option1 = document.createElement("option");
    option1.value = animal.id_hewan;
    option1.textContent = animal.nama_hewan;
    visitAnimalSelect.appendChild(option1);

    const option2 = document.createElement("option");
    option2.value = animal.id_hewan;
    option2.textContent = animal.nama_hewan;
    appointmentAnimalSelect.appendChild(option2);
  });
}

/**
 * Tangani submit form hewan untuk tambah atau ubah.
 * Menggunakan status editingAnimal untuk menentukan POST atau PUT.
 */
animalForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const ownerId = document.getElementById("animalOwner").value;
  const nama = document.getElementById("animalName").value.trim();
  const jenis = document.getElementById("animalType").value.trim();

  if (!ownerId) {
    alert("Pilih pemilik dulu!");
    return;
  }
  if (!nama) {
    alert("Nama hewan tidak boleh kosong!");
    return;
  }
  if (!jenis) {
    alert("Jenis hewan tidak boleh kosong!");
    return;
  }

  const payload = {
    id_pemilik: ownerId,
    nama_hewan: nama,
    jenis: jenis,
    ras: document.getElementById("animalBreed").value.trim(),
    tanggal_lahir: document.getElementById("animalDob").value || null,
    berat: document.getElementById("animalWeight").value || null,
    warna: document.getElementById("animalColor").value.trim() || null,
    jenis_kelamin: document.getElementById("animalGender").value || null,
    alergi: document.getElementById("animalAllergy").value.trim() || null,
    status_vaksin: document.getElementById("animalVaccineStatus").value || null,
    foto: document.getElementById("animalPhoto").value.trim() || null,
  };

  console.log("Submitting animal:", payload);

  const result = await apiRequest(apiPath("animals", editingAnimal), {
    method: editingAnimal ? "PUT" : "POST",
    body: JSON.stringify(payload),
  });

  if (result) {
    alert(editingAnimal ? "Hewan berhasil diupdate!" : "Hewan berhasil ditambahkan!");
    cancelEditing("animalForm");
    fetchAnimals();
  } else {
    alert("Gagal menyimpan hewan. Cek console untuk error details.");
  }
});
