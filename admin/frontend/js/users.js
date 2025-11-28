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

let users = [];

async function loadUsers() {
  const loadingState = document.getElementById("loadingState");
  const usersTableContainer = document.getElementById("usersTableContainer");

  try {
    const response = await fetch(`${API_BASE}/users`);
    const data = await response.json();

    if (data.success) {
      users = data.users || [];
      displayUsers(users);
      loadingState.classList.add("hidden");
      usersTableContainer.classList.remove("hidden");
    } else {
      throw new Error(data.message || "Failed to load users");
    }
  } catch (error) {
    loadingState.classList.add("hidden");
    showMessage(`Error: ${error.message}`, "error");
  }
}

function displayUsers(usersList) {
  const tbody = document.getElementById("usersTableBody");
  tbody.innerHTML = usersList
    .map(
      (user) => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
              user.user_id
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
              user.first_name
            } ${user.last_name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
              user.email
            }</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${
                  user.role === "ADMIN"
                    ? "bg-red-100 text-red-800"
                    : user.role === "DOCTOR"
                    ? "bg-green-100 text-green-800"
                    : user.role === "PHARMACY"
                    ? "bg-orange-100 text-orange-800"
                    : "bg-blue-100 text-blue-800"
                }">
                    ${user.role}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
              user.contactno || "N/A"
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="editUser(${
                  user.user_id
                })" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                <button onclick="deleteUser(${
                  user.user_id
                })" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
        </tr>
    `
    )
    .join("");
}

function openCreateModal() {
  document.getElementById("modalTitle").textContent = "Create User";
  document.getElementById("userForm").reset();
  document.getElementById("userId").value = "";
  document.getElementById("modalError").classList.add("hidden");
  resetRoleFields();
  handleRoleFields(false);
  document.getElementById("userModal").classList.remove("hidden");
}

function editUser(userId) {
  const user = users.find((u) => u.user_id === userId);
  if (!user) return;

  document.getElementById("modalTitle").textContent = "Edit User";
  document.getElementById("userId").value = user.user_id;
  document.getElementById("firstName").value = user.first_name || "";
  document.getElementById("lastName").value = user.last_name || "";
  document.getElementById("email").value = user.email || "";
  document.getElementById("role").value = user.role || "";
  document.getElementById("contactno").value = user.contactno || "";
  document.getElementById("address").value = user.address || "";
  document.getElementById("password").value = "";
  document.getElementById("password").required = false;
  document.getElementById("modalError").classList.add("hidden");
  resetRoleFields();
  handleRoleFields(true);
  document.getElementById("userModal").classList.remove("hidden");
}

function closeModal() {
  document.getElementById("userModal").classList.add("hidden");
}

document.getElementById("userForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const errorDiv = document.getElementById("modalError");
  errorDiv.classList.add("hidden");

  const userId = document.getElementById("userId").value;
  const roleValue = document.getElementById("role").value;
  const userData = {
    user: {
      first_name: document.getElementById("firstName").value.trim(),
      last_name: document.getElementById("lastName").value.trim(),
      email: document.getElementById("email").value.trim(),
      role: roleValue,
      contactno: document.getElementById("contactno").value.trim(),
      address: document.getElementById("address").value.trim(),
    },
  };

  if (document.getElementById("password").value) {
    userData.user.password = document.getElementById("password").value;
  }

  const profilePayload = buildProfilePayload(roleValue, userId, errorDiv);
  if (profilePayload === null) {
    return;
  }
  if (Object.keys(profilePayload).length) {
    userData.profile = profilePayload;
  }

  try {
    let response;
    if (userId) {
      // Update
      response = await fetch(`${API_BASE}/users/${userId}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(userData),
      });
    } else {
      // Create
      if (!userData.user.password) {
        errorDiv.textContent = "Password is required for new users";
        errorDiv.classList.remove("hidden");
        return;
      }
      response = await fetch(`${API_BASE}/users`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(userData),
      });
    }

    const data = await response.json();
    if (response.ok && data.success) {
      closeModal();
      showMessage(
        userId ? "User updated successfully" : "User created successfully",
        "success"
      );
      loadUsers();
    } else {
      let errorMessage = data.message || "Operation failed";
      if (data.errors && Array.isArray(data.errors)) {
        const errorDetails = data.errors
          .map((err) => err.msg || err.message)
          .join("\n");
        errorMessage = `${errorMessage}\n${errorDetails}`;
      }
      errorDiv.textContent = errorMessage;
      errorDiv.classList.remove("hidden");
    }
  } catch (error) {
    errorDiv.textContent = `Error: ${error.message}`;
    errorDiv.classList.remove("hidden");
  }
});

let userToDelete = null;

function deleteUser(userId) {
  const user = users.find((u) => u.user_id === userId);
  if (!user) return;

  userToDelete = userId;

  document.getElementById(
    "deleteUserName"
  ).textContent = `${user.first_name} ${user.last_name}`;
  document.getElementById("deleteUserEmail").textContent = user.email;
  document.getElementById("deleteUserRole").textContent = user.role;

  document.getElementById("deleteModal").classList.remove("hidden");
}

