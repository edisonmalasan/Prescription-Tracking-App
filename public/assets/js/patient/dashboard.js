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
    tableBody.innerHTML = `
      <tr>
        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
          No prescriptions found.
        </td>
      </tr>`;
    return;
  }

  const prescriptions = res.prescriptions;

  // update stats
  totalEl.textContent = prescriptions.length;
  activeEl.textContent = prescriptions.filter(p => p.status === "active").length;

  // Fetch full details for each prescription
  const detailedList = await Promise.all(
    prescriptions.map(async (rx) => {
      const detailsRes = await api.get(
        `prescriptionRoutes.php?action=details&prescription_id=${rx.prescription_id}`
      );

      const details = detailsRes.details || [];

      // Fetch generic names
      for (let d of details) {
        if (!d.drug_id) continue;

        try {
          const drugRes = await api.get(
            `drugRoutes.php?action=get&drug_id=${d.drug_id}`
          );
          d.generic_name = drugRes.drug?.generic_name || "Unknown";
        } catch {
          d.generic_name = "Unknown";
        }
      }

      return { ...rx, details };
    })
  );

  renderPrescriptionAccordion(detailedList);
}

function renderPrescriptionAccordion(list) {
  const container = document.querySelector("#recent-prescriptions tbody");

  if (!list.length) {
    container.innerHTML = `
      <tr>
        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
          No prescriptions found
        </td>
      </tr>`;
    return;
  }

  container.innerHTML = "";

  list.forEach((rx) => {
    const statusColor =
      rx.status === "active"
        ? "text-green-600 bg-green-100"
        : "text-gray-600 bg-gray-200";

    const detailsHTML = rx.details
      .map(
        (d) => `
      <div class="border-b py-3">
        <div class="font-semibold text-gray-800">${d.generic_name}</div>
        <div class="text-sm text-gray-600">Dosage: ${d.dosage ?? "—"}</div>
        <div class="text-sm text-gray-600">Frequency: ${d.frequency ?? "—"}</div>
        <div class="text-sm text-gray-600">Duration: ${d.duration ?? "—"} days</div>
        <div class="text-sm text-gray-600">Refills: ${d.refills ?? 0}</div>
        <div class="text-sm text-gray-600 italic">${d.special_instructions ?? ""}</div>
      </div>`
      )
      .join("");

    const item = document.createElement("tr");
    item.innerHTML = `
      <td colspan="5" class="p-0">
        <div class="border rounded-xl overflow-hidden mb-4">

          <!-- Header -->
          <button class="w-full flex justify-between items-center px-6 py-4 bg-white hover:bg-blue-50 transition">
            <div>
              <div class="font-bold text-lg text-gray-800">Prescription #${rx.prescription_id}</div>
              <div class="text-sm text-gray-600">
                Dr. ${rx.doctor_name ?? "—"} &middot; ${formatDate(rx.prescription_date)}
              </div>
            </div>

            <div class="flex items-center gap-3">
              <span class="${statusColor} px-3 py-1 rounded-full text-xs font-semibold">
                ${capitalize(rx.status)}
              </span>

              <svg class="w-5 h-5 text-gray-600 accordion-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </button>

          <!-- Body -->
          <div class="hidden accordion-body bg-gray-50 px-6 py-4">
            <div class="font-semibold text-gray-700 mb-2">Medicines</div>
            ${detailsHTML}

            <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition view-full-btn"
              data-id="${rx.prescription_id}">
              View Full Prescription
            </button>
          </div>
        </div>
      </td>
    `;

    // Append to table
    container.appendChild(item);

    // Add accordion toggle behavior
    const btn = item.querySelector("button");
    const body = item.querySelector(".accordion-body");
    const icon = item.querySelector(".accordion-icon");

    btn.addEventListener("click", () => {
      body.classList.toggle("hidden");
      icon.classList.toggle("rotate-180");
    });

    // Add redirect button
    item.querySelector(".view-full-btn").addEventListener("click", (e) => {
      const id = e.target.dataset.id;
      sessionStorage.setItem("selectedPrescriptionId", id);
      window.location.href = `./myprescription.php?prescription_id=${id}`;
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
