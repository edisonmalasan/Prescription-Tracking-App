const API_BASE =
  (typeof getAdminApiBase === "function" && getAdminApiBase()) ||
  window.ADMIN_API_BASE ||
  "http://localhost:4000/api/admin";

// auth checker that supports both admin frontend login and PHP login
let admin = JSON.parse(sessionStorage.getItem("admin") || "null");
if (!admin) {
  const loggedInUser = JSON.parse(
    sessionStorage.getItem("loggedInUser") || "null"
  );
  if (loggedInUser && loggedInUser.role === "ADMIN") {
    admin = loggedInUser;
    sessionStorage.setItem("admin", JSON.stringify(admin));
  } else {
    window.location.href = "/";
  }
}

async function loadDashboard() {
  const loadingState = document.getElementById("loadingState");
  const errorState = document.getElementById("errorState");
  const statsContainer = document.getElementById("statsContainer");
  const prescriptionListContainer = document.getElementById(
    "prescriptionListContainer"
  );

  try {
    const response = await fetch(`${API_BASE}/dashboard/summary`);
    const data = await response.json();

    if (data.success) {
      displayStats(data.summary);
      displayPrescriptionList(data.summary.prescriptions || []);
      loadingState.classList.add("hidden");
      statsContainer.classList.remove("hidden");
      prescriptionListContainer.classList.remove("hidden");
    } else {
      throw new Error(data.message || "Failed to load dashboard");
    }
  } catch (error) {
    loadingState.classList.add("hidden");
    errorState.textContent = `Error: ${error.message}`;
    errorState.classList.remove("hidden");
  }
}

function displayStats(summary) {
  const statsContainer = document.getElementById("statsContainer");
  const counts = summary.counts || {};

  const stats = [
    { label: "Total Users", value: counts.users || 0, color: "blue" },
    { label: "Doctors", value: counts.doctors || 0, color: "green" },
    { label: "Patients", value: counts.patients || 0, color: "purple" },
    {
      label: "Pharmacies",
      value: counts.pharmacies || 0,
      color: "orange",
    },
  ];

  statsContainer.innerHTML = stats
    .map(
      (stat) => `
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">${stat.label}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">${stat.value}</p>
                        </div>
                        <div class="bg-${stat.color}-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-${stat.color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            `
    )
    .join("");
}

function displayPrescriptionList(prescriptions) {
  const tableBody = document.getElementById("prescriptionListBody");
  const metaLabel = document.getElementById("prescriptionListMeta");

  if (!prescriptions.length) {
    metaLabel.textContent = "No prescriptions found";
    tableBody.innerHTML = `
      <tr>
        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
          No prescription data available yet.
        </td>
      </tr>
    `;
    return;
  }

  metaLabel.textContent = `${prescriptions.length} recent ${
    prescriptions.length === 1 ? "prescription" : "prescriptions"
  }`;

  tableBody.innerHTML = prescriptions
    .map((item) => {
      const dateLabel = formatDate(item.prescription_date || item.created_at);
      const status = formatStatus(item.status);
      return `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
            #${item.prescription_id}
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
            ${item.patient_name || "Unknown patient"}
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
            ${item.doctor_name || "Unknown doctor"}
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
            ${dateLabel}
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            <span class="${status.className}">${status.label}</span>
          </td>
        </tr>
      `;
    })
    .join("");
}

function formatDate(dateString) {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) {
    return dateString;
  }
  return date.toLocaleDateString(undefined, {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
}

function formatStatus(status = "") {
  const normalized = status.toUpperCase();
  const baseClass =
    "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold";

  switch (normalized) {
    case "ACTIVE":
      return {
        label: "Active",
        className: `${baseClass} bg-green-100 text-green-800`,
      };
    case "COMPLETED":
      return {
        label: "Completed",
        className: `${baseClass} bg-blue-100 text-blue-800`,
      };
    case "CANCELLED":
    case "CANCELED":
      return {
        label: "Cancelled",
        className: `${baseClass} bg-red-100 text-red-800`,
      };
    default:
      return {
        label: normalized || "Unknown",
        className: `${baseClass} bg-gray-100 text-gray-700`,
      };
  }
}

function logout() {
  sessionStorage.removeItem("admin");
  sessionStorage.removeItem("loggedInUser");
  window.location.href = "/";
}

loadDashboard();
