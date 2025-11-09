document.addEventListener("DOMContentLoaded", () => {
  if (!USER_ID) return;

  fetch(`../../controllers/PrescriptionController.php?action=by-patient&patient_id=${USER_ID}`)
    .then(res => res.json())
    .then(data => {
      if (!Array.isArray(data)) return console.error(data.error);

      document.getElementById("stat-total-prescriptions").textContent = data.length;
      document.getElementById("stat-active-prescriptions").textContent = data.filter(p => p.status === 'active').length;

      const tbody = document.querySelector("#recent-prescriptions tbody");
      tbody.innerHTML = '';
      data.slice(0,5).forEach(p => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${p.prescription_id}</td>
          <td>${p.prescribing_doctor}</td>
          <td>${p.prescription_date}</td>
          <td>${p.status}</td>
        `;
        row.addEventListener("click", () => {
          window.location.href = `MyPrescription.php?prescription_id=${p.prescription_id}`;
        });
        tbody.appendChild(row);
      });
    })
    .catch(err => console.error(err));
});
