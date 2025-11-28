const express = require("express");
const cors = require("cors");
const dotenv = require("dotenv");
const adminRoutes = require("./api/adminRoutes");

dotenv.config();

const app = express();

app.use(
  cors({
    origin: function (origin, callback) {
      console.log("Incoming Origin:", origin);

      if (!origin) return callback(null, true);

      // allow any port on localhost
      const localhostRegex = /^http:\/\/localhost(:\d+)?$/;
      const loopbackRegex = /^http:\/\/127\.0\.0\.1(:\d+)?$/;

      if (localhostRegex.test(origin) || loopbackRegex.test(origin)) {
        return callback(null, true);
      }

      const allowedEnv = (process.env.ADMIN_APP_ORIGIN || "")
        .split(",")
        .map((o) => o.trim())
        .filter(Boolean);

      if (allowedEnv.includes(origin)) {
        return callback(null, true);
      }

      return callback(new Error(`CORS blocked: ${origin} is not allowed.`));
    },
    credentials: true,
  })
);

app.use(express.json({ limit: "1mb" }));

const rootResponse = {
  message: "Prescription Tracking Admin API",
  docs: "/api/admin/health",
};

app.get("/", (req, res) => {
  res.json(rootResponse);
});

app.get("/api/admin", (req, res) => {
  res.json({ status: "ok", uptime: process.uptime() });
});

app.use("/api/admin", adminRoutes);
app.use((err, req, res, next) => {
  console.error("Error:", err);
  const status = err.status || err.statusCode || 500;
  const message = err.message || "Internal server error";
  const response = {
    success: false,
    message,
  };

  if (err.details && Array.isArray(err.details)) {
    response.errors = err.details;
  }

  if (process.env.NODE_ENV === "development") {
    response.stack = err.stack;
  }

  res.status(status).json(response);
});

app.use((req, res) => {
  res.status(404).json({
    success: false,
    message: "Route not found",
  });
});

const PORT = process.env.ADMIN_PORT || process.env.PORT || 4000;
app.listen(PORT, () => console.log(`Admin API running on port ${PORT}`));
