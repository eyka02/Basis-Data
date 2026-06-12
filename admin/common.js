/**
 * common.js
 *
 * File helper bersama untuk admin yang berisi autentikasi,
 * pemetaan route, navigasi section, dan utilitas request API.
 */

const API_BASE = "http://localhost:5000/api";
const token = localStorage.getItem("adminToken");
const username = localStorage.getItem("adminUsername");

// Redirect to login if admin token is missing.
if (!token) {
  window.location.href = "login.html";
}

// Standard headers for authenticated API requests.
const authHeaders = {
  "Content-Type": "application/json",
  Authorization: `Bearer ${token}`,
};

// Named backend routes for each admin feature.
const API_ROUTES = {
  owners: "/pemilik",
  animals: "/hewan",
  visits: "/kunjungan",
  items: "/obat-vaksin",
  appointments: "/appointment",
  payments: "/pembayaran",
  reportSummary: "/laporan/clinic-summary",
  reportItems: "/laporan/most-used-items",
};

/**
 * Build API path from route key and optional resource ID.
 * @param {string} routeKey
 * @param {string|number|null} id
 * @returns {string}
 */
function apiPath(routeKey, id = null) {
  const route = API_ROUTES[routeKey];
  if (!route) {
    console.warn(`Unknown API route key: ${routeKey}`);
    return routeKey;
  }
  return id ? `${route}/${id}` : route;
}

const sections = document.querySelectorAll(".page-section");
const navButtons = document.querySelectorAll(".nav-btn");
const welcomeText = document.getElementById("welcomeText");
const logoutBtn = document.getElementById("logoutBtn");

welcomeText.textContent = username ? `Selamat datang, ${username}` : "Selamat datang di panel admin";

/**
 * Tambahkan listener klik pada tombol navigasi sidebar.
 * Saat ditekan, tampilkan section admin yang dipilih.
 */
navButtons.forEach((btn) => {
  btn.addEventListener("click", () => {
    navButtons.forEach((nav) => nav.classList.remove("active"));
    btn.classList.add("active");
    const sectionId = btn.dataset.section;
    showSection(sectionId);
  });
});

/**
 * Aksi logout membersihkan kredensial lalu kembali ke halaman login.
 */
logoutBtn.addEventListener("click", () => {
  localStorage.removeItem("adminToken");
  localStorage.removeItem("adminUsername");
  window.location.href = "login.html";
});

/**
 * Tampilkan hanya section admin terpilih dan sembunyikan yang lain.
 * @param {string} id
 */
function showSection(id) {
  sections.forEach((section) => {
    section.classList.toggle("visible", section.id === id);
  });
}

/**
 * Wrapper request API generik untuk script admin.
 * Otomatis menambahkan header auth dan menangani logout 401.
 * @param {string} path
 * @param {object} options
 * @returns {Promise<any|null>}
 */
async function apiRequest(path, options = {}) {
  try {
    options.headers = {
      ...authHeaders,
      ...(options.headers || {}),
    };

    console.log(`[ADMIN API] ${options.method || "GET"} ${API_BASE}${path}`);
    const response = await fetch(`${API_BASE}${path}`, options);

    if (response.status === 401) {
      logoutBtn.click();
      return null;
    }

    const data = await response.json();
    console.log(`API Response for ${path}:`, data);

    if (!response.ok) {
      console.error(`API Error ${response.status}:`, data.message || "Unknown error");
      return null;
    }

    if (data && typeof data === "object" && data.success === false) {
      console.warn(`API returned success:false for ${path}:`, data.message);
      return null;
    }

    return data.data || data;
  } catch (error) {
    console.error(`API Request Error for ${path}:`, error.message);
    return null;
  }
}

/**
 * Format angka menjadi Rupiah Indonesia.
 * @param {number|string} value
 * @returns {string}
 */
function formatCurrency(value) {
  return `Rp ${Number(value).toLocaleString("id-ID")}`;
}

/**
 * Buat elemen tombol yang dapat digunakan ulang untuk aksi tabel.
 * @param {string} label
 * @param {string} type
 * @param {string} className
 * @returns {HTMLButtonElement}
 */
function createButton(label, type = "button", className = "btn-danger") {
  const btn = document.createElement("button");
  btn.type = type;
  btn.textContent = label;
  btn.className = `btn ${className}`;
  return btn;
}

/**
 * Reset status edit dan bersihkan form setelah simpan/batal.
 * @param {string} formId
 */
function cancelEditing(formId) {
  if (formId === "ownerForm") {
    editingOwner = null;
  } else if (formId === "animalForm") {
    editingAnimal = null;
  } else if (formId === "visitForm") {
    editingVisit = null;
  } else if (formId === "itemForm") {
    editingItem = null;
  } else if (formId === "appointmentForm") {
    editingAppointment = null;
  } else if (formId === "paymentForm") {
    editingPayment = null;
  }
  const form = document.getElementById(formId);
  if (form) {
    form.reset();
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.textContent = "Simpan";
  }
}
