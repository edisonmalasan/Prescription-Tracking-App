const express = require("express");
const cors = require("cors");
const dotenv = require("dotenv");
const adminRoutes = require("./api/adminRoutes");

dotenv.config();

const app = express();

app.use(
  cors({
    origin: (process.env.ADMIN_APP_ORIGIN || "*")
      .split(",")
      .map((origin) => origin.trim()),
    methods: ["GET", "POST", "PUT", "PATCH", "DELETE"],
    credentials: true,
  })
);
app.use(express.json({ limit: "1mb" }));

app.get("/api/admin/health", (req, res) => {
  res.json({ status: "ok", uptime: process.uptime() });
});

app.use("/api/admin", adminRoutes);

const PORT = process.env.ADMIN_PORT || process.env.PORT || 4000;
app.listen(PORT, () => console.log(`Admin API running on port ${PORT}`));
