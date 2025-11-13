console.log("Pharmacy Profile JS loaded");

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

// DOM references
const nameEl = document.getElementById("profile-name");
const emailEl = document.getElementById("profile-email");
const contactEl = document.getElementById("profile-contact");
const addressEl = document.getElementById("profile-address");
const hoursEl = document.getElementById("profile-hours");

const form = document.getElementById("profile-form");
const editBtn = document.getElementById("edit-profile-btn");
const cancelBtn = document.getElementById("cancel-profile");

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));

  if (!user || user.role !== "PHARMACY") {
    window.location.href = "../../../public/login.html";
    return;
  }

  try {
    await loadPharmacyProfile(user.user_id);
  } catch (err) {
    console.error("Error loading pharmacy profile:", err);
    alert("Failed to load pharmacy data.");
  }

  editBtn.addEventListener("click", enableEditMode);
  cancelBtn.addEventListener("click", cancelEdit);
  form.addEventListener("submit", saveProfile);
});

async function loadPharmacyProfile(pharmacyId) {
  const res = await api.get(`pharmacyRoutes.php?action=profile&user_id=${pharmacyId}`);
  console.log("[DEBUG] Pharmacy profile response:", res);

  const p = res.profile ?? res.pharmacy ?? res.data ?? null;
  if (!p) throw new Error("Failed to load pharmacy profile");

  nameEl.textContent = `${p.first_name ?? ""} ${p.last_name ?? ""}`.trim() || "—";
  emailEl.textContent = p.email ?? "—";
  contactEl.textContent = `Contact: ${p.contact_number ?? p.contactno ?? "—"}`;
  addressEl.textContent = `Address: ${p.address ?? "—"}`;

  const openTime = p.open_time ? formatTime(p.open_time) : "—";
  const closeTime = p.close_time ? formatTime(p.close_time) : "—";
  hoursEl.textContent = `Operating Hours: ${openTime} - ${closeTime}`;

  form.querySelector("#pharmacy_name").value = p.pharmacy_name ?? "";
  form.querySelector("#email").value = p.email ?? "";
  form.querySelector("#contact_number").value = p.contact_number ?? p.contactno ?? "";
  form.querySelector("#address").value = p.address ?? "";
  form.querySelector("#open_time").value = p.open_time ?? "";
  form.querySelector("#close_time").value = p.close_time ?? "";

  toggleForm(false);
}

function toggleForm(editMode) {
  form.querySelectorAll("input").forEach((input) => (input.disabled = !editMode));
  form.style.display = editMode ? "block" : "none";
  editBtn.style.display = editMode ? "none" : "inline-block";
}

function enableEditMode() {
  toggleForm(true);
}

function cancelEdit() {
  toggleForm(false);
}

async function saveProfile(e) {
  e.preventDefault();

  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user) return alert("Session expired. Please log in again.");

  const updated = {
    pharmacy_name: document.getElementById("pharmacy_name").value.trim(),
    email: document.getElementById("email").value.trim(),
    contact_number: document.getElementById("contact_number").value.trim(),
    address: document.getElementById("address").value.trim(),
    open_time: document.getElementById("open_time").value,
    close_time: document.getElementById("close_time").value,
  };

  try {
    const res = await api.put(`pharmacyRoutes.php?action=profile&user_id=${user.user_id}`, updated);
    console.log("[DEBUG] Update response:", res);

    if (res.success) {
      alert("Profile updated successfully!");
      await loadPharmacyProfile(user.user_id);
      toggleForm(false);
    } else {
      console.error("Update failed:", res);
      alert(res.error || "Failed to update profile.");
    }
  } catch (err) {
    console.error("Error updating profile:", err);
    alert("Server error while saving profile.");
  }
}

function formatTime(timeStr) {
  if (!timeStr) return "";
  const [h, m] = timeStr.split(":");
  const hours = parseInt(h, 10);
  const suffix = hours >= 12 ? "PM" : "AM";
  const display = ((hours + 11) % 12 + 1) + ":" + m;
  return `${display} ${suffix}`;
}
