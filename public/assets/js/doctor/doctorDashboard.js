console.log("Dashboard JS loaded");

const API_BASE = "../../../src/api";

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
}

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  
  await loadDashboard(user.user_id);

  const newBtn = document.getElementById("new-prescription-btn");
  if (newBtn) {
    newBtn.addEventListener("click", () => {
      location.href = "PrescriptionManagement.php";
    });
  }
});


async function loadDashboard(doctorId) {
  try {
    // const profileURL = `${API_BASE}/doctorRoutes.php?action=profile&user_id=${doctorId}`;
    // const patientsURL = `${API_BASE}/patientRoutes.php?action=all`;
    // const prescriptionsURL = `${API_BASE}/prescriptionRoutes.php?action=all`;

    // console.log("PROFILE URL:", profileURL);
    // console.log("PATIENTS URL:", patientsURL);
    // console.log("PRESCRIPTIONS URL:", prescriptionsURL);

    // // Test each endpoint individually
    // const profileTxt = await (await fetch(profileURL)).text();
    // console.log("PROFILE RAW:", profileTxt);

    // const patientsTxt = await (await fetch(patientsURL)).text();
    // console.log("PATIENTS RAW:", patientsTxt);

    // const prescriptionsTxt = await (await fetch(prescriptionsURL)).text();
    // console.log("PRESCRIPTIONS RAW:", prescriptionsTxt);

    // return;
    
    const [
      doctorRes,
      patientsRes,
      prescriptionsRes
    ] = await Promise.all([
      fetchJSON(`${API_BASE}/doctorRoutes.php?action=profile&user_id=${doctorId}`),
      fetchJSON(`${API_BASE}/patientRoutes.php?action=all`),
      fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=all`)
    ]);

    //prescrip filter for doctor
    const prescriptions = (prescriptionsRes.prescriptions || []).filter(
      p => p.doctor_id == doctorId
    );
    
    const patients = patientsRes.patients || [];
    const patientMap = {};
    patients.forEach(p => {
      if (p.record_id) patientMap[p.record_id] = p;
    });

    // Update UI blocks
    updateDoctorProfile(doctorRes);
    updateStatsFromMergedLogic(patients, prescriptions, patientMap);
    renderRecentPrescriptions(prescriptions, patientMap);
    renderFullTable(prescriptions, patientMap);

  } catch (err) {
    console.error("Error loading dashboard:", err);
  }
}

function updateDoctorProfile(docRes) {
  if (!(docRes?.success && docRes.doctor)) return;

  const p = docRes.doctor;

  const nameEl = document.getElementById("doctor-name");
  const prcEl = document.getElementById("doctor-prc");

  if (nameEl) nameEl.textContent = `Welcome, Dr. ${p.first_name} ${p.last_name}`;
  if (prcEl) prcEl.textContent = `PRC License: ${p.prc_license ?? "—"}`;
}

function updateStatsFromMergedLogic(patients, prescriptions, patientMap) {
  const setText = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val ?? "—";
  };

  //dooctor -> patients
  const docPatients = prescriptions
    .map(pres => patientMap[pres.record_id])
    .filter(Boolean);

  setText("total-patients", new Set(docPatients.map(p => p.user_id)).size);

  setText("total-prescriptions", prescriptions.length);

  // Time-based logic
  const now = new Date();
  const startOfWeek = new Date(now.getFullYear(), now.getMonth(), now.getDate() - now.getDay());
  const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

  const weekCount = prescriptions.filter(
    p => new Date(p.created_at) >= startOfWeek
  ).length;

  const monthCount = prescriptions.filter(
    p => new Date(p.created_at) >= startOfMonth
  ).length;

  setText("total-week", weekCount);
  setText("total-month", monthCount);
}

function renderRecentPrescriptions(prescriptions, patientMap) {
  const tbody = document.querySelector("#recent-prescriptions tbody");
  if (!tbody) return;

  tbody.innerHTML = "";

  const top10 = prescriptions.slice(0, 10);

  top10.forEach(rx => {
    const patient = patientMap[rx.record_id];
    const row = document.createElement("tr");

    row.innerHTML = `
      <td>${patient ? `${patient.first_name} ${patient.last_name}` : "Unknown"}</td>
      <td>${rx.medicine_name ?? rx.medication_name ?? "—"}</td>
      <td>${rx.created_at ? new Date(rx.created_at).toLocaleDateString() : "—"}</td>
      <td>${rx.status ?? "—"}</td>
    `;

    tbody.appendChild(row);
  });
}

function renderFullTable(prescriptions, patientMap) {
  const tableBody = document.querySelector(".patient-tbody");
  if (!tableBody) return;

  tableBody.innerHTML = "";

  if (!prescriptions.length) {
    tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">No prescriptions found</td></tr>`;
    return;
  }

  prescriptions.forEach((p, index) => {
    const patient = patientMap[p.record_id];
    const row = document.createElement("tr");

    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${patient ? patient.first_name + " " + patient.last_name : "Unknown"}</td>
      <td>${p.medicine_name || "-"}</td>
      <td>${p.dosage || "-"}</td>
      <td>${p.created_at ? new Date(p.created_at).toLocaleDateString() : "-"}</td>
    `;

    tableBody.appendChild(row);
  });
}
