const express = require('express');
const pool = require('../config/database');

const router = express.Router();

// Get all hewan
router.get('/', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT h.*, p.nama_pemilik, p.telepon 
      FROM hewan h
      LEFT JOIN pemilik p ON h.id_pemilik = p.id_pemilik
    `);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get hewan by ID
router.get('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT h.*, p.nama_pemilik, p.telepon 
      FROM hewan h
      LEFT JOIN pemilik p ON h.id_pemilik = p.id_pemilik
      WHERE h.id_hewan = ?
    `, [id]);
    connection.release();
    
    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Hewan not found' });
    }
    
    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get hewan by pemilik ID
router.get('/pemilik/:id_pemilik', async (req, res) => {
  try {
    const { id_pemilik } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query(
      'SELECT * FROM hewan WHERE id_pemilik = ?',
      [id_pemilik]
    );
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Create hewan
router.post('/', async (req, res) => {
  try {
    const { id_pemilik, nama_hewan, jenis_hewan, ras, umur_bulan } = req.body;
    
    // Validation
    if (!id_pemilik || !nama_hewan || !jenis_hewan) {
      return res.status(400).json({ 
        success: false, 
        message: 'id_pemilik, nama_hewan, and jenis_hewan are required' 
      });
    }
    
    const connection = await pool.getConnection();
    const [result] = await connection.query(
      'INSERT INTO hewan (id_pemilik, nama_hewan, jenis_hewan, ras, umur_bulan) VALUES (?, ?, ?, ?, ?)',
      [id_pemilik, nama_hewan, jenis_hewan, ras || null, umur_bulan || null]
    );
    connection.release();
    
    res.status(201).json({ 
      success: true, 
      message: 'Hewan created successfully',
      id: result.insertId 
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Update hewan
router.put('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const { nama_hewan, jenis_hewan, ras, umur_bulan } = req.body;
    
    const connection = await pool.getConnection();
    const [result] = await connection.query(
      'UPDATE hewan SET nama_hewan = ?, jenis_hewan = ?, ras = ?, umur_bulan = ? WHERE id_hewan = ?',
      [nama_hewan, jenis_hewan, ras || null, umur_bulan || null, id]
    );
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Hewan not found' });
    }
    
    res.json({ success: true, message: 'Hewan updated successfully' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Delete hewan
router.delete('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [result] = await connection.query('DELETE FROM hewan WHERE id_hewan = ?', [id]);
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Hewan not found' });
    }
    
    res.json({ success: true, message: 'Hewan deleted successfully' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
