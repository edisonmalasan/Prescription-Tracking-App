console.log("My Prescription JS loaded");

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
const tableBody = document.querySelector("#itemsTable tbody");

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "PATIENT") {
    window.location.href = "../../../public/login.html";
    return;
  }

  if (!PRESCRIPTION_ID) {
    alert("No prescription selected.");
    window.location.href = "./patientDashboard.php"; // fallback
    return;
  }

  try {
    await loadPatientProfile(user.user_id);
    await loadPrescriptionDetails(PRESCRIPTION_ID);
  } catch (err) {
    console.error("Error loading prescription details:", err);
    alert("Failed to load prescription data.");
  }
});

async function loadPatientProfile(patientId) {
  const res = await api.get(`patientRoutes.php?action=profile&user_id=${patientId}`);
  if (!res.success || !res.patient) throw new Error(res.error || "Profile not found");

  const p = res.patient;
  nameEl.textContent = `Welcome, ${p.first_name ?? "—"} ${p.last_name ?? ""}`;
  idEl.textContent = `Patient ID: ${patientId}`;
}

async function loadPrescriptionDetails(prescriptionId) {
  const res = await api.get(`prescriptionRoutes.php?action=details&prescription_id=${prescriptionId}`);
  
  if (!res.success || !Array.isArray(res.details)) {
    console.warn("No details found for prescription", prescriptionId);
    tableBody.innerHTML = `<tr><td colspan="6">No prescription details found.</td></tr>`;
    return;
  }

  const detailedItems = await Promise.all(
    res.details.map(async (d) => {
      if (d.drug_id) {
        try {
          const drugRes = await api.get(`drugRoutes.php?action=get&drug_id=${d.drug_id}`);
          d.generic_name = drugRes.drug?.generic_name ?? "Unknown";
        } catch (err) {
          console.error(`Error fetching drug ${d.drug_id}:`, err);
          d.generic_name = "Unknown";
        }
      } else {
        d.generic_name = "—";
      }
      return d;
    })
  );

  const rows = detailedItems
    .map((d) => {
      return `
        <tr>
          <td>${d.generic_name ?? d.drug_name ?? "—"}</td>
          <td>${d.dosage ?? "—"}</td>
          <td>${d.frequency ?? "—"}</td>
          <td>${d.duration ?? "—"}</td>
          <td>${d.refills ?? 0}</td>
          <td>${d.special_instructions ?? "None"}</td>
        </tr>
      `;
    })
    .join("");

  tableBody.innerHTML = rows || `<tr><td colspan="6">No prescription items available.</td></tr>`;
}
