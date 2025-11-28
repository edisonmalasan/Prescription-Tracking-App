const API_BASE =
  (typeof getAdminApiBase === "function" && getAdminApiBase()) ||
  window.ADMIN_API_BASE ||
  "http://localhost:4000/api/admin";

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

async function loadPrescriptions() {
  const tableBody = document.getElementById("prescriptionTableBody");
  const metaLabel = document.getElementById("prescriptionMeta");
  const messageDiv = document.getElementById("messageDiv");

  tableBody.innerHTML = `
    <tr>
      <td colspan="5" class="px-6 py-4 text-center text-gray-500">
        Loading prescriptions...
      </td>
    </tr>
  `;
  messageDiv.classList.add("hidden");

  try {
    const response = await fetch(`${API_BASE}/prescriptions`);
    const data = await response.json();

    if (!response.ok || !data.success) {
      throw new Error(data.message || "Failed to load prescriptions");
    }

    const prescriptions = data.prescriptions || [];
    metaLabel.textContent = `${prescriptions.length} record${
      prescriptions.length === 1 ? "" : "s"
    }`;

    if (!prescriptions.length) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="5" class="px-6 py-4 text-center text-gray-500">
            No prescriptions found.
          </td>
        </tr>
      `;
      return;
    }

    tableBody.innerHTML = prescriptions
      .map((prescription) => renderPrescriptionRow(prescription))
      .join("");
  } catch (error) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="5" class="px-6 py-4 text-center text-red-600">
          ${error.message}
        </td>
      </tr>
    `;
    showMessage(error.message, "error");
  }
}

function renderPrescriptionRow(prescription) {
  const {
    prescription_id,
    patient_name,
    doctor_name,
    prescription_date,
    created_at,
    status,
  } = prescription;

  const dateLabel = formatDate(prescription_date || created_at);
  const statusPill = formatStatus(status);

  return `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
        #${prescription_id}
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
        ${patient_name || "Unknown"}
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
        ${doctor_name || "Unknown"}
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        ${dateLabel}
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <span class="${statusPill.className}">${statusPill.label}</span>
      </td>
    </tr>
  `;
}

function formatDate(dateString) {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return dateString;
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

function showMessage(message, type = "info") {
  const messageDiv = document.getElementById("messageDiv");
  messageDiv.textContent = message;
  messageDiv.className = `mb-4 px-4 py-3 rounded-lg ${
    type === "error"
      ? "bg-red-50 border border-red-200 text-red-700"
      : "bg-blue-50 border border-blue-200 text-blue-700"
  }`;
  messageDiv.classList.remove("hidden");
}

function logout() {
  sessionStorage.removeItem("admin");
  sessionStorage.removeItem("loggedInUser");
  window.location.href = "/";
}

loadPrescriptions();


