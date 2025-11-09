document.addEventListener("DOMContentLoaded", () => {
  if (!USER_ID) return;

  let url = `../../controllers/PrescriptionController.php?action=by-patient&patient_id=${USER_ID}`;
  if (PRESCRIPTION_ID) url += `&prescription_id=${PRESCRIPTION_ID}`;

  fetch(url)
    .then(res => res.json())
    .then(prescriptions => {
      if (!Array.isArray(prescriptions)) return console.error(prescriptions.error);

      const tbody = document.querySelector("#itemsTable tbody");
      tbody.innerHTML = '';

      prescriptions.forEach(p => {
        fetch(`../../controllers/PrescriptionController.php?action=details&prescription_id=${p.prescription_id}`)
          .then(res => res.json())
          .then(details => {
            details.forEach(d => {
              const row = document.createElement("tr");
              row.innerHTML = `
                <td>${d.drug_id}</td>
                <td>${d.dosage}</td>
                <td>${d.frequency}</td>
                <td>${d.duration}</td>
                <td>${d.refills}</td>
                <td>${d.special_instructions}</td>
              `;
              tbody.appendChild(row);
            });
          });
      });
    });
});
