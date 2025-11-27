const API_BASE = "http://localhost:4000/api/admin";

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
  const userData = {
    user: {
      first_name: document.getElementById("firstName").value,
      last_name: document.getElementById("lastName").value,
      email: document.getElementById("email").value,
      role: document.getElementById("role").value,
      contactno: document.getElementById("contactno").value,
      address: document.getElementById("address").value,
    },
    profile: {},
  };

  if (document.getElementById("password").value) {
    userData.user.password = document.getElementById("password").value;
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
      errorDiv.textContent = data.message || "Operation failed";
      errorDiv.classList.remove("hidden");
    }
  } catch (error) {
    errorDiv.textContent = `Error: ${error.message}`;
    errorDiv.classList.remove("hidden");
  }
});

async function deleteUser(userId) {
  if (
    !confirm(
      "Are you sure you want to delete this user? This action cannot be undone."
    )
  ) {
    return;
  }

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

// Load users on page load
loadUsers();
