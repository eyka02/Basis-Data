/**
 * owners.js
 *
 * Mengelola aksi CRUD pemilik di panel admin.
 * Termasuk logika ambil, sunting, hapus, dan submit form pemilik.
 */

const ownersTable = document.getElementById("ownersTable");
const animalOwnerSelect = document.getElementById("animalOwner");
const ownerForm = document.getElementById("ownerForm");
let editingOwner = null;

/**
 * Ambil daftar pemilik dari backend dan render baris tabel.
 * Juga mengisi dropdown pemilik untuk form hewan.
 */
async function fetchOwners() {
  const data = await apiRequest(apiPath("owners"));
  ownersTable.innerHTML = "";
  animalOwnerSelect.innerHTML = '<option value="">Pilih pemilik</option>';

  if (!data) return;
  data.forEach((owner) => {
    const row = document.createElement("tr");
    const editBtn = createButton("Edit", "button", "btn-primary");
    const deleteBtn = createButton("Hapus", "button", "btn-danger");

    editBtn.addEventListener("click", () => {
      editingOwner = owner.id_pemilik;
      document.getElementById("ownerName").value = owner.nama_pemilik;
      document.getElementById("ownerPhone").value = owner.no_hp || "";
      document.getElementById("ownerEmail").value = owner.email || "";
      document.getElementById("ownerAddress").value = owner.alamat || "";
      ownerForm.querySelector('button[type="submit"]').textContent = "Update";
      ownerForm.scrollIntoView({ behavior: "smooth" });
    });

    deleteBtn.addEventListener("click", async () => {
      if (confirm("Yakin ingin menghapus?")) {
        await apiRequest(apiPath("owners", owner.id_pemilik), { method: "DELETE" });
        fetchOwners();
        fetchAnimals();
      }
    });

    row.innerHTML = `
      <td>${owner.id_pemilik}</td>
      <td>${owner.nama_pemilik}</td>
      <td>${owner.no_hp || "-"}</td>
      <td>${owner.email || "-"}</td>
      <td>${owner.alamat || "-"}</td>
      <td></td>
    `;
    const actionCell = row.querySelector("td:last-child");
    actionCell.appendChild(editBtn);
    actionCell.appendChild(deleteBtn);
    ownersTable.appendChild(row);

    const option = document.createElement("option");
    option.value = owner.id_pemilik;
    option.textContent = owner.nama_pemilik;
    animalOwnerSelect.appendChild(option);
  });
}

/**
 * Tangani submit form pemilik untuk tambah atau ubah.
 * Menggunakan status editingOwner untuk menentukan POST atau PUT.
 */
ownerForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const nama = document.getElementById("ownerName").value.trim();
  if (!nama) {
    alert("Nama pemilik tidak boleh kosong!");
    return;
  }

  const payload = {
    nama_pemilik: nama,
    no_hp: document.getElementById("ownerPhone").value.trim(),
    email: document.getElementById("ownerEmail").value.trim(),
    alamat: document.getElementById("ownerAddress").value.trim(),
  };

  console.log("Submitting owner:", payload);

  const result = await apiRequest(apiPath("owners", editingOwner), {
    method: editingOwner ? "PUT" : "POST",
    body: JSON.stringify(payload),
  });

  if (result) {
    alert(editingOwner ? "Pemilik berhasil diupdate!" : "Pemilik berhasil ditambahkan!");
    cancelEditing("ownerForm");
    fetchOwners();
  } else {
    alert("Gagal menyimpan pemilik. Cek console untuk error details.");
  }
});
