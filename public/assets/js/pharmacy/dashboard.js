console.log("Pharmacy Dashboard JS loaded");

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
  put: (endpoint, data) =>
    apiCall(`${API_BASE}/${endpoint}`, {
      method: "PUT",
      body: JSON.stringify(data),
    }),
};

//dom refs
const nameEl = document.getElementById("pharmacy-name");
const addressEl = document.getElementById("pharmacy-address");
const totalEl = document.getElementById("stat-total-prescriptions");
const pendingEl = document.getElementById("stat-pending-prescriptions");
const filledEl = document.getElementById("stat-filled-prescriptions");
const tableBody = document.querySelector("#prescriptions-table tbody");

const patientFilter = document.getElementById("patient-name-filter");
const drugFilter = document.getElementById("drug-name-filter");

let allPrescriptions = [];

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "PHARMACY") {
    window.location.href = "../../../public/login.html";
    return;
  }

  try {
    await loadPharmacyProfile(user.user_id);
    await loadPrescriptions();
  } catch (err) {
    console.error("Error loading pharmacy dashboard:", err);
    alert("Failed to load pharmacy dashboard data.");
  }

  //filtor
  [patientFilter, drugFilter].forEach((input) =>
    input.addEventListener("input", applyFilters)
  );
});

async function loadPharmacyProfile(pharmacyId) {
  const res = await api.get(`pharmacyRoutes.php?action=profile&user_id=${pharmacyId}`);
  console.log("[DEBUG] Pharmacy profile response:", res);

  const p = res.profile ?? res.data ?? res.pharmacy ?? null;
  if (!p) throw new Error("Failed to load pharmacy profile");

  const fullName = [p.first_name, p.last_name].filter(Boolean).join(" ");
  nameEl.textContent = `Welcome, ${fullName || "Pharmacy User"}`;
  addressEl.textContent = `Address: ${p.address ?? "—"}`;
}

async function loadPrescriptions() {
  const res = await api.get(`prescriptionRoutes.php?action=all`);
  if (!res.success || !Array.isArray(res.prescriptions)) {
    tableBody.innerHTML = `<tr><td colspan="9">No prescriptions found</td></tr>`;
    return;
  }

  allPrescriptions = res.prescriptions;
  renderStats(allPrescriptions);
  renderTable(allPrescriptions);
}

function renderStats(data) {
  totalEl.textContent = data.length;
  pendingEl.textContent = data.filter((p) => p.status === "pending").length;
  filledEl.textContent = data.filter((p) => p.status === "filled").length;
}

function renderTable(data) {
  if (!data.length) {
    tableBody.innerHTML = `<tr><td colspan="9">No prescriptions match the filters</td></tr>`;
    return;
  }

  tableBody.innerHTML = data
    .map((p) => {
      const date = formatDate(p.prescription_date);
      const statusClass =
        p.status === "pending"
          ? "status-pending"
          : p.status === "filled"
          ? "status-filled"
          : p.status === "dispensed"
          ? "status-dispensed"
          : "status-other";

      //dropdown actions --for status scaling cs idk
      const actions = [];
      if (p.status === "pending") {
        actions.push(`
          <button class="dropdown-item fill-btn" data-id="${p.prescription_id}" data-action="filled">
            ✅ Mark as Filled
          </button>
          <button class="dropdown-item cancel-btn" data-id="${p.prescription_id}" data-action="cancelled">
            ❌ Cancel
          </button>`);
      } else if (p.status === "filled") {
        actions.push(`
          <button class="dropdown-item dispense-btn" data-id="${p.prescription_id}" data-action="dispensed">
            💊 Mark as Dispensed
          </button>`);
      }

      const actionHTML = actions.length
        ? `
          <div class="dropdown">
            <button class="btn small action-toggle" title="Actions">⋮</button>
            <div class="dropdown-menu">
              ${actions.join("")}
            </div>
          </div>`
        : `<span class="text-muted">—</span>`;

      return `
        <tr>
          <td>${p.patient_name ?? "—"}</td>
          <td>${p.doctor_name ?? "—"}</td>
          <td>${p.medication_name ?? "—"}</td>
          <td>${p.dosage ?? "—"}</td>
          <td>${p.duration ?? "—"}</td>
          <td>${date}</td>
          <td>${p.notes ?? "—"}</td>
          <td class="${statusClass}">${capitalize(p.status)}</td>
          <td>${actionHTML}</td>
        </tr>
      `;
    })
    .join("");

  document.querySelectorAll(".dropdown-item").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      const prescId = e.target.dataset.id;
      const newStatus = e.target.dataset.action;
      await updatePrescriptionStatus(prescId, newStatus);
    });
  });
}



async function updatePrescriptionStatus(prescriptionId, newStatus) {
  if (!confirm(`Are you sure you want to mark this prescription as "${newStatus}"?`)) return;

  try {
    const res = await api.put(
      `prescriptionRoutes.php?action=update&prescription_id=${prescriptionId}`,
      { status: newStatus }
    );

    if (res.success) {
      alert(`Prescription updated to "${capitalize(newStatus)}"!`);
      await loadPrescriptions();
    } else {
      alert(res.error || "Failed to update prescription status.");
    }
  } catch (err) {
    console.error("Error updating prescription:", err);
    alert("Server error while updating status.");
  }
}


function applyFilters() {
  const patientQuery = patientFilter.value.toLowerCase();
  const drugQuery = drugFilter.value.toLowerCase();

  const filtered = allPrescriptions.filter((p) => {
    const patientMatch = p.patient_name?.toLowerCase().includes(patientQuery);
    const drugMatch = p.medication_name?.toLowerCase().includes(drugQuery);
    return patientMatch && drugMatch;
  });

  renderTable(filtered);
  renderStats(filtered);
}

function formatDate(dateStr) {
  if (!dateStr) return "—";
  const d = new Date(dateStr);
  return isNaN(d) ? "—" : d.toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
}

function capitalize(str) {
  return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
}
