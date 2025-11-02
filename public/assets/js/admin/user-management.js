const API_BASE = "../../src/api";

async function fetchUsers() {
  const response = await fetch(`${API_BASE}/adminRoutes.php?action=all-users`);
  const data = await response.json();

  if (data.success && data.users) {
    displayUsers(data.users);
  }
}

function displayUsers(users) {
  const tbody = document.getElementById("users-tbody");

  if (!users || users.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5">No users found</td></tr>';
    return;
  }

  tbody.innerHTML = users
    .map(
      (user) => `
        <tr>
            <td>${user.user_id}</td>
            <td>${user.first_name} ${user.last_name}</td>
            <td>${user.email || "N/A"}</td>
            <td>${user.role || "N/A"}</td>
            <td>
                <button onclick="editUser(${user.user_id})">Edit</button>
                <button onclick="deleteUser(${user.user_id})">Delete</button>
            </td>
        </tr>
    `
    )
    .join("");
}

function filterUsers() {
  const role = document.getElementById("role-filter").value;
  const searchTerm = document
    .getElementById("search-input")
    .value.toLowerCase();

  fetch(`${API_BASE}/adminRoutes.php?action=all-users`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.users) {
        let filtered = data.users;

        if (role) {
          filtered = filtered.filter((user) => user.role === role);
        }

        if (searchTerm) {
          filtered = filtered.filter((user) => {
            const name = `${user.first_name} ${user.last_name}`.toLowerCase();
            const email = (user.email || "").toLowerCase();
            return name.includes(searchTerm) || email.includes(searchTerm);
          });
        }

        displayUsers(filtered);
      }
    })
    .catch((error) => {
      console.error("Error filtering users:", error);
    });
}

function editUser(userId) {
  alert(`Edit user with ID: ${userId}`);
}

async function deleteUser(userId) {
  if (confirm("Are you sure you want to delete this user?")) {
    try {
      const response = await fetch(
        `${API_BASE}/adminRoutes.php?action=delete-user&user_id=${userId}`,
        {
          method: "DELETE",
        }
      );
      const data = await response.json();

      if (data.success) {
        alert("User deleted successfully");
        fetchUsers();
      } else {
        alert("Error deleting user: " + (data.error || "Unknown error"));
      }
    } catch (error) {
      console.error("Error deleting user:", error);
      alert("Error deleting user");
    }
  }
}

function showError(message) {
  const tbody = document.getElementById("users-tbody");
  tbody.innerHTML = `<tr><td colspan="5">${message}</td></tr>`;
}

document.addEventListener("DOMContentLoaded", () => {
  fetchUsers();

  document.getElementById("search-btn").addEventListener("click", filterUsers);
  document.getElementById("refresh-btn").addEventListener("click", fetchUsers);
  document
    .getElementById("role-filter")
    .addEventListener("change", filterUsers);
  document.getElementById("search-input").addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      filterUsers();
    }
  });
});
