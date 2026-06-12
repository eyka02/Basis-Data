const express = require("express");
const pool = require("../config/database");

const router = express.Router();

// Get all pembayaran
router.get("/", async (req, res) => {
  try {
    console.log("GET /pembayaran");
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT pb.*, k.id_hewan, k.total_biaya, h.nama_hewan, p.nama AS nama_pemilik
      FROM pembayaran pb
      JOIN kunjungan k ON pb.id_kunjungan = k.id_kunjungan
      JOIN hewan h ON k.id_hewan = h.id_hewan
      JOIN pemilik p ON h.id_pemilik = p.id_pemilik
      ORDER BY pb.id_pembayaran DESC
    `);
    connection.release();

    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get pembayaran by ID
router.get("/:id", async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query(
      `
      SELECT pb.*, k.id_hewan, k.total_biaya, h.nama_hewan, p.nama AS nama_pemilik
      FROM pembayaran pb
      JOIN kunjungan k ON pb.id_kunjungan = k.id_kunjungan
      JOIN hewan h ON k.id_hewan = h.id_hewan
      JOIN pemilik p ON h.id_pemilik = p.id_pemilik
      WHERE pb.id_pembayaran = ?
    `,
      [id],
    );
    connection.release();

    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: "Pembayaran tidak ditemukan" });
    }

    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Create pembayaran
router.post("/", async (req, res) => {
  try {
    console.log("POST /pembayaran body:", req.body);
    const { id_kunjungan, metode } = req.body;

    if (!id_kunjungan || !metode) {
      return res.status(400).json({ success: false, message: "id_kunjungan dan metode wajib diisi" });
    }

    const connection = await pool.getConnection();
    const [visitRows] = await connection.query("SELECT total_biaya FROM kunjungan WHERE id_kunjungan = ?", [id_kunjungan]);

    if (visitRows.length === 0) {
      connection.release();
      return res.status(404).json({ success: false, message: "Kunjungan tidak ditemukan" });
    }

    const total = visitRows[0].total_biaya || 0;
    const invoice = `INV-${Date.now()}`;

    const [result] = await connection.query("INSERT INTO pembayaran (invoice, id_kunjungan, total, metode, created_at) VALUES (?, ?, ?, ?, NOW())", [invoice, id_kunjungan, total, metode]);
    connection.release();

    res.status(201).json({ success: true, message: "Pembayaran berhasil dibuat", id: result.insertId });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Update pembayaran
router.put("/:id", async (req, res) => {
  try {
    const { id } = req.params;
    const { metode } = req.body;

    if (!metode) {
      return res.status(400).json({ success: false, message: "Metode pembayaran wajib diisi" });
    }

    const connection = await pool.getConnection();
    const [result] = await connection.query("UPDATE pembayaran SET metode = ? WHERE id_pembayaran = ?", [metode, id]);
    connection.release();

    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: "Pembayaran tidak ditemukan" });
    }

    res.json({ success: true, message: "Pembayaran berhasil diperbarui" });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Delete pembayaran
router.delete("/:id", async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [result] = await connection.query("DELETE FROM pembayaran WHERE id_pembayaran = ?", [id]);
    connection.release();

    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: "Pembayaran tidak ditemukan" });
    }

    res.json({ success: true, message: "Pembayaran berhasil dihapus" });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
