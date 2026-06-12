/**
 * payments.js
 *
 * Mengelola aksi CRUD pembayaran di panel admin.
 * Termasuk daftar pembayaran dan submit form pembayaran.
 */

const paymentsTable = document.getElementById("paymentsTable");
const paymentForm = document.getElementById("paymentForm");
let editingPayment = null;

/**
 * Ambil data pembayaran dan render di tabel.
 */
async function fetchPayments() {
  const data = await apiRequest(apiPath("payments"));
  paymentsTable.innerHTML = "";

  if (!data) return;
  data.forEach((payment) => {
    const row = document.createElement("tr");
    const editBtn = createButton("Edit", "button", "btn-primary");
    const deleteBtn = createButton("Hapus", "button", "btn-danger");

    editBtn.addEventListener("click", () => {
      editingPayment = payment.id_pembayaran;
      document.getElementById("paymentVisit").value = payment.id_kunjungan;
      document.getElementById("paymentMethod").value = payment.metode;
      paymentForm.querySelector('button[type="submit"]').textContent = "Update";
      paymentForm.scrollIntoView({ behavior: "smooth" });
    });

    deleteBtn.addEventListener("click", async () => {
      if (confirm("Yakin ingin menghapus?")) {
        await apiRequest(apiPath("payments", payment.id_pembayaran), { method: "DELETE" });
        fetchPayments();
      }
    });

    row.innerHTML = `
      <td>${payment.id_pembayaran}</td>
      <td>${payment.invoice}</td>
      <td>${payment.nama_pemilik || "-"}</td>
      <td>${payment.nama_hewan || "-"}</td>
      <td>${formatCurrency(payment.total)}</td>
      <td>${payment.metode || "-"}</td>
      <td></td>
    `;
    const actionCell = row.querySelector("td:last-child");
    actionCell.appendChild(editBtn);
    actionCell.appendChild(deleteBtn);
    paymentsTable.appendChild(row);
  });
}

/**
 * Tangani submit form pembayaran untuk tambah atau ubah.
 */
paymentForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const visitId = document.getElementById("paymentVisit").value;
  const metode = document.getElementById("paymentMethod").value;

  if (!visitId) {
    alert("Pilih kunjungan dulu!");
    return;
  }
  if (!metode) {
    alert("Metode pembayaran tidak boleh kosong!");
    return;
  }

  const payload = {
    id_kunjungan: visitId,
    metode: metode,
  };

  console.log("Submitting payment:", payload);

  const result = await apiRequest(apiPath("payments", editingPayment), {
    method: editingPayment ? "PUT" : "POST",
    body: JSON.stringify(payload),
  });

  if (result) {
    alert(editingPayment ? "Pembayaran berhasil diupdate!" : "Pembayaran berhasil ditambahkan!");
    cancelEditing("paymentForm");
    fetchPayments();
  } else {
    alert("Gagal menyimpan pembayaran. Cek console untuk error details.");
  }
});
