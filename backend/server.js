const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
require('dotenv').config();

// Import routes
const pemilikRoutes = require('./routes/pemilik');
const hewanRoutes = require('./routes/hewan');
const kunjunganRoutes = require('./routes/kunjungan');
const obatVaksinRoutes = require('./routes/obatVaksin');
const detailObatRoutes = require('./routes/detailObat');
const laporanRoutes = require('./routes/laporan');

const app = express();

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Routes
app.use('/api/pemilik', pemilikRoutes);
app.use('/api/hewan', hewanRoutes);
app.use('/api/kunjungan', kunjunganRoutes);
app.use('/api/obat-vaksin', obatVaksinRoutes);
app.use('/api/detail-obat', detailObatRoutes);
app.use('/api/laporan', laporanRoutes);

// Health check
app.get('/api/health', (req, res) => {
  res.json({ status: 'Server is running' });
});

// Error handling middleware
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ 
    success: false, 
    message: 'Internal server error',
    error: process.env.NODE_ENV === 'development' ? err.message : undefined
  });
});

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
