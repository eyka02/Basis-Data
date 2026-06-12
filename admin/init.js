/**
 * init.js
 *
 * Titik masuk untuk panel admin.
 * Memuat semua data fitur saat halaman dibuka.
 */

async function loadAllData() {
  await Promise.all([fetchOwners(), fetchAnimals(), fetchVisits(), fetchItems(), fetchAppointments(), fetchPayments(), fetchDashboard(), fetchReport()]);
}

// Inisialisasi UI admin dengan memuat semua data fitur.
loadAllData();
