const express = require("express");
const pool = require("../config/database");

const router = express.Router();

// Get all appointment
router.get("/", async (req, res) => {
  try {
    console.log("GET /appointment");
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT a.*, h.nama_hewan, p.nama AS nama_pemilik
      FROM appointment a
      LEFT JOIN hewan h ON a.id_hewan = h.id_hewan
      LEFT JOIN pemilik p ON h.id_pemilik = p.id_pemilik
      ORDER BY a.tanggal DESC, a.jam DESC
    `);
    connection.release();

    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get appointments by pemilik ID
router.get("/user/:id_pemilik", async (req, res) => {
  try {
    const { id_pemilik } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query(
      `
      SELECT a.*, h.nama_hewan, p.nama AS nama_pemilik
      FROM appointment a
      JOIN hewan h ON a.id_hewan = h.id_hewan
      JOIN pemilik p ON h.id_pemilik = p.id_pemilik
      WHERE p.id_pemilik = ?
      ORDER BY a.tanggal DESC, a.jam DESC
    `,
      [id_pemilik],
    );
    connection.release();

    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get appointment by ID
router.get("/:id", async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query(
      `
      SELECT a.*, h.nama_hewan, p.nama AS nama_pemilik
      FROM appointment a
      LEFT JOIN hewan h ON a.id_hewan = h.id_hewan
      LEFT JOIN pemilik p ON h.id_pemilik = p.id_pemilik
      WHERE a.id_appointment = ?
    `,
      [id],
    );
    connection.release();

    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: "Appointment tidak ditemukan" });
    }

    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Create appointment
router.post("/", async (req, res) => {
  try {
    console.log("POST /appointment body:", req.body);
    const { id_hewan, tanggal, jam, keluhan } = req.body;

    if (!id_hewan || !tanggal || !jam) {
      return res.status(400).json({ success: false, message: "id_hewan, tanggal, dan jam wajib diisi" });
    }

    const connection = await pool.getConnection();
    const [result] = await connection.query("INSERT INTO appointment (id_hewan, tanggal, jam, keluhan, status) VALUES (?, ?, ?, ?, ?)", [id_hewan, tanggal, jam, keluhan || null, "Pending"]);
    connection.release();

    res.status(201).json({ success: true, message: "Appointment berhasil dibuat", id: result.insertId });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Update appointment
router.put("/:id", async (req, res) => {
  try {
    const { id } = req.params;
    const { id_hewan, tanggal, jam, keluhan, status } = req.body;
    const fields = [];
    const params = [];

    if (id_hewan !== undefined) {
      fields.push("id_hewan = ?");
      params.push(id_hewan);
    }
    if (tanggal !== undefined) {
      fields.push("tanggal = ?");
      params.push(tanggal);
    }
    if (jam !== undefined) {
      fields.push("jam = ?");
      params.push(jam);
    }
    if (keluhan !== undefined) {
      fields.push("keluhan = ?");
      params.push(keluhan);
    }
    if (status !== undefined) {
      fields.push("status = ?");
      params.push(status);
    }

    if (fields.length === 0) {
      return res.status(400).json({ success: false, message: "Tidak ada data yang diupdate" });
    }

    params.push(id);
    const connection = await pool.getConnection();
    const [result] = await connection.query(`UPDATE appointment SET ${fields.join(", ")} WHERE id_appointment = ?`, params);
    connection.release();

    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: "Appointment tidak ditemukan" });
    }

    res.json({ success: true, message: "Appointment berhasil diperbarui" });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Delete appointment
router.delete("/:id", async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [result] = await connection.query("DELETE FROM appointment WHERE id_appointment = ?", [id]);
    connection.release();

    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: "Appointment tidak ditemukan" });
    }

    res.json({ success: true, message: "Appointment berhasil dihapus" });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
