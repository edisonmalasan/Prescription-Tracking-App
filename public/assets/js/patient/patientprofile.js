document.addEventListener("DOMContentLoaded", () => {
  if (!USER_ID) return;

  fetch(`../../controllers/PatientController.php?action=profile`)
    .then(res => res.json())
    .then(data => {
      if (data.error) return console.error(data.error);

      document.getElementById("patient-name").textContent = `Welcome, ${data.first_name} ${data.last_name}`;
      document.getElementById("patient-id").textContent = `Patient ID: ${data.user_id}`;
      document.getElementById("first-name").textContent = data.first_name;
      document.getElementById("last-name").textContent = data.last_name;
      document.getElementById("email").textContent = data.email;
      document.getElementById("contactno").textContent = data.contactno;
      document.getElementById("address").textContent = data.address;
      document.getElementById("birth-date").textContent = data.birth_date;
      document.getElementById("age").textContent = data.birth_date ? new Date().getFullYear() - new Date(data.birth_date).getFullYear() : '—';

      if (data.medical_records && data.medical_records.length > 0) {
        const record = data.medical_records[0];
        document.getElementById("allergies").textContent = record.allergies || '—';
      }
    })
    .catch(err => console.error(err));
});
