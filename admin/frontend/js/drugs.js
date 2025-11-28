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

let drugs = [];

async function loadDrugs() {
  const tableBody = document.getElementById("drugTableBody");
  const messageDiv = document.getElementById("messageDiv");

  tableBody.innerHTML = `
    <tr>
      <td colspan="6" class="px-6 py-4 text-center text-gray-500">
        Loading drugs...
      </td>
    </tr>
  `;
  messageDiv.classList.add("hidden");

  try {
    const response = await fetch(`${API_BASE}/drugs`);
    const data = await response.json();

    if (!response.ok || !data.success) {
      throw new Error(data.message || "Failed to load drugs");
    }

    drugs = data.drugs || [];
    if (!drugs.length) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="px-6 py-4 text-center text-gray-500">
            No drugs found. Click "Add Drug" to create one.
          </td>
        </tr>
      `;
      return;
    }

    tableBody.innerHTML = drugs.map((drug) => renderDrugRow(drug)).join("");
  } catch (error) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="6" class="px-6 py-4 text-center text-red-600">
          ${error.message}
        </td>
      </tr>
    `;
  }
}

function renderDrugRow(drug) {
  return `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
        ${drug.drug_id}
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
        ${drug.generic_name}
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        ${drug.brand || "—"}
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        ${drug.category || "—"}
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${
          drug.isControlled
            ? "bg-red-100 text-red-800"
            : "bg-green-100 text-green-800"
        }">
          ${drug.isControlled ? "Yes" : "No"}
        </span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
        <button
          class="text-blue-600 hover:text-blue-900"
          onclick="openDrugModal(${drug.drug_id})"
        >
          Edit
        </button>
        <button
          class="text-red-600 hover:text-red-900"
          onclick="deleteDrug(${drug.drug_id})"
        >
          Delete
        </button>
      </td>
    </tr>
  `;
}

function openDrugModal(drugId = null) {
  const modal = document.getElementById("drugModal");
  const title = document.getElementById("drugModalTitle");
  const errorBox = document.getElementById("drugModalError");
  errorBox.classList.add("hidden");
  errorBox.textContent = "";

  if (drugId) {
    const drug = drugs.find((d) => d.drug_id === drugId);
    if (!drug) return;
    title.textContent = "Edit Drug";
    document.getElementById("drugId").value = drug.drug_id;
    document.getElementById("genericName").value = drug.generic_name || "";
    document.getElementById("brand").value = drug.brand || "";
    document.getElementById("chemicalName").value = drug.chemical_name || "";
    document.getElementById("category").value = drug.category || "";
    document.getElementById("isControlled").checked = !!drug.isControlled;
  } else {
    title.textContent = "Add Drug";
    document.getElementById("drugForm").reset();
    document.getElementById("drugId").value = "";
  }

  modal.classList.remove("hidden");
}

function closeDrugModal() {
  document.getElementById("drugModal").classList.add("hidden");
}

document.getElementById("drugForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const errorBox = document.getElementById("drugModalError");
  errorBox.classList.add("hidden");

  const drugId = document.getElementById("drugId").value;
  const payload = {
    generic_name: document.getElementById("genericName").value,
    brand: document.getElementById("brand").value || null,
    chemical_name: document.getElementById("chemicalName").value || null,
    category: document.getElementById("category").value || null,
    isControlled: document.getElementById("isControlled").checked ? 1 : 0,
  };

  try {
    const response = await fetch(
      drugId ? `${API_BASE}/drugs/${drugId}` : `${API_BASE}/drugs`,
      {
        method: drugId ? "PUT" : "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      }
    );

    const data = await response.json();
    if (!response.ok || !data.success) {
      throw new Error(data.message || "Operation failed");
    }

    closeDrugModal();
    showMessage(
      drugId ? "Drug updated successfully" : "Drug created successfully",
      "success"
    );
    loadDrugs();
  } catch (error) {
    errorBox.textContent = error.message;
    errorBox.classList.remove("hidden");
  }
});

async function deleteDrug(drugId) {
  if (
    !confirm(
      "Are you sure you want to delete this drug? This action cannot be undone."
    )
  ) {
    return;
  }

  try {
    const response = await fetch(`${API_BASE}/drugs/${drugId}`, {
      method: "DELETE",
    });
    const data = await response.json();
    if (!response.ok || !data.success) {
      throw new Error(data.message || "Failed to delete drug");
    }
    showMessage("Drug deleted successfully", "success");
    loadDrugs();
  } catch (error) {
    showMessage(error.message, "error");
  }
}

function showMessage(message, type = "info") {
  const messageDiv = document.getElementById("messageDiv");
  messageDiv.textContent = message;
  messageDiv.className = `mb-6 px-4 py-3 rounded-lg ${
    type === "error"
      ? "bg-red-50 border border-red-200 text-red-700"
      : "bg-green-50 border border-green-200 text-green-700"
  }`;
  messageDiv.classList.remove("hidden");
  setTimeout(() => messageDiv.classList.add("hidden"), 4000);
}

function logout() {
  sessionStorage.removeItem("admin");
  sessionStorage.removeItem("loggedInUser");
  window.location.href = "/";
}

loadDrugs();


