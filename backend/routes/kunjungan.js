const express = require('express');
const pool = require('../config/database');

const router = express.Router();

// Get all kunjungan
router.get('/', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT k.*, h.nama_hewan, h.jenis_hewan, p.nama_pemilik
      FROM kunjungan k
      LEFT JOIN hewan h ON k.id_hewan = h.id_hewan
      LEFT JOIN pemilik p ON h.id_pemilik = p.id_pemilik
      ORDER BY k.tanggal_kunjungan DESC
    `);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get kunjungan by ID
router.get('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT k.*, h.nama_hewan, h.jenis_hewan, p.nama_pemilik
      FROM kunjungan k
      LEFT JOIN hewan h ON k.id_hewan = h.id_hewan
      LEFT JOIN pemilik p ON h.id_pemilik = p.id_pemilik
      WHERE k.id_kunjungan = ?
    `, [id]);
    connection.release();
    
    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Kunjungan not found' });
    }
    
    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get kunjungan by hewan ID
router.get('/hewan/:id_hewan', async (req, res) => {
  try {
    const { id_hewan } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT k.*, h.nama_hewan
      FROM kunjungan k
      LEFT JOIN hewan h ON k.id_hewan = h.id_hewan
      WHERE k.id_hewan = ?
      ORDER BY k.tanggal_kunjungan DESC
    `, [id_hewan]);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Create kunjungan
router.post('/', async (req, res) => {
  try {
    const { id_hewan, tanggal_kunjungan, keluhan, diagnosa, tindakan_medis } = req.body;
    
    // Validation
    if (!id_hewan || !tanggal_kunjungan) {
      return res.status(400).json({ 
        success: false, 
        message: 'id_hewan and tanggal_kunjungan are required' 
      });
    }
    
    const connection = await pool.getConnection();
    const [result] = await connection.query(
      'INSERT INTO kunjungan (id_hewan, tanggal_kunjungan, keluhan, diagnosa, tindakan_medis) VALUES (?, ?, ?, ?, ?)',
      [id_hewan, tanggal_kunjungan, keluhan || null, diagnosa || null, tindakan_medis || null]
    );
    connection.release();
    
    res.status(201).json({ 
      success: true, 
      message: 'Kunjungan created successfully',
      id: result.insertId 
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Update kunjungan
router.put('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const { keluhan, diagnosa, tindakan_medis } = req.body;
    
    const connection = await pool.getConnection();
    const [result] = await connection.query(
      'UPDATE kunjungan SET keluhan = ?, diagnosa = ?, tindakan_medis = ? WHERE id_kunjungan = ?',
      [keluhan || null, diagnosa || null, tindakan_medis || null, id]
    );
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Kunjungan not found' });
    }
    
    res.json({ success: true, message: 'Kunjungan updated successfully' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Delete kunjungan
router.delete('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [result] = await connection.query('DELETE FROM kunjungan WHERE id_kunjungan = ?', [id]);
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Kunjungan not found' });
    }
    
    res.json({ success: true, message: 'Kunjungan deleted successfully' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
