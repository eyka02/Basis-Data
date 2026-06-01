const express = require('express');
const pool = require('../config/database');

const router = express.Router();

// Get all obat/vaksin
router.get('/', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    const [rows] = await connection.query('SELECT * FROM obat_vaksin');
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get obat/vaksin by ID
router.get('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query('SELECT * FROM obat_vaksin WHERE id_obat = ?', [id]);
    connection.release();
    
    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Obat/Vaksin not found' });
    }
    
    res.json({ success: true, data: rows[0] });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get obat/vaksin by type
router.get('/type/:jenis', async (req, res) => {
  try {
    const { jenis } = req.params;
    
    if (!['Obat', 'Vaksin'].includes(jenis)) {
      return res.status(400).json({ success: false, message: 'Invalid type. Must be Obat or Vaksin' });
    }
    
    const connection = await pool.getConnection();
    const [rows] = await connection.query('SELECT * FROM obat_vaksin WHERE jenis = ?', [jenis]);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Create obat/vaksin
router.post('/', async (req, res) => {
  try {
    const { nama_item, jenis, stok, harga } = req.body;
    
    // Validation
    if (!nama_item || !jenis || harga === undefined) {
      return res.status(400).json({ 
        success: false, 
        message: 'nama_item, jenis, and harga are required' 
      });
    }
    
    if (!['Obat', 'Vaksin'].includes(jenis)) {
      return res.status(400).json({ 
        success: false, 
        message: 'Invalid jenis. Must be Obat or Vaksin' 
      });
    }
    
    const connection = await pool.getConnection();
    const [result] = await connection.query(
      'INSERT INTO obat_vaksin (nama_item, jenis, stok, harga) VALUES (?, ?, ?, ?)',
      [nama_item, jenis, stok || 0, harga]
    );
    connection.release();
    
    res.status(201).json({ 
      success: true, 
      message: 'Obat/Vaksin created successfully',
      id: result.insertId 
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Update obat/vaksin
router.put('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const { nama_item, jenis, stok, harga } = req.body;
    
    if (jenis && !['Obat', 'Vaksin'].includes(jenis)) {
      return res.status(400).json({ 
        success: false, 
        message: 'Invalid jenis. Must be Obat or Vaksin' 
      });
    }
    
    const connection = await pool.getConnection();
    const [result] = await connection.query(
      'UPDATE obat_vaksin SET nama_item = ?, jenis = ?, stok = ?, harga = ? WHERE id_obat = ?',
      [nama_item, jenis, stok, harga, id]
    );
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Obat/Vaksin not found' });
    }
    
    res.json({ success: true, message: 'Obat/Vaksin updated successfully' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Delete obat/vaksin
router.delete('/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const connection = await pool.getConnection();
    const [result] = await connection.query('DELETE FROM obat_vaksin WHERE id_obat = ?', [id]);
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Obat/Vaksin not found' });
    }
    
    res.json({ success: true, message: 'Obat/Vaksin deleted successfully' });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
