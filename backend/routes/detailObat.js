const express = require('express');
const pool = require('../config/database');

const router = express.Router();

// Get all detail obat
router.get('/', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT do.*, ov.nama_item, ov.jenis, k.tanggal_kunjungan
      FROM detail_obat do
      LEFT JOIN obat_vaksin ov ON do.id_obat = ov.id_obat
      LEFT JOIN kunjungan k ON do.id_kunjungan = k.id_kunjungan
    `);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get detail obat by kunjungan ID
router.get('/kunjungan/:id_kunjungan', async (req, res) => {
  try {
    const { id_kunjungan } = req.params;
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT do.*, ov.nama_item, ov.jenis, ov.harga
      FROM detail_obat do
      LEFT JOIN obat_vaksin ov ON do.id_obat = ov.id_obat
      WHERE do.id_kunjungan = ?
    `, [id_kunjungan]);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Create detail obat with auto stock decrement
router.post('/', async (req, res) => {
  const connection = await pool.getConnection();
  
  try {
    const { id_kunjungan, id_obat, jumlah } = req.body;
    
    // Validation
    if (!id_kunjungan || !id_obat || !jumlah) {
      return res.status(400).json({ 
        success: false, 
        message: 'id_kunjungan, id_obat, and jumlah are required' 
      });
    }
    
    if (jumlah <= 0) {
      return res.status(400).json({ 
        success: false, 
        message: 'jumlah must be greater than 0' 
      });
    }
    
    // Start transaction
    await connection.beginTransaction();
    
    // Check current stock
    const [obatRows] = await connection.query(
      'SELECT stok FROM obat_vaksin WHERE id_obat = ? FOR UPDATE',
      [id_obat]
    );
    
    if (obatRows.length === 0) {
      await connection.rollback();
      connection.release();
      return res.status(404).json({ success: false, message: 'Obat/Vaksin not found' });
    }
    
    const currentStok = obatRows[0].stok;
    
    if (currentStok < jumlah) {
      await connection.rollback();
      connection.release();
      return res.status(400).json({ 
        success: false, 
        message: `Insufficient stock. Available: ${currentStok}, Requested: ${jumlah}` 
      });
    }
    
    // Insert detail obat
    const [result] = await connection.query(
      'INSERT INTO detail_obat (id_kunjungan, id_obat, jumlah) VALUES (?, ?, ?)',
      [id_kunjungan, id_obat, jumlah]
    );
    
    // Decrease stock
    await connection.query(
      'UPDATE obat_vaksin SET stok = stok - ? WHERE id_obat = ?',
      [jumlah, id_obat]
    );
    
    // Commit transaction
    await connection.commit();
    connection.release();
    
    res.status(201).json({ 
      success: true, 
      message: 'Detail obat created and stock decreased successfully',
      id: result.insertId 
    });
  } catch (error) {
    await connection.rollback();
    connection.release();
    res.status(500).json({ success: false, message: error.message });
  }
});

// Update detail obat (with stock adjustment)
router.put('/:id', async (req, res) => {
  const connection = await pool.getConnection();
  
  try {
    const { id } = req.params;
    const { jumlah } = req.body;
    
    if (!jumlah || jumlah <= 0) {
      return res.status(400).json({ 
        success: false, 
        message: 'jumlah must be greater than 0' 
      });
    }
    
    // Start transaction
    await connection.beginTransaction();
    
    // Get current detail obat
    const [currentRows] = await connection.query(
      'SELECT id_obat, jumlah FROM detail_obat WHERE id_detail = ? FOR UPDATE',
      [id]
    );
    
    if (currentRows.length === 0) {
      await connection.rollback();
      connection.release();
      return res.status(404).json({ success: false, message: 'Detail obat not found' });
    }
    
    const { id_obat, jumlah: currentJumlah } = currentRows[0];
    const selisih = jumlah - currentJumlah;
    
    // Check stock if increasing
    if (selisih > 0) {
      const [obatRows] = await connection.query(
        'SELECT stok FROM obat_vaksin WHERE id_obat = ? FOR UPDATE',
        [id_obat]
      );
      
      if (obatRows[0].stok < selisih) {
        await connection.rollback();
        connection.release();
        return res.status(400).json({ 
          success: false, 
          message: `Insufficient stock to increase. Available: ${obatRows[0].stok}` 
        });
      }
    }
    
    // Update detail obat
    const [result] = await connection.query(
      'UPDATE detail_obat SET jumlah = ? WHERE id_detail = ?',
      [jumlah, id]
    );
    
    // Adjust stock
    await connection.query(
      'UPDATE obat_vaksin SET stok = stok - ? WHERE id_obat = ?',
      [selisih, id_obat]
    );
    
    await connection.commit();
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Detail obat not found' });
    }
    
    res.json({ success: true, message: 'Detail obat updated and stock adjusted successfully' });
  } catch (error) {
    await connection.rollback();
    connection.release();
    res.status(500).json({ success: false, message: error.message });
  }
});

// Delete detail obat (with stock restoration)
router.delete('/:id', async (req, res) => {
  const connection = await pool.getConnection();
  
  try {
    // Start transaction
    await connection.beginTransaction();
    
    // Get detail obat
    const [rows] = await connection.query(
      'SELECT id_obat, jumlah FROM detail_obat WHERE id_detail = ? FOR UPDATE',
      [req.params.id]
    );
    
    if (rows.length === 0) {
      await connection.rollback();
      connection.release();
      return res.status(404).json({ success: false, message: 'Detail obat not found' });
    }
    
    const { id_obat, jumlah } = rows[0];
    
    // Delete detail obat
    const [result] = await connection.query(
      'DELETE FROM detail_obat WHERE id_detail = ?',
      [req.params.id]
    );
    
    // Restore stock
    await connection.query(
      'UPDATE obat_vaksin SET stok = stok + ? WHERE id_obat = ?',
      [jumlah, id_obat]
    );
    
    await connection.commit();
    connection.release();
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Detail obat not found' });
    }
    
    res.json({ success: true, message: 'Detail obat deleted and stock restored successfully' });
  } catch (error) {
    await connection.rollback();
    connection.release();
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
