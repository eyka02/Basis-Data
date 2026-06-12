const express = require("express");
const router = express.Router();
require("dotenv").config();

const ADMIN_USERNAME = process.env.ADMIN_USERNAME || "admin";
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || "admin123";

router.post("/login", (req, res) => {
  const { username, password } = req.body;

  if (!username || !password) {
    return res.status(400).json({ success: false, message: "Username dan password wajib diisi" });
  }

  if (username === ADMIN_USERNAME && password === ADMIN_PASSWORD) {
    return res.json({
      success: true,
      data: {
        username,
        token: "admin-token",
      },
    });
  }

  res.status(401).json({ success: false, message: "Kredensial tidak valid" });
});

router.post("/logout", (req, res) => {
  res.json({ success: true, message: "Logout berhasil" });
});

router.get("/me", (req, res) => {
  res.json({ success: true, data: { username: ADMIN_USERNAME } });
});

module.exports = router;
