// public/assets/js/patient/dashboard.js

// Populate the patient dashboard using existing backend endpoints.
// - Fetch prescriptions for the logged-in patient and compute stats.

(function () {
	document.addEventListener('DOMContentLoaded', () => {
		function getUserId() {
			try {
				const stored = sessionStorage.getItem('loggedInUser');
				if (stored) {
					const u = JSON.parse(stored);
					if (u && (u.user_id || u.userId || u.id)) return u.user_id || u.userId || u.id;
				}
			} catch (e) {}
			const params = new URLSearchParams(window.location.search);
			if (params.has('user_id')) return params.get('user_id');
			return null;
		}

		const userId = getUserId();
		if (!userId) {
			console.error('No user id found for dashboard');
			return;
		}

		const apiPrescriptions = '../src/api/prescriptionRoutes.php?action=by-patient&patient_id=' + encodeURIComponent(userId);

		const elActive = document.getElementById('active-prescriptions');
		const elCompleted = document.getElementById('completed-prescriptions');
		const elDoctors = document.getElementById('total-doctors');
		const elNext = document.getElementById('next-appointment');
		const elLog = document.getElementById('prescription-log');

		fetch(apiPrescriptions, { credentials: 'same-origin' })
			.then(res => res.ok ? res.json() : Promise.reject(res))
			.then(data => {
				if (!data || !data.success) throw new Error(data.error || 'Invalid response');
				const prescriptions = data.prescriptions || [];

				const completed = prescriptions.filter(p => (p.status && p.status.toLowerCase() === 'completed'));
				const active = prescriptions.filter(p => !(p.status && p.status.toLowerCase() === 'completed'));

				if (elActive) elActive.textContent = active.length;
				if (elCompleted) elCompleted.textContent = completed.length;

				// count unique doctors
				const doctors = new Set(prescriptions.map(p => p.prescribing_doctor).filter(Boolean));
				if (elDoctors) elDoctors.textContent = doctors.size;

				// next appointment: no appointment API available - keep placeholder
				if (elNext) elNext.textContent = '--';

				// recent prescriptions
				if (elLog) {
					elLog.innerHTML = '';
					const recent = prescriptions.sort((a,b) => new Date(b.prescription_date) - new Date(a.prescription_date)).slice(0,5);
					if (recent.length === 0) {
						elLog.innerHTML = '<p class="no-data">No recent prescriptions</p>';
					} else {
						recent.forEach(p => {
							const item = document.createElement('div');
							item.className = 'prescription-item';
							const statusClass = (p.status && p.status.toLowerCase() === 'completed') ? 'status completed' : 'status active';
							item.innerHTML = `<p>Prescription #${p.prescription_id} — ${p.prescription_date || '-'} </p><span class="${statusClass}">${p.status || ''}</span>`;
							elLog.appendChild(item);
						});
					}
				}
			})
			.catch(err => {
				console.error('Failed to load dashboard data', err);
			});
	});
})();
