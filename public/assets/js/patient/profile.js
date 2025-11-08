// public/assets/js/patient/profile.js

// This script fetches the patient profile from the existing backend API
// and populates the DOM fields in src/views/patient/PatientProfile.php.
// It also adds a "Save Changes" button that sends a PUT request to update
// the profile. This file intentionally does not change any backend code.

(function () {
  document.addEventListener('DOMContentLoaded', () => {
    const profileNameEl = document.getElementById('profile-name');
    const profileEmailEl = document.getElementById('profile-email');

    const fields = {
      firstName: document.getElementById('firstName'),
      lastName: document.getElementById('lastName'),
      dateOfBirth: document.getElementById('dateOfBirth'),
      email: document.getElementById('email'),
      contactNumber: document.getElementById('contactNumber'),
      address: document.getElementById('address'),
      city: document.getElementById('city'),
      province: document.getElementById('province'),
      allergies: document.getElementById('allergies')
    };

    // helper: get user_id from sessionStorage.loggedInUser or URL param
    function getUserId() {
      try {
        const stored = sessionStorage.getItem('loggedInUser');
        if (stored) {
          const u = JSON.parse(stored);
          if (u && (u.user_id || u.userId || u.id)) {
            return u.user_id || u.userId || u.id;
          }
        }
      } catch (e) {
        // ignore parse errors
      }

      // fallback: try URL param user_id
      const params = new URLSearchParams(window.location.search);
      if (params.has('user_id')) return params.get('user_id');

      return null;
    }

    const userId = getUserId();
    if (!userId) {
      if (profileNameEl) profileNameEl.textContent = 'User not logged in';
      console.error('No user id found in sessionStorage or URL.');
      return;
    }

    // Build API URL (match existing frontend convention)
    const apiBase = '../src/api/patientRoutes.php';
    const profileUrl = apiBase + '?action=profile&user_id=' + encodeURIComponent(userId);

    // fetch and populate
    fetch(profileUrl, { credentials: 'same-origin' })
      .then(async (res) => {
        if (!res.ok) {
          const txt = await res.text();
          throw new Error('Failed to fetch profile: ' + res.status + ' ' + txt);
        }
        return res.json();
      })
      .then((data) => {
        if (!data || !data.success || !data.patient) {
          throw new Error(data.error || 'Invalid profile response');
        }

        const p = data.patient;
        if (profileNameEl) profileNameEl.textContent = (p.first_name || '') + ' ' + (p.last_name || '');
        if (profileEmailEl) profileEmailEl.textContent = p.email || '';

        // map known backend keys to form fields
        if (fields.firstName) fields.firstName.value = p.first_name || p.firstName || '';
        if (fields.lastName) fields.lastName.value = p.last_name || p.lastName || '';
        if (fields.dateOfBirth) fields.dateOfBirth.value = p.birth_date || p.dateOfBirth || '';
        if (fields.email) fields.email.value = p.email || '';
        if (fields.contactNumber) fields.contactNumber.value = p.contactno || p.contactNumber || '';
        if (fields.address) fields.address.value = p.address || '';
        if (fields.city) fields.city.value = p.city || '';
        if (fields.province) fields.province.value = p.province || '';
        if (fields.allergies) fields.allergies.value = p.allergies || '';

        addSaveButton();
      })
      .catch((err) => {
        console.error(err);
        if (profileNameEl) profileNameEl.textContent = 'Error loading profile';
      });

    function addSaveButton() {
      // only add once
      if (document.getElementById('save-profile-btn')) return;

      const formSection = document.querySelector('.form-section');
      if (!formSection) return;

      const btn = document.createElement('button');
      btn.id = 'save-profile-btn';
      btn.type = 'button';
      btn.textContent = 'Save Changes';
      btn.style.marginTop = '12px';
      btn.className = 'save-btn';

      btn.addEventListener('click', onSave);

      // append to formSection (after the form)
      const form = formSection.querySelector('.profile-form');
      if (form) {
        form.appendChild(btn);
      } else {
        formSection.appendChild(btn);
      }
    }

    async function onSave() {
      const payload = {};
      // note: backend PatientRepository expects fields like birth_date, contactno, address
      payload.first_name = fields.firstName ? fields.firstName.value : '';
      payload.last_name = fields.lastName ? fields.lastName.value : '';
      payload.birth_date = fields.dateOfBirth ? fields.dateOfBirth.value : '';
      payload.email = fields.email ? fields.email.value : '';
      payload.contactno = fields.contactNumber ? fields.contactNumber.value : '';
      payload.address = fields.address ? fields.address.value : '';
      // optional fields
      payload.city = fields.city ? fields.city.value : '';
      payload.province = fields.province ? fields.province.value : '';
      payload.allergies = fields.allergies ? fields.allergies.value : '';

      // The backend update endpoint expects user_id as query param and form data in body
      const updateUrl = apiBase + '?action=profile&user_id=' + encodeURIComponent(userId);

      try {
        const res = await fetch(updateUrl, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload),
          credentials: 'same-origin'
        });

        const result = await res.json();
        if (res.ok && result.success) {
          showMessage('Profile updated successfully', false);
        } else {
          showMessage(result.error || 'Failed to update profile', true);
        }
      } catch (err) {
        console.error('Update failed', err);
        showMessage('Network or server error while updating', true);
      }
    }

    function showMessage(text, isError) {
      let el = document.getElementById('profile-message');
      if (!el) {
        el = document.createElement('div');
        el.id = 'profile-message';
        el.style.marginTop = '8px';
        el.style.fontWeight = 'bold';
        const container = document.querySelector('.profile-management') || document.body;
        container.insertBefore(el, container.firstChild);
      }
      el.textContent = text;
      el.style.color = isError ? 'red' : 'green';

      // hide after 3s
      setTimeout(() => { el.textContent = ''; }, 3000);
    }
  });
})();

