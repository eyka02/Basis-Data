const express = require('express');
const pool = require('../config/database');
const validator = require('validator');

const router = express.Router();

// Get all pemilik
router.get('/', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    const [rows] = await connection.query('SELECT * FROM pemilik');
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get pemilik by ID
router.get('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query('SELECT * FROM pemilik WHERE id_pemilik = ?', [id]);
    connection.release();
    
    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Pemilik not found' });
    }
    
    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Create pemilik
router.post('/', async (req, res) => {
  try {
    const { nama_pemilik, telepon, alamat } = req.body;
    
    // Validation
    if (!nama_pemilik) {
      return res.status(400).json({ success: false, message: 'Nama pemilik is required' });
    }
    
    if (telepon && !validator.isMobilePhone(telepon, 'id-ID')) {
      return res.status(400).json({ success: false, message: 'Invalid phone number' });
    }
    
    const connection = await pool.getConnection();
    const [result] = await connection.query(
      'INSERT INTO pemilik (nama_pemilik, telepon, alamat) VALUES (?, ?, ?)',
      [nama_pemilik, telepon || null, alamat || null]
    );
    connection.release();
    
    res.status(201).json({ 
      success: true, 
      message: 'Pemilik created successfully',
      id: result.insertId 
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Update pemilik
router.put('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const { nama_pemilik, telepon, alamat } = req.body;
    
    if (telepon && !validator.isMobilePhone(telepon, 'id-ID')) {
      return res.status(400).json({ success: false, message: 'Invalid phone number' });
    }
    
    const connection = await pool.getConnection();
    const [result] = await connection.query(
      'UPDATE pemilik SET nama_pemilik = ?, telepon = ?, alamat = ? WHERE id_pemilik = ?',
      [nama_pemilik, telepon || null, alamat || null, id]
    );
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Pemilik not found' });
    }
    
    res.json({ success: true, message: 'Pemilik updated successfully' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Delete pemilik
router.delete('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [result] = await connection.query('DELETE FROM pemilik WHERE id_pemilik = ?', [id]);
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Pemilik not found' });
    }
    
    res.json({ success: true, message: 'Pemilik deleted successfully' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
