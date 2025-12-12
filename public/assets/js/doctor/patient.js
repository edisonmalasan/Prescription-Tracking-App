console.log("Patient Managememnt JS loaded");

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

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  if (!res.ok) {
    const text = await res.text();
    throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`);
  }
  try {
    return await res.json();
  } catch {
    const text = await res.text();
    console.error("Invalid JSON from server:", text);
    throw new Error("Invalid JSON response");
  }
}

// Age helper
function formatAge(dob) {
  if (!dob) return "—";
  const birth = new Date(dob);
  return new Date().getFullYear() - birth.getFullYear();
}

document.addEventListener("DOMContentLoaded", async () => {
  //auth
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  await loadPatients(user.user_id);

  document.getElementById("search-btn").addEventListener("click", () => {
    const q = document.getElementById("search-patient").value.trim();
    loadPatients(user.user_id, q);
  });

  document
    .querySelector(".close-modal")
    .addEventListener("click", () => hideModal("patient-modal"));

  document
    .getElementById("save-patient-btn")
    .addEventListener("click", saveNewPatient);
});

async function loadPatients(doctorId, query = "") {
  try {
    const res = await fetchJSON(
      `${API_BASE}/patientRoutes.php?action=by-doctor&user_id=${doctorId}`
    );
    const patients = res.patients ?? [];

    const tbody = document.querySelector("#patients-table tbody");
    tbody.innerHTML = "";

    const filtered = query
      ? patients.filter((p) =>
          `${p.first_name} ${p.last_name}`
            .toLowerCase()
            .includes(query.toLowerCase())
        )
      : patients;

    if (!filtered.length) {
      tbody.innerHTML = `<tr><td colspan="4">No patients found</td></tr>`;
      return;
    }

    filtered.forEach((p) => {
      const tr = document.createElement("tr");
      tr.className = "hover:bg-blue-50 transition-colors duration-150";
      tr.innerHTML = `
        <td class="px-6 py-3 text-sm font-medium text-gray-900">${
          p.first_name
        } ${p.last_name}</td>
        <td class="px-6 py-3 text-sm text-gray-700">${formatAge(
          p.birth_date
        )}</td>
        <td class="px-6 py-3 text-sm text-gray-700">${p.contactno ?? "—"}</td>
        <td class="px-6 py-3 text-sm">
          <button class="view-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md" data-id="${
            p.user_id
          }">
            View
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    document.querySelectorAll(".view-btn").forEach((btn) => {
      btn.addEventListener("click", () => openPatientModal(btn.dataset.id));
    });
  } catch (err) {
    console.error("Error loading patients:", err);
  }
}

