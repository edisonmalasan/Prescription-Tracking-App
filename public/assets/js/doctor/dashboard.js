console.log("Doctor Dashboard JS loaded");

const API_BASE = "../../../src/api";

const apiCall = async (url, options = {}) => {
  const response = await fetch(url, {
    headers: { "Content-Type": "application/json", ...options.headers },
    ...options,
  });
  const text = await response.text();

  //remove php debug texts
  const jsonStart = text.indexOf("{");
  const cleanText = jsonStart >= 0 ? text.slice(jsonStart) : text;

  try {
    return JSON.parse(cleanText);
  } catch (err) {
    console.error("Invalid JSON from server:", text);
    throw err;
  }
};

const api = {
  get: (endpoint) => apiCall(`${API_BASE}/${endpoint}`),
  post: (endpoint, data) =>
    apiCall(`${API_BASE}/${endpoint}`, {
      method: "POST",
      body: JSON.stringify(data),
    }),
  put: (endpoint, data) =>
    apiCall(`${API_BASE}/${endpoint}`, {
      method: "PUT",
      body: JSON.stringify(data),
    }),
};

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));

  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  console.log("Loading dashboard for doctor:", user.user_id);

  try {
    await loadDashboard(user);
  } catch (err) {
    console.error("Error loading dashboard:", err);
    alert("Failed to load dashboard data.");
  }

  const newBtn = document.getElementById("new-prescription-btn");
  if (newBtn) {
    newBtn.addEventListener("click", () => {
      location.href = "PrescriptionManagement.php";
    });
  }
});

async function loadDashboard(user) {
  const [doctorRes, patientRes, presRes] = await Promise.all([
    api.get(`doctorRoutes.php?action=profile&user_id=${user.user_id}`),
    api.get(`patientRoutes.php?action=all`),
    api.get(`prescriptionRoutes.php?action=all`),
  ]);

  console.log("[DEBUG] Doctor:", doctorRes);
  console.log("[DEBUG] Patients:", patientRes);
  console.log("[DEBUG] Prescriptions:", presRes);

  if (!doctorRes.success || !doctorRes.doctor) throw new Error("Failed to load doctor profile");

  const doctor = doctorRes.doctor;
  const doctorName = `${doctor.first_name} ${doctor.last_name}`.toLowerCase();

  const prescriptions = (presRes.prescriptions ?? []).filter(
    (p) => (p.doctor_name ?? "").toLowerCase() === doctorName
  );

  updateDoctorProfile(doctor);
  updateStats(prescriptions);
  renderRecentPrescriptions(prescriptions);
}

function updateDoctorProfile(doctor) {
  document.getElementById("doctor-name").textContent = `Welcome, Dr. ${doctor.first_name} ${doctor.last_name}`;
  document.getElementById("doctor-prc").textContent = `PRC License: ${doctor.prc_license ?? "—"}`;
}

function updateStats(prescriptions) {
  const setText = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val ?? "0";
  };

  if (!prescriptions || prescriptions.length === 0) {
    setText("stat-total-patients", 0);
    setText("stat-active-prescriptions", 0);
    setText("stat-this-week", 0);
    setText("stat-this-month", 0);
    return;
  }


  //filter by uniqque patient names
  const totalPatients = new Set(
    prescriptions.map((p) => (p.patient_name ?? "").trim().toLowerCase())
  ).size;

  //filter by active prescrips
  const activeCount = prescriptions.filter(
    (p) => (p.status ?? "").toLowerCase() === "active"
  ).length;

  const now = new Date();
  const startOfWeek = new Date(now);
  startOfWeek.setDate(now.getDate() - now.getDay());
  const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

  const weekCount = prescriptions.filter((p) => {
    const d = new Date(p.prescription_date);
    return !isNaN(d) && d >= startOfWeek;
  }).length;

  const monthCount = prescriptions.filter((p) => {
    const d = new Date(p.prescription_date);
    return !isNaN(d) && d >= startOfMonth;
  }).length;

  setText("stat-total-patients", totalPatients);
  setText("stat-active-prescriptions", activeCount);
  setText("stat-this-week", weekCount);
  setText("stat-this-month", monthCount);

  console.log("[STATS DEBUG] patients:", totalPatients, "active:", activeCount, "week:", weekCount, "month:", monthCount);
}

function renderRecentPrescriptions(prescriptions) {
  const tbody = document.querySelector("#recent-prescriptions tbody");
  if (!tbody) return;

  tbody.innerHTML = "";

  if (!prescriptions.length) {
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">No prescriptions found</td></tr>`;
    return;
  }

  //sort by recent --idk if ts works
  prescriptions.sort(
    (a, b) => new Date(b.prescription_date) - new Date(a.prescription_date)
  );

  prescriptions.slice(0, 10).forEach((rx) => {
    const tr = document.createElement("tr");
    const status = (rx.status ?? "—").toLowerCase();
    const statusLabel =
      status === "active"
        ? `<span class="status active">${status}</span>`
        : `<span class="status">${status}</span>`;

    tr.innerHTML = `
      <td>${rx.patient_name ?? "Unknown"}</td>
      <td>${rx.medication_name ?? "—"}</td>
      <td>${formatDate(rx.prescription_date)}</td>
      <td>${statusLabel}</td>
    `;
    tbody.appendChild(tr);
  });
}

function formatDate(dateStr) {
  if (!dateStr) return "—";
  const d = new Date(dateStr);
  return isNaN(d) ? "—" : d.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
}
