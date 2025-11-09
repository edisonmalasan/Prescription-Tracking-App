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
      document.getElementById("profile-name").textContent = `Dr. ${p.first_name} ${p.last_name}`;
      document.getElementById("profile-special").textContent = p.specialization ?? '';
      document.getElementById("profile-prc").textContent = `PRC License: ${p.prc_license ?? '—'}`;

      // fill form
      document.getElementById("first_name").value = p.first_name ?? '';
      document.getElementById("last_name").value = p.last_name ?? '';
      document.getElementById("contactno").value = p.contactno ?? '';
      document.getElementById("email").value = p.email ?? '';
      document.getElementById("prc_license").value = p.prc_license ?? '';
      document.getElementById("specialization").value = p.specialization ?? '';
      document.getElementById("clinic_name").value = p.clinic_name ?? '';
      document.getElementById("address").value = p.address ?? '';
    }
  } catch (err) {
    console.error("Error loading profile", err);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  loadProfile();

  document.getElementById("edit-profile-btn").addEventListener("click", () => {
    // scroll to form and focus
    document.getElementById("first_name").focus();
  });

  document.getElementById("cancel-profile").addEventListener("click", (e) => {
    e.preventDefault();
    loadProfile();
  });

  document.getElementById("profile-form").addEventListener("submit", async (e) => {
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
      // doctorRoutes.php uses PUT profile update at action=profile
      const res = await fetchJSON(`${API_BASE}/doctorRoutes.php?action=profile`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      if (res.success) {
        alert("Profile updated");
        loadProfile();
      } else {
        console.error("Update response", res);
        alert("Failed to update profile");
      }
    } catch (err) {
      console.error("Error updating profile", err);
      alert("Server error while updating profile");
    }
  });
});
