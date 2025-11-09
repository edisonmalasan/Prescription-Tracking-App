const API_BASE = "../../src/api";

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
}

async function loadProfile() {
  try {
    const res = await fetchJSON(`${API_BASE}/doctorRoutes.php?action=profile`);
    if (res.success && res.profile) {
      const p = res.profile;

      // --- VIEW INFO ---
      document.getElementById("profile-name").textContent = `Dr. ${p.first_name || ''} ${p.last_name || ''}`;
      document.getElementById("profile-special").textContent = `Specialization: ${p.specialization || '—'}`;
      document.getElementById("profile-prc").textContent = `PRC License: ${p.prc_license || '—'}`;
      document.getElementById("profile-contact").textContent = `Contact: ${p.contactno || '—'}`;
      document.getElementById("profile-email").textContent = `Email: ${p.email || '—'}`;
      document.getElementById("profile-clinic").textContent = `Clinic: ${p.clinic_name || '—'}`;
      document.getElementById("profile-address").textContent = `Address: ${p.address || '—'}`;

      // --- FORM FIELDS ---
      document.getElementById("first_name").value = p.first_name ?? "";
      document.getElementById("last_name").value = p.last_name ?? "";
      document.getElementById("contactno").value = p.contactno ?? "";
      document.getElementById("email").value = p.email ?? "";
      document.getElementById("prc_license").value = p.prc_license ?? "";
      document.getElementById("specialization").value = p.specialization ?? "";
      document.getElementById("clinic_name").value = p.clinic_name ?? "";
      document.getElementById("address").value = p.address ?? "";
    } else {
      console.error("Invalid response:", res);
    }
  } catch (err) {
    console.error("Error loading profile", err);
  }
}

function toggleEditMode(isEditing) {
  const viewDiv = document.getElementById("profile-view");
  const form = document.getElementById("profile-form");
  const editBtn = document.getElementById("edit-profile-btn");

  const inputs = document.querySelectorAll("#profile-form input");
  inputs.forEach(i => i.disabled = !isEditing);

  if (isEditing) {
    viewDiv.style.display = "none";
    form.style.display = "block";
    editBtn.style.display = "none";
  } else {
    viewDiv.style.display = "block";
    form.style.display = "none";
    editBtn.style.display = "inline-block";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const editBtn = document.getElementById("edit-profile-btn");
  const cancelBtn = document.getElementById("cancel-profile");
  const form = document.getElementById("profile-form");

  loadProfile();
  toggleEditMode(false);

  editBtn.addEventListener("click", () => {
    toggleEditMode(true);
  });

  cancelBtn.addEventListener("click", (e) => {
    e.preventDefault();
    toggleEditMode(false);
    loadProfile();
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
      first_name: document.getElementById("first_name").value,
      last_name: document.getElementById("last_name").value,
      contactno: document.getElementById("contactno").value,
      email: document.getElementById("email").value,
      prc_license: document.getElementById("prc_license").value,
      specialization: document.getElementById("specialization").value,
      clinic_name: document.getElementById("clinic_name").value,
      address: document.getElementById("address").value
    };

    try {
      const res = await fetchJSON(`${API_BASE}/doctorRoutes.php?action=profile`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      if (res.success) {
        alert("Profile updated successfully!");
        toggleEditMode(false);
        loadProfile();
      } else {
        alert("Failed to update profile");
      }
    } catch (err) {
      console.error("Error updating profile", err);
      alert("Server error while updating profile");
    }
  });
});
