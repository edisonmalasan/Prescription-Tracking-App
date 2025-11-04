console.log("Dashboard JS loaded");

const API_BASE = "../../src/api";

async function fetchJSON(url) {
  const res = await fetch(url);
  return res.json();
}

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  await fetchDoctorDashboard(user.user_id);
});

async function fetchDoctorDashboard(doctorId) {
  try {
    // Fetch data in parallel
    const [patientsRes, prescriptionsRes] = await Promise.all([
      fetchJSON(`${API_BASE}/patientRoutes.php?action=all`),
      fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=all`),
    ]);

    const patients = patientsRes.patients || [];
    const prescriptions = (prescriptionsRes.prescriptions || []).filter(
      (p) => p.doctor_id == doctorId
    );

    //map prescription -> patient via medical record
    const patientMap = {};
    patients.forEach((p) => {
      if (p.record_id) patientMap[p.record_id] = p;
    });

    const doctorPatients = prescriptions.map(
      (pres) => patientMap[pres.record_id]
    );

    updateStats(doctorPatients, prescriptions);
    renderTable(prescriptions, patientMap);
  } catch (error) {
    console.error("Error loading dashboard:", error);
  }
}

function updateStats(patients, prescriptions) {
  const setText = (id, val) =>
    (document.getElementById(id).textContent = val ?? "-");

  setText("total-patients", new Set(patients.filter(Boolean).map(p => p.user_id)).size);
  setText("total-prescriptions", prescriptions.length);

  const now = new Date();
  const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
  const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

  const prescriptionsThisWeek = prescriptions.filter(
    (p) => new Date(p.created_at) >= startOfWeek
  );
  const prescriptionsThisMonth = prescriptions.filter(
    (p) => new Date(p.created_at) >= startOfMonth
  );

  setText("total-week", prescriptionsThisWeek.length);
  setText("total-month", prescriptionsThisMonth.length);
}

function renderTable(prescriptions, patientMap) {
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
      <td>${patient ? `${patient.first_name} ${patient.last_name}` : "Unknown"}</td>
      <td>${p.medicine_name || "-"}</td>
      <td>${p.dosage || "-"}</td>
      <td>${p.created_at ? new Date(p.created_at).toLocaleDateString() : "-"}</td>
    `;
    tableBody.appendChild(row);
  });
}
