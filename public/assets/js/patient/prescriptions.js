// public/assets/js/patient/prescriptions.js

// Fetch and render prescriptions for the logged-in patient.
// - Uses existing backend: ../src/api/prescriptionRoutes.php?action=by-patient&patient_id=...
// - Renders active/completed tabs, supports searching/filtering, and shows details modal

(function () {
  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);

    function getUserId() {
      try {
        const stored = sessionStorage.getItem('loggedInUser');
        if (stored) {
          const u = JSON.parse(stored);
          if (u && (u.user_id || u.userId || u.id)) return u.user_id || u.userId || u.id;
        }
      } catch (e) {}
      if (params.has('user_id')) return params.get('user_id');
      return null;
    }

    const userId = getUserId();
    if (!userId) {
      console.error('No patient user_id found.');
      return;
    }

    const apiBase = '../src/api/prescriptionRoutes.php';
    const fetchUrl = apiBase + '?action=by-patient&patient_id=' + encodeURIComponent(userId);

    const table = document.querySelector('#active-prescriptions');
    const tbody = table ? table.querySelector('tbody') : null;
    const searchInput = document.querySelector('.search-prescriptions');
    const filterSelect = document.querySelector('.filter-by-date');
    const tabButtons = document.querySelectorAll('.tab-buttons .tab');
    const templateModal = document.getElementById('prescription-modal');

    let prescriptions = [];
    let activeList = [];
    let completedList = [];
    let currentTab = 'active';

    function renderCounts() {
      const activeBtn = document.querySelector('.tab-buttons .tab[data-tab="active"]');
      const completedBtn = document.querySelector('.tab-buttons .tab[data-tab="completed"]');
      if (activeBtn) activeBtn.textContent = `Active (${activeList.length})`;
      if (completedBtn) completedBtn.textContent = `Completed (${completedList.length})`;
    }

    function renderTable(list) {
      if (!tbody) return;
      tbody.innerHTML = '';
      if (!list || list.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="6" class="no-data">No prescriptions found</td>';
        tbody.appendChild(tr);
        return;
      }

      list.forEach((p) => {
        const tr = document.createElement('tr');
        const status = (p.status || '').toString();
        tr.innerHTML = `
          <td>${p.prescription_id}</td>
          <td>${p.prescribing_doctor || '-'}</td>
          <td>${p.record_id || '-'}</td>
          <td>${p.prescription_date || '-'}</td>
          <td>${status}</td>
          <td>
            <button class="btn view-details" data-id="${p.prescription_id}">Details</button>
          </td>
        `;
        tbody.appendChild(tr);
      });

      // attach detail buttons
      tbody.querySelectorAll('.view-details').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const id = btn.getAttribute('data-id');
          showPrescriptionDetails(id);
        });
      });
    }

    function applySearchAndFilter(list) {
      const q = (searchInput && searchInput.value || '').trim().toLowerCase();
      const dateFilter = (filterSelect && filterSelect.value) || 'all';

      const cutoff = (() => {
        const now = new Date();
        switch (dateFilter) {
          case 'month':
            const m = new Date(); m.setMonth(now.getMonth() - 1); return m;
          case '3months':
            const t = new Date(); t.setMonth(now.getMonth() - 3); return t;
          case '6months':
            const s = new Date(); s.setMonth(now.getMonth() - 6); return s;
          case 'year':
            const y = new Date(); y.setFullYear(now.getFullYear() - 1); return y;
          default:
            return null;
        }
      })();

      return list.filter((p) => {
        if (cutoff) {
          const pd = p.prescription_date ? new Date(p.prescription_date) : null;
          if (!pd || pd < cutoff) return false;
        }
        if (!q) return true;
        // search fields: id, date, status, doctor id
        if ((p.prescription_id && p.prescription_id.toString().includes(q)) ||
            (p.prescription_date && p.prescription_date.toLowerCase().includes(q)) ||
            (p.status && p.status.toLowerCase().includes(q)) ||
            (p.prescribing_doctor && p.prescribing_doctor.toString().includes(q))) {
          return true;
        }
        return false;
      });
    }

    function setupTabs() {
      tabButtons.forEach((b) => {
        b.addEventListener('click', () => {
          tabButtons.forEach(t => t.classList.remove('active'));
          b.classList.add('active');
          currentTab = b.getAttribute('data-tab');
          refreshView();
        });
      });
    }

    function refreshView() {
      const list = currentTab === 'active' ? activeList : completedList;
      const shown = applySearchAndFilter(list);
      renderTable(shown);
    }

    function showPrescriptionDetails(prescriptionId) {
      // fetch details
      const url = apiBase + '?action=details&prescription_id=' + encodeURIComponent(prescriptionId);
      fetch(url)
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
          if (!data || !data.success) throw new Error(data.error || 'Failed to load details');
          const details = data.details || [];
          openModalWithDetails(prescriptionId, details);
        })
        .catch(err => {
          console.error('Failed to fetch prescription details', err);
          alert('Failed to load prescription details');
        });
    }

    function openModalWithDetails(prescriptionId, details) {
      const tpl = templateModal;
      if (!tpl) {
        // fallback simple alert
        alert('Prescription ' + prescriptionId + '\n' + JSON.stringify(details, null, 2));
        return;
      }

      const frag = tpl.content.cloneNode(true);
      const modal = frag.querySelector('.prescription-detail-modal');
      const body = frag.querySelector('.modal-body');
      const closeBtns = frag.querySelectorAll('.close-modal');

      body.innerHTML = '';

      if (!details || details.length === 0) {
        body.innerHTML = '<p>No details available</p>';
      } else {
        const ul = document.createElement('div');
        ul.className = 'prescription-details-list';
        details.forEach(d => {
          const item = document.createElement('div');
          item.className = 'prescription-detail-item';
          item.innerHTML = `
            <p><strong>Drug ID:</strong> ${d.drug_id || '-'} | <strong>Dosage:</strong> ${d.dosage || '-'} | <strong>Frequency:</strong> ${d.frequency || '-'}</p>
            <p><strong>Duration:</strong> ${d.duration || '-'} | <strong>Refills:</strong> ${d.refills || 0}</p>
            <p><em>${d.special_instructions || ''}</em></p>
          `;
          ul.appendChild(item);
        });
        body.appendChild(ul);
      }

      document.body.appendChild(modal);

      function closeModal() {
        modal.remove();
      }

      closeBtns.forEach(cb => cb.addEventListener('click', closeModal));
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });
    }

    // initial fetch
    fetch(fetchUrl, { credentials: 'same-origin' })
      .then(res => {
        if (!res.ok) return res.json().then(j => Promise.reject(j));
        return res.json();
      })
      .then(data => {
        if (!data || !data.success) throw new Error(data.error || 'Invalid response');
        prescriptions = data.prescriptions || [];
        // partition
        activeList = prescriptions.filter(p => !(p.status && p.status.toLowerCase() === 'completed'));
        completedList = prescriptions.filter(p => (p.status && p.status.toLowerCase() === 'completed'));
        renderCounts();
        setupTabs();
        refreshView();

        // wire search and filter
        if (searchInput) {
          searchInput.addEventListener('input', () => refreshView());
        }
        if (filterSelect) {
          filterSelect.addEventListener('change', () => refreshView());
        }
      })
      .catch(err => {
        console.error('Failed to load prescriptions', err);
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="no-data">Error loading prescriptions</td></tr>';
      });
  });
})();

