/**
 * dashboard.js
 *
 * Bertanggung jawab untuk logika rendering dashboard dan laporan.
 * Memuat kartu ringkasan dan widget laporan di panel admin.
 */

const dashboardSummary = document.getElementById("dashboardSummary");
const reportSummary = document.getElementById("reportSummary");
const clinicSummary = document.getElementById("clinicSummary");
const mostUsedItems = document.getElementById("mostUsedItems");

/**
 * Ambil ringkasan dan laporan item paling sering digunakan untuk dashboard.
 * Render kartu ringkasan dan widget item terpopuler.
 */
async function fetchDashboard() {
  const summary = await apiRequest(apiPath("reportSummary"));
  const items = await apiRequest(apiPath("reportItems"));
  dashboardSummary.innerHTML = "";
  reportSummary.innerHTML = "";

  if (!summary) {
    dashboardSummary.innerHTML = "<p>Tidak ada data dashboard saat ini.</p>";
    reportSummary.innerHTML = "<p>Tidak ada data laporan saat ini.</p>";
    return;
  }

  const cards = [
    { title: "Pemilik", value: summary.total_owners },
    { title: "Hewan", value: summary.total_animals },
    { title: "Kunjungan", value: summary.total_visits },
    { title: "Item Stok Rendah", value: summary.low_stock_items },
  ];

  cards.forEach((card) => {
    const element = document.createElement("div");
    element.className = "card";
    element.innerHTML = `<h4>${card.title}</h4><h2>${card.value != null ? card.value : 0}</h2>`;
    dashboardSummary.appendChild(element);
  });

  if (items && Array.isArray(items) && items.length > 0) {
    const element = document.createElement("div");
    element.className = "card";
    element.innerHTML = `<h4>Obat/Vaksin Terpopuler</h4>${items
      .slice(0, 5)
      .map((item) => `<p>${item.nama_item} (${item.jenis}) - ${item.total_usage || item.total || 0}</p>`)
      .join("")}`;
    reportSummary.appendChild(element);
  } else {
    reportSummary.innerHTML = "<p>Tidak ada data item terpopuler saat ini.</p>";
  }
}

/**
 * Ambil detail laporan untuk section laporan.
 * Render ringkasan klinik dan daftar item paling sering digunakan.
 */
async function fetchReport() {
  const summary = await apiRequest(apiPath("reportSummary"));
  const items = await apiRequest(apiPath("reportItems"));

  clinicSummary.innerHTML = "";
  mostUsedItems.innerHTML = "";

  if (summary) {
    clinicSummary.innerHTML = `
      <p><strong>Total Pemilik:</strong> ${summary.total_owners}</p>
      <p><strong>Total Hewan:</strong> ${summary.total_animals}</p>
      <p><strong>Total Kunjungan:</strong> ${summary.total_visits}</p>
      <p><strong>Total Item:</strong> ${summary.total_items}</p>
      <p><strong>Stok Rendah:</strong> ${summary.low_stock_items}</p>
    `;
  }

  if (items && items.length > 0) {
    mostUsedItems.innerHTML = `<ul>${items
      .slice(0, 5)
      .map((item) => `<li>${item.nama_item} (${item.jenis}) - ${item.total_usage || item.total || 0}</li>`)
      .join("")}</ul>`;
  } else {
    mostUsedItems.textContent = "Tidak ada data item paling sering digunakan";
  }
}
