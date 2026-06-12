const express = require("express");
const crypto = require("crypto");
const pool = require("../config/database");

const router = express.Router();

function hashPassword(password) {
  return crypto.createHash("sha256").update(password).digest("hex");
}

async function ensureUsersTable() {
  const connection = await pool.getConnection();
  await connection.query(`
    CREATE TABLE IF NOT EXISTS users (
      id_user int(11) NOT NULL AUTO_INCREMENT,
      id_pemilik int(11) NOT NULL,
      email varchar(100) NOT NULL,
      password varchar(255) NOT NULL,
      created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id_user),
      UNIQUE KEY unique_email (email),
      KEY fk_pemilik (id_pemilik),
      CONSTRAINT fk_users_pemilik FOREIGN KEY (id_pemilik) REFERENCES pemilik(id_pemilik) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  `);
  connection.release();
}

router.post("/register", async (req, res) => {
  try {
    const { name, email, phone, password } = req.body;

    if (!name || !email || !phone || !password) {
      return res.status(400).json({ success: false, message: "Semua field wajib diisi" });
    }

    await ensureUsersTable();
    const connection = await pool.getConnection();

    const [existingUser] = await connection.query("SELECT id_user FROM users WHERE email = ?", [email]);
    if (existingUser.length > 0) {
      connection.release();
      return res.status(409).json({ success: false, message: "Email sudah terdaftar" });
    }

    const [existingOwner] = await connection.query("SELECT id_pemilik FROM pemilik WHERE email = ? LIMIT 1", [email]);
    let id_pemilik;

    if (existingOwner.length > 0) {
      id_pemilik = existingOwner[0].id_pemilik;
      await connection.query("UPDATE pemilik SET nama = ?, no_hp = ? WHERE id_pemilik = ?", [name, phone, id_pemilik]);
    } else {
      const [result] = await connection.query("INSERT INTO pemilik (nama, no_hp, email, tanggal_registrasi, created_at) VALUES (?, ?, ?, CURDATE(), NOW())", [name, phone, email]);
      id_pemilik = result.insertId;
    }

    await connection.query("INSERT INTO users (id_pemilik, email, password) VALUES (?, ?, ?)", [id_pemilik, email, hashPassword(password)]);
    connection.release();

    res.status(201).json({ success: true, data: { id_pemilik, name, email } });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

router.post("/login", async (req, res) => {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ success: false, message: "Email dan password wajib diisi" });
    }

    await ensureUsersTable();
    const hashedPassword = hashPassword(password);
    const connection = await pool.getConnection();
    const [rows] = await connection.query(
      `SELECT u.id_user, u.id_pemilik, u.email, p.nama AS nama_pemilik, p.no_hp, p.alamat, p.tanggal_registrasi
       FROM users u
       JOIN pemilik p ON u.id_pemilik = p.id_pemilik
       WHERE u.email = ? AND u.password = ? LIMIT 1`,
      [email, hashedPassword],
    );
    connection.release();

    if (rows.length === 0) {
      return res.status(401).json({ success: false, message: "Email atau password tidak sesuai" });
    }

    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

router.get("/me/:id_pemilik", async (req, res) => {
  try {
    const { id_pemilik } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query("SELECT id_pemilik, nama AS nama_pemilik, no_hp, email, alamat, tanggal_registrasi FROM pemilik WHERE id_pemilik = ? LIMIT 1", [id_pemilik]);
    connection.release();

    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: "User tidak ditemukan" });
    }

    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
