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

let doctors = [];
let pharmacies = [];

async function loadDoctors() {
  const loadingState = document.getElementById("loadingState");
  const doctorsSection = document.getElementById("doctorsSection");

  try {
    const response = await fetch(`${API_BASE}/doctors`);
    const data = await response.json();

    if (data.success) {
      doctors = data.doctors || [];
      displayDoctors(doctors);
      loadingState.classList.add("hidden");
      doctorsSection.classList.remove("hidden");
    } else {
      throw new Error(data.message || "Failed to load doctors");
    }
  } catch (error) {
    loadingState.classList.add("hidden");
    showMessage(`Error: ${error.message}`, "error");
  }
}

async function loadPharmacies() {
  const loadingState = document.getElementById("loadingState");
  const pharmaciesSection = document.getElementById("pharmaciesSection");

  try {
    const response = await fetch(`${API_BASE}/pharmacies`);
    const data = await response.json();

    if (data.success) {
      pharmacies = data.pharmacies || [];
      displayPharmacies(pharmacies);
      loadingState.classList.add("hidden");
      pharmaciesSection.classList.remove("hidden");
    } else {
      throw new Error(data.message || "Failed to load pharmacies");
    }
  } catch (error) {
    loadingState.classList.add("hidden");
    showMessage(`Error: ${error.message}`, "error");
  }
}

function displayDoctors(doctorsList) {
  const tbody = document.getElementById("doctorsTableBody");
  tbody.innerHTML = doctorsList
    .map((doctor) => {
      const isVerified = doctor.isVerified === 1 || doctor.isVerified === true;
      return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                  doctor.user_id
                }</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
                  doctor.first_name
                } ${doctor.last_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
                  doctor.email
                }</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
                  doctor.prc_license || "N/A"
                }</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
                  doctor.specialization || "N/A"
                }</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${
                      isVerified
                        ? "bg-green-100 text-green-800"
                        : "bg-yellow-100 text-yellow-800"
                    }">
                        ${isVerified ? "Verified" : "Pending"}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="toggleDoctorVerification(${
                      doctor.user_id
                    }, ${!isVerified})" 
                        class="${
                          isVerified
                            ? "text-yellow-600 hover:text-yellow-900"
                            : "text-green-600 hover:text-green-900"
                        }">
                        ${isVerified ? "Revoke" : "Verify"}
                    </button>
                </td>
            </tr>
        `;
    })
    .join("");
}

function displayPharmacies(pharmaciesList) {
  const tbody = document.getElementById("pharmaciesTableBody");
  tbody.innerHTML = pharmaciesList
    .map((pharmacy) => {
      const isVerified =
        pharmacy.isVerified === 1 || pharmacy.isVerified === true;
      return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                  pharmacy.user_id
                }</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
                  pharmacy.first_name
                } ${pharmacy.last_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
                  pharmacy.email
                }</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
                  pharmacy.pharmacy_name || "N/A"
                }</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
                  pharmacy.phar_license || "N/A"
                }</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${
                      isVerified
                        ? "bg-green-100 text-green-800"
                        : "bg-yellow-100 text-yellow-800"
                    }">
                        ${isVerified ? "Verified" : "Pending"}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="togglePharmacyVerification(${
                      pharmacy.user_id
                    }, ${!isVerified})" 
                        class="${
                          isVerified
                            ? "text-yellow-600 hover:text-yellow-900"
                            : "text-green-600 hover:text-green-900"
                        }">
                        ${isVerified ? "Revoke" : "Verify"}
                    </button>
                </td>
            </tr>
        `;
    })
    .join("");
}

async function toggleDoctorVerification(userId, isVerified) {
  try {
    const response = await fetch(`${API_BASE}/doctors/${userId}/verify`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ isVerified: isVerified ? 1 : 0 }),
    });

    const data = await response.json();
    if (response.ok && data.success) {
      showMessage(
        `Doctor ${
          isVerified ? "verified" : "verification revoked"
        } successfully`,
        "success"
      );
      loadDoctors();
    } else {
      showMessage(data.message || "Operation failed", "error");
    }
  } catch (error) {
    showMessage(`Error: ${error.message}`, "error");
  }
}

async function togglePharmacyVerification(userId, isVerified) {
  try {
    const response = await fetch(`${API_BASE}/pharmacies/${userId}/verify`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ isVerified: isVerified ? 1 : 0 }),
    });

    const data = await response.json();
    if (response.ok && data.success) {
      showMessage(
        `Pharmacy ${
          isVerified ? "verified" : "verification revoked"
        } successfully`,
        "success"
      );
      loadPharmacies();
    } else {
      showMessage(data.message || "Operation failed", "error");
    }
  } catch (error) {
    showMessage(`Error: ${error.message}`, "error");
  }
}

function showDoctors() {
  document
    .getElementById("doctorsTab")
    .classList.add("border-blue-500", "text-blue-600");
  document
    .getElementById("doctorsTab")
    .classList.remove("border-transparent", "text-gray-500");
  document
    .getElementById("pharmaciesTab")
    .classList.remove("border-blue-500", "text-blue-600");
  document
    .getElementById("pharmaciesTab")
    .classList.add("border-transparent", "text-gray-500");

  document.getElementById("doctorsSection").classList.remove("hidden");
  document.getElementById("pharmaciesSection").classList.add("hidden");

  if (doctors.length === 0) {
    loadDoctors();
  }
}

function showPharmacies() {
  document
    .getElementById("pharmaciesTab")
    .classList.add("border-blue-500", "text-blue-600");
  document
    .getElementById("pharmaciesTab")
    .classList.remove("border-transparent", "text-gray-500");
  document
    .getElementById("doctorsTab")
    .classList.remove("border-blue-500", "text-blue-600");
  document
    .getElementById("doctorsTab")
    .classList.add("border-transparent", "text-gray-500");

  document.getElementById("pharmaciesSection").classList.remove("hidden");
  document.getElementById("doctorsSection").classList.add("hidden");

  if (pharmacies.length === 0) {
    loadPharmacies();
  }
}

function showMessage(message, type) {
  const messageDiv = document.getElementById("messageDiv");
  messageDiv.textContent = message;
  messageDiv.className = `mb-6 px-4 py-3 rounded-lg ${
    type === "success"
      ? "bg-green-50 border border-green-200 text-green-700"
      : "bg-red-50 border border-red-200 text-red-700"
  }`;
  messageDiv.classList.remove("hidden");
  setTimeout(() => messageDiv.classList.add("hidden"), 5000);
}

function logout() {
  sessionStorage.removeItem("admin");
  sessionStorage.removeItem("loggedInUser");
  window.location.href = "/";
}

showDoctors();