async function openPatientModal(userId) {
  try {
    const [profileRes, recordRes, prescriptionsRes] = await Promise.all([
      fetchJSON(
        `${API_BASE}/patientRoutes.php?action=profile&user_id=${userId}`
      ),
      fetchJSON(
        `${API_BASE}/patientRoutes.php?action=medical-record&user_id=${userId}`
      ),
      fetchJSON(
        `${API_BASE}/prescriptionRoutes.php?action=by-patient&patient_id=${userId}`
      ),
    ]);

    const profile = profileRes.patient ?? {};
    const record = recordRes.medical_record ?? {};
    const prescriptions = prescriptionsRes.prescriptions ?? [];

    const prescriptionsWithDetails = await Promise.all(
      prescriptions.map(async (rx) => {
        try {
          const detailRes = await fetchJSON(
            `${API_BASE}/prescriptionRoutes.php?action=details&prescription_id=${rx.prescription_id}`
          );
          rx.details = detailRes.details ?? [];

          //now fetch drug name from drug
          rx.details = await Promise.all(
            rx.details.map(async (d) => {
              if (d.drug_id) {
                try {
                  const drugRes = await fetchJSON(
                    `${API_BASE}/drugRoutes.php?action=get&drug_id=${d.drug_id}`
                  );
                  d.generic_name = drugRes.drug?.generic_name ?? "Unknown";
                } catch {
                  d.generic_name = "Unknown";
                }
              } else {
                d.generic_name = "—";
              }
              return d;
            })
          );
        } catch {
          rx.details = [];
        }
        return rx;
      })
    );

    const activePrescriptions = prescriptionsWithDetails.filter(
      (rx) => rx.status === "pending"
    );

    const activeRows =
      activePrescriptions
        .flatMap((rx) => rx.details ?? [])
        .map(
          (d) => `
        <tr class="hover:bg-blue-50 transition-colors duration-150">
          <td class="px-4 py-3 text-sm font-medium text-gray-900">${
            d.generic_name ?? "—"
          }</td>
          <td class="px-4 py-3 text-sm text-gray-700">${d.dosage ?? "—"}</td>
          <td class="px-4 py-3 text-sm text-gray-700">${d.frequency ?? "—"}</td>
        </tr>
      `
        )
        .join("") ||
      `<tr>
        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
          <div class="flex flex-col items-center justify-center">
            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
            <p class="text-sm">No active prescriptions</p>
          </div>
        </td>
      </tr>`;

    document.getElementById("patient-modal-content").innerHTML = `
      <!-- Patient Header Card -->
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 mb-6 border border-blue-200">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
            ${(profile.first_name?.[0] || "") + (profile.last_name?.[0] || "")}
          </div>
          <div>
            <h3 class="text-2xl font-bold text-gray-800">${
              profile.first_name || ""
            } ${profile.last_name || ""}</h3>
            <p class="text-gray-600 text-sm">Patient ID: ${
              profile.user_id || "—"
            }</p>
          </div>
        </div>
      </div>

      <!-- Information Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Patient Information Card -->
        <div class="bg-white border-2 border-blue-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center gap-3 mb-4">
            <div class="bg-blue-100 rounded-lg p-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-800">Patient Information</h4>
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
              <span class="text-sm font-medium text-gray-600">Age</span>
              <span class="text-sm font-semibold text-gray-900">${formatAge(
                profile.birth_date
              )} years</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
              <span class="text-sm font-medium text-gray-600">Birth Date</span>
              <span class="text-sm font-semibold text-gray-900">${
                profile.birth_date
                  ? new Date(profile.birth_date).toLocaleDateString()
                  : "—"
              }</span>
            </div>
            <div class="flex items-center justify-between py-2">
              <span class="text-sm font-medium text-gray-600">Contact</span>
              <span class="text-sm font-semibold text-gray-900">${
                profile.contactno ?? "—"
              }</span>
            </div>
          </div>
        </div>

        <!-- Allergies Card -->
        <div class="bg-white border-2 border-blue-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center gap-3 mb-4">
            <div class="bg-red-100 rounded-lg p-2">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-800">Allergies</h4>
          </div>
          <div class="py-2">
            <p class="text-sm text-gray-700 ${
              !record.allergies ||
              record.allergies === "None" ||
              record.allergies === "N/A"
                ? "text-gray-500 italic"
                : "font-medium"
            }">
              ${
                record.allergies &&
                record.allergies !== "None" &&
                record.allergies !== "N/A"
                  ? record.allergies
                  : "No known allergies"
              }
            </p>
          </div>
        </div>
      </div>

      <!-- Prescriptions Section -->
      <div class="bg-white border-2 border-blue-200 rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
          <div class="bg-blue-100 rounded-lg p-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
          </div>
          <h4 class="text-lg font-semibold text-gray-800">Active Prescriptions</h4>
          <span class="ml-auto bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">${
            activePrescriptions.length
          }</span>
        </div>
        
        <div class="overflow-x-auto rounded-lg border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-blue-600 to-blue-700">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Medication</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Dosage</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Frequency</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              ${activeRows}
            </tbody>
          </table>
        </div>
      </div>
    `;

    showModal("patient-modal");
  } catch (err) {
    console.error("Error opening patient modal:", err);
  }
}



function showModal(id) {
  const el = document.getElementById(id);
  console.log("showModal called for:", id, "element:", el);
  if (!el) {
    console.error("Modal not found:", id);
    return;
  }
  el.classList.remove("hidden");
}
function hideModal(id) {
  document.getElementById(id).classList.add("hidden");
}
