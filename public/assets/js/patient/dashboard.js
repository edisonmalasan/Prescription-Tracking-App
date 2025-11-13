console.log("Patient Dashboard JS loaded");

const API_BASE = "../../../src/api";

const apiCall = async (url, options = {}) => {
  const response = await fetch(url, {
    headers: { "Content-Type": "application/json", ...options.headers },
    ...options,
  });

  const text = await response.text();
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
};

//dom refs
const nameEl = document.getElementById("patient-name");
const idEl = document.getElementById("patient-id");
const totalEl = document.getElementById("stat-total-prescriptions");
const activeEl = document.getElementById("stat-active-prescriptions");
const tableBody = document.querySelector("#recent-prescriptions tbody");

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "PATIENT") {
    window.location.href = "../../../public/login.html";
    return;
  }

  try {
    await loadPatientProfile(user.user_id);
    await loadPrescriptions(user.user_id);
  } catch (err) {
    console.error("Error loading patient dashboard:", err);
    alert("Failed to load dashboard data.");
  }
});

async function loadPatientProfile(patientId) {
  const res = await api.get(`patientRoutes.php?action=profile&user_id=${patientId}`);
  if (!res.success || !res.patient) throw new Error(res.error || "Profile not found");

  const p = res.patient;

  nameEl.textContent = `Welcome, ${p.first_name ?? "—"} ${p.last_name ?? ""}`;
  idEl.textContent = `Patient ID: ${patientId}`;
}

async function loadPrescriptions(patientId) {
  const res = await api.get(`prescriptionRoutes.php?action=by-patient&patient_id=${patientId}`);
  if (!res.success || !Array.isArray(res.prescriptions)) {
    console.warn("No prescriptions found for patient", patientId);
    totalEl.textContent = "0";
    activeEl.textContent = "0";
    tableBody.innerHTML = `<tr><td colspan="4">No prescriptions found</td></tr>`;
    return;
  }

  const prescriptions = res.prescriptions;
  const totalCount = prescriptions.length;
  const activeCount = prescriptions.filter((p) => p.status === "active").length;

  totalEl.textContent = totalCount;
  activeEl.textContent = activeCount;

  // Display most recent prescriptions (limit 5)
  const rows = prescriptions
    .slice(0, 5)
    .map((p) => {
      const date = formatDate(p.prescription_date);
      const statusClass = p.status === "active" ? "status-active" : "status-inactive";
      return `
        <tr>
          <td>${p.prescription_id ?? "—"}</td>
          <td>${p.doctor_name ?? "—"}</td>
          <td>${date}</td>
          <td class="${statusClass}">${capitalize(p.status)}</td>
          <td><button class="btn small view-btn" data-id="${p.prescription_id}">View</button></td>
        </tr>
      `;
    })
    .join("");

  tableBody.innerHTML = rows || `<tr><td colspan="4">No recent prescriptions</td></tr>`;
  document.querySelectorAll(".view-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const prescId = e.target.dataset.id;
      if (!prescId) return alert("Missing prescription ID.");

      sessionStorage.setItem("selectedPrescriptionId", prescId);

      window.location.href = `./myprescription.php?prescription_id=${prescId}`;
    });
  });
}

function formatDate(dateStr) {
  if (!dateStr) return "—";
  const d = new Date(dateStr);
  return isNaN(d) ? "—" : d.toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
}

function capitalize(str) {
  return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
}
