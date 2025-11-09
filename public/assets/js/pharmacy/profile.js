const API_BASE = "../../src/api";

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
}

let pharmacyProfile = {};

async function loadProfile() {
  try {
    const res = await fetchJSON(`${API_BASE}/pharmacyRoutes.php?action=profile`);

    if (res.success && res.profile) {
      pharmacyProfile = res.profile;
      displayProfile();
    } else {
      console.error("Failed to load profile");
    }
  } catch (err) {
    console.error("Error loading profile:", err);
  }
}

function displayProfile() {
  document.getElementById("profile-name").textContent = pharmacyProfile.pharmacy_name;
  document.getElementById("profile-email").textContent = pharmacyProfile.email;
  document.getElementById("profile-contact").textContent = `Contact: ${pharmacyProfile.contact_number ?? '—'}`;
  document.getElementById("profile-address").textContent = `Address: ${pharmacyProfile.address ?? '—'}`;
  document.getElementById("profile-hours").textContent = `Operating Hours: ${pharmacyProfile.operating_hours ?? '—'}`;
}

function showEditForm() {
  document.getElementById("pharmacy_name").value = pharmacyProfile.pharmacy_name;
  document.getElementById("email").value = pharmacyProfile.email;
  document.getElementById("contact_number").value = pharmacyProfile.contact_number;
  document.getElementById("address").value = pharmacyProfile.address;
  document.getElementById("operating_hours").value = pharmacyProfile.operating_hours;

  document.querySelector(".profile-form").style.display = "block";
}

function hideEditForm() {
  document.querySelector(".profile-form").style.display = "none";
}

async function saveProfile(event) {
  event.preventDefault();

  const updatedProfile = {
    pharmacy_name: document.getElementById("pharmacy_name").value,
    email: document.getElementById("email").value,
    contact_number: document.getElementById("contact_number").value,
    address: document.getElementById("address").value,
    operating_hours: document.getElementById("operating_hours").value,
  };

  try {
    const res = await fetchJSON(`${API_BASE}/pharmacyRoutes.php?action=profile`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(updatedProfile),
    });

    if (res.success) {
      hideEditForm();
      loadProfile();
    } else {
      alert(`Error: ${res.error}`);
    }
  } catch (err) {
    console.error("Error saving profile:", err);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  loadProfile();

  document.getElementById("edit-profile-btn").addEventListener("click", showEditForm);
  document.getElementById("cancel-profile").addEventListener("click", hideEditForm);
  document.getElementById("profile-form").addEventListener("submit", saveProfile);
});