function closeDeleteModal() {
  document.getElementById("deleteModal").classList.add("hidden");
  userToDelete = null;
}

async function confirmDeleteUser() {
  if (!userToDelete) return;

  const userId = userToDelete;
  closeDeleteModal();

  try {
    const response = await fetch(`${API_BASE}/users/${userId}`, {
      method: "DELETE",
    });

    const data = await response.json();
    if (response.ok && data.success) {
      showMessage("User deleted successfully", "success");
      loadUsers();
    } else {
      showMessage(data.message || "Failed to delete user", "error");
    }
  } catch (error) {
    showMessage(`Error: ${error.message}`, "error");
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

document.getElementById("role").addEventListener("change", () =>
  handleRoleFields(Boolean(document.getElementById("userId").value))
);

function handleRoleFields(isEditMode = false) {
  const role = document.getElementById("role").value;
  const patientFields = document.getElementById("patientFields");
  const doctorFields = document.getElementById("doctorFields");
  const pharmacyFields = document.getElementById("pharmacyFields");

  patientFields.classList.add("hidden");
  doctorFields.classList.add("hidden");
  pharmacyFields.classList.add("hidden");

  const setRequired = (id, required) => {
    const el = document.getElementById(id);
    if (el) el.required = required;
  };

  // reset required
  [
    "patientBirthDate",
    "doctorBirthDate",
    "doctorPRCLicense",
    "pharmacyName",
    "pharmacyLicense",
  ].forEach((id) => setRequired(id, false));

  const isCreate = !isEditMode;

  if (role === "PATIENT") {
    patientFields.classList.remove("hidden");
    setRequired("patientBirthDate", isCreate);
  } else if (role === "DOCTOR") {
    doctorFields.classList.remove("hidden");
    setRequired("doctorBirthDate", isCreate);
    setRequired("doctorPRCLicense", isCreate);
  } else if (role === "PHARMACY") {
    pharmacyFields.classList.remove("hidden");
    setRequired("pharmacyName", isCreate);
    setRequired("pharmacyLicense", isCreate);
  }
}

function resetRoleFields() {
  [
    "patientBirthDate",
    "doctorBirthDate",
    "doctorPRCLicense",
    "doctorSpecialization",
    "doctorClinicName",
    "pharmacyName",
    "pharmacyLicense",
    "pharmacyOpenTime",
    "pharmacyCloseTime",
    "pharmacyDatesOpen",
  ].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });

  ["doctorIsVerified", "pharmacyIsVerified"].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.checked = false;
  });
}

function buildProfilePayload(role, userId, errorDiv) {
  const profile = {};
  const isEdit = Boolean(userId);

  const showError = (message) => {
    errorDiv.textContent = message;
    errorDiv.classList.remove("hidden");
  };

  if (role === "PATIENT") {
    const birthDate = document.getElementById("patientBirthDate").value;
    if (!isEdit && !birthDate) {
      showError("Birth date is required for patients");
      return null;
    }
    if (birthDate) profile.birth_date = birthDate;
  } else if (role === "DOCTOR") {
    const birthDate = document.getElementById("doctorBirthDate").value;
    const license = document
      .getElementById("doctorPRCLicense")
      .value.trim();
    const specialization = document
      .getElementById("doctorSpecialization")
      .value.trim();
    const clinic = document
      .getElementById("doctorClinicName")
      .value.trim();
    const isVerified = document.getElementById("doctorIsVerified").checked
      ? 1
      : 0;

    if (!isEdit && !birthDate) {
      showError("Birth date is required for doctors");
      return null;
    }
    if (!isEdit && !license) {
      showError("PRC license is required for doctors");
      return null;
    }

    if (birthDate) profile.birth_date = birthDate;
    if (license) profile.prc_license = license;
    if (specialization) profile.specialization = specialization;
    if (clinic) profile.clinic_name = clinic;
    profile.isVerified = isVerified;
  } else if (role === "PHARMACY") {
    const name = document.getElementById("pharmacyName").value.trim();
    const license = document.getElementById("pharmacyLicense").value.trim();
    const openTime = document.getElementById("pharmacyOpenTime").value;
    const closeTime = document.getElementById("pharmacyCloseTime").value;
    const datesOpen = document
      .getElementById("pharmacyDatesOpen")
      .value.trim();
    const isVerified = document.getElementById("pharmacyIsVerified").checked
      ? 1
      : 0;

    if (!isEdit && !name) {
      showError("Pharmacy name is required");
      return null;
    }
    if (!isEdit && !license) {
      showError("Pharmacy license is required");
      return null;
    }

    if (name) profile.pharmacy_name = name;
    if (license) profile.phar_license = license;
    if (openTime) profile.open_time = openTime;
    if (closeTime) profile.close_time = closeTime;
    if (datesOpen) profile.dates_open = datesOpen;
    profile.isVerified = isVerified;
  }

  return profile;
}

loadUsers();
