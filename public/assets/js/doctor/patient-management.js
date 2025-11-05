console.log("Patient Management JS loaded");

const API_BASE = "../../src/api";

const $ = (id) => document.getElementById(id);

async function fetchJSON(url) {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`HTTP ${res.status}: ${url}`);
  return res.json();
}

function showMessage(container, message, color = "gray") {
  container.innerHTML = `<tr><td colspan="5" style="text-align:center; color:${color};">${message}</td></tr>`;
}

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));

  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  try {
    await loadPatientTable(user.user_id);
    //await loadMockPatients(); //for testuing
  } catch (err) {
    console.error(err);
    showMessage($("patientList"), "Failed to load patients", "red");
  }

  $("searchPatient")?.addEventListener("input", handleSearch);
});

let allPatients = [];

async function loadPatientTable(doctorId) {
  const tableBody = $("patientList");
  showMessage(tableBody, "Loading...");

  const [patientsRes, prescriptionsRes] = await Promise.all([
    fetchJSON(`${API_BASE}/patientRoutes.php?action=all`),
    fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=all`),
  ]);

  const patients = patientsRes.patients || [];
  const prescriptions = (prescriptionsRes.prescriptions || []).filter(
    (p) => p.doctor_id == doctorId
  );

  const patientMap = Object.fromEntries(
    patients.filter((p) => p.record_id).map((p) => [p.record_id, p])
  );

  allPatients = prescriptions
    .map((pres) => ({ ...patientMap[pres.record_id], prescription: pres }))
    .filter((p) => p && p.user_id);

  renderTable(allPatients);
}

function renderTable(patients) {
  const tableBody = $("patientList");
  tableBody.innerHTML = "";

  if (!patients.length) {
    showMessage(tableBody, "No patients on record");
    return;
  }

  for (const p of patients) {
    const { first_name, last_name, age, contactno, allergies } = p;
    const pres = p.prescription || {};
    const status = pres.status || "Pending";
    const row = document.createElement("tr");

    row.innerHTML = `
      <td>${first_name} ${last_name}</td>
      <td>${age ?? "-"}</td>
      <td>${pres.medicine_name ?? "-"}</td>
      <td><span class="status ${status.toLowerCase()}">${status}</span></td>
      <td><button class="view-btn">View</button></td>
    `;

    row.querySelector(".view-btn").addEventListener("click", () => {
      openModal({
        name: `${first_name} ${last_name}`,
        age: age ?? "-",
        contact: contactno ?? "-",
        medication: pres.medicine_name ?? "-",
        dosage: pres.dosage ?? "-",
        allergies: allergies || "None",
        visits: pres.visit_dates || "No records",
      });
    });

    tableBody.appendChild(row);
  }
}

function handleSearch(e) {
  const q = e.target.value.trim().toLowerCase();
  const filtered = allPatients.filter((p) =>
    `${p.first_name} ${p.last_name}`.toLowerCase().includes(q)
  );
  renderTable(filtered);
}

function openModal(data) {
  $("modalName").textContent = data.name;
  $("modalAge").textContent = data.age;
  $("modalContact").textContent = data.contact;
  $("modalMedication").textContent = data.medication;
  $("modalDosage").textContent = data.dosage;
  $("modalAllergies").textContent = data.allergies;
  $("modalVisits").textContent = data.visits;

  $("patientModal").style.display = "block";
}

function closeModal() {
  $("patientModal").style.display = "none";
}

window.onclick = (e) => {
  if (e.target === $("patientModal")) closeModal();
};

function loadMockPatients() {
  const mockPatients = [
    {
      user_id: 1,
      first_name: "John",
      last_name: "Doe",
      age: 45,
      contactno: "09123456789",
      allergies: "Penicillin",
      prescription: {
        medicine_name: "Amoxicillin",
        dosage: "500mg",
        status: "Ongoing",
        visit_dates: "2025-11-01, 2025-11-03",
      },
    },
    {
      user_id: 2,
      first_name: "Jane",
      last_name: "Smith",
      age: 34,
      contactno: "09987654321",
      allergies: "None",
      prescription: {
        medicine_name: "Metformin",
        dosage: "850mg",
        status: "Completed",
        visit_dates: "2025-10-28, 2025-10-30",
      },
    },
  ];

  renderTable(mockPatients);
}
