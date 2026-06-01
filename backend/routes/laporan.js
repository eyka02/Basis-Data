const express = require('express');
const pool = require('../config/database');

const router = express.Router();

// Get most used medicines/vaccines
router.get('/most-used-items', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT 
        ov.id_obat,
        ov.nama_item,
        ov.jenis,
        SUM(do.jumlah) as total_usage,
        COUNT(DISTINCT do.id_kunjungan) as visit_count
      FROM detail_obat do
      LEFT JOIN obat_vaksin ov ON do.id_obat = ov.id_obat
      GROUP BY ov.id_obat, ov.nama_item, ov.jenis
      ORDER BY total_usage DESC
    `);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get medicines/vaccines usage statistics
router.get('/item-statistics/:id_obat', async (req, res) => {
  try {
    const { id_obat } = req.params;
    const connection = await pool.getConnection();
    
    const [itemData] = await connection.query(
      'SELECT * FROM obat_vaksin WHERE id_obat = ?',
      [id_obat]
    );
    
    if (itemData.length === 0) {
      connection.release();
      return res.status(404).json({ success: false, message: 'Item not found' });
    }
    
    const [usageData] = await connection.query(`
      SELECT 
        DATE(k.tanggal_kunjungan) as tanggal,
        COUNT(*) as usage_count,
        SUM(do.jumlah) as total_jumlah
      FROM detail_obat do
      LEFT JOIN kunjungan k ON do.id_kunjungan = k.id_kunjungan
      WHERE do.id_obat = ?
      GROUP BY DATE(k.tanggal_kunjungan)
      ORDER BY tanggal DESC
    `, [id_obat]);
    
    connection.release();
    
    res.json({ 
      success: true, 
      data: {
        item: itemData[0],
        usage_history: usageData
      }
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get medical services usage (medical treatments)
router.get('/medical-services', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT 
        tindakan_medis,
        COUNT(*) as frequency
      FROM kunjungan
      WHERE tindakan_medis IS NOT NULL AND tindakan_medis != ''
      GROUP BY tindakan_medis
      ORDER BY frequency DESC
    `);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get visit statistics by date range
router.get('/visit-statistics', async (req, res) => {
  try {
    const { start_date, end_date } = req.query;
    const connection = await pool.getConnection();
    
    let query = `
      SELECT 
        DATE(k.tanggal_kunjungan) as tanggal,
        COUNT(*) as total_visits,
        COUNT(DISTINCT k.id_hewan) as unique_animals,
        COUNT(DISTINCT h.id_pemilik) as unique_owners
      FROM kunjungan k
      LEFT JOIN hewan h ON k.id_hewan = h.id_hewan
    `;
    
    const params = [];
    
    if (start_date || end_date) {
      query += ' WHERE ';
      if (start_date && end_date) {
        query += 'DATE(k.tanggal_kunjungan) BETWEEN ? AND ?';
        params.push(start_date, end_date);
      } else if (start_date) {
        query += 'DATE(k.tanggal_kunjungan) >= ?';
        params.push(start_date);
      } else {
        query += 'DATE(k.tanggal_kunjungan) <= ?';
        params.push(end_date);
      }
    }
    
    query += ' GROUP BY DATE(k.tanggal_kunjungan) ORDER BY tanggal DESC';
    
    const [rows] = await connection.query(query, params);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get animal type statistics
router.get('/animal-statistics', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    const [rows] = await connection.query(`
      SELECT 
        h.jenis_hewan,
        COUNT(*) as total_animals,
        COUNT(DISTINCT k.id_kunjungan) as total_visits
      FROM hewan h
      LEFT JOIN kunjungan k ON h.id_hewan = k.id_hewan
      GROUP BY h.jenis_hewan
      ORDER BY total_animals DESC
    `);
    connection.release();
    
    res.json({ success: true, data: rows });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

// Get comprehensive clinic report
router.get('/clinic-summary', async (req, res) => {
  try {
    const connection = await pool.getConnection();
    
    const [totalOwners] = await connection.query('SELECT COUNT(*) as count FROM pemilik');
    const [totalAnimals] = await connection.query('SELECT COUNT(*) as count FROM hewan');
    const [totalVisits] = await connection.query('SELECT COUNT(*) as count FROM kunjungan');
    const [totalItems] = await connection.query('SELECT COUNT(*) as count FROM obat_vaksin');
    const [lowStockItems] = await connection.query('SELECT COUNT(*) as count FROM obat_vaksin WHERE stok < 5');
    
    const [mostUsedMedicine] = await connection.query(`
      SELECT ov.nama_item, SUM(do.jumlah) as total
      FROM detail_obat do
      LEFT JOIN obat_vaksin ov ON do.id_obat = ov.id_obat
      WHERE ov.jenis = 'Obat'
      GROUP BY ov.id_obat
      ORDER BY total DESC
      LIMIT 1
    `);
    
    const [mostUsedVaccine] = await connection.query(`
      SELECT ov.nama_item, SUM(do.jumlah) as total
      FROM detail_obat do
      LEFT JOIN obat_vaksin ov ON do.id_obat = ov.id_obat
      WHERE ov.jenis = 'Vaksin'
      GROUP BY ov.id_obat
      ORDER BY total DESC
      LIMIT 1
    `);
    
    connection.release();
    
    res.json({ 
      success: true, 
      data: {
        total_owners: totalOwners[0].count,
        total_animals: totalAnimals[0].count,
        total_visits: totalVisits[0].count,
        total_items: totalItems[0].count,
        low_stock_items: lowStockItems[0].count,
        most_used_medicine: mostUsedMedicine[0] || null,
        most_used_vaccine: mostUsedVaccine[0] || null
      }
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

module.exports = router;
