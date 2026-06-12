/**
 * items.js
 *
 * Mengelola aksi CRUD obat/vaksin di panel admin.
 * Termasuk daftar, edit, hapus, dan submit form.
 */

const itemsTable = document.getElementById("itemsTable");
const itemForm = document.getElementById("itemForm");
let editingItem = null;

/**
 * Ambil data item dan render baris tabel.
 */
async function fetchItems() {
  const data = await apiRequest(apiPath("items"));
  itemsTable.innerHTML = "";

  if (!data) return;
  data.forEach((item) => {
    const row = document.createElement("tr");
    const editBtn = createButton("Edit", "button", "btn-primary");
    const deleteBtn = createButton("Hapus", "button", "btn-danger");

    editBtn.addEventListener("click", () => {
      editingItem = item.id_obat;
      document.getElementById("itemName").value = item.nama_item;
      document.getElementById("itemType").value = item.jenis;
      document.getElementById("itemStock").value = item.stok;
      document.getElementById("itemPrice").value = item.harga;
      itemForm.querySelector('button[type="submit"]').textContent = "Update";
      itemForm.scrollIntoView({ behavior: "smooth" });
    });

    deleteBtn.addEventListener("click", async () => {
      if (confirm("Yakin ingin menghapus?")) {
        await apiRequest(apiPath("items", item.id_obat), { method: "DELETE" });
        fetchItems();
      }
    });

    row.innerHTML = `
      <td>${item.id_obat}</td>
      <td>${item.nama_item}</td>
      <td>${item.jenis}</td>
      <td>${item.stok}</td>
      <td>${formatCurrency(item.harga)}</td>
      <td></td>
    `;
    const actionCell = row.querySelector("td:last-child");
    actionCell.appendChild(editBtn);
    actionCell.appendChild(deleteBtn);
    itemsTable.appendChild(row);
  });
}

/**
 * Tangani submit form item untuk tambah atau ubah.
 */
itemForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const nama = document.getElementById("itemName").value.trim();
  const jenis = document.getElementById("itemType").value;
  const stok = document.getElementById("itemStock").value;
  const harga = document.getElementById("itemPrice").value;

  if (!nama) {
    alert("Nama item tidak boleh kosong!");
    return;
  }
  if (!stok || stok < 0) {
    alert("Stok harus diisi dengan angka positif!");
    return;
  }
  if (!harga || harga < 0) {
    alert("Harga harus diisi dengan angka positif!");
    return;
  }

  const payload = {
    nama_item: nama,
    jenis: jenis,
    stok: Number(stok),
    harga: Number(harga),
  };

  console.log("Submitting item:", payload);

  const result = await apiRequest(apiPath("items", editingItem), {
    method: editingItem ? "PUT" : "POST",
    body: JSON.stringify(payload),
  });

  if (result) {
    alert(editingItem ? "Item berhasil diupdate!" : "Item berhasil ditambahkan!");
    cancelEditing("itemForm");
    fetchItems();
  } else {
    alert("Gagal menyimpan item. Cek console untuk error details.");
  }
});
