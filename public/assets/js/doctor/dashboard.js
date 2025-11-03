// ===============================
// Doctor Dashboard + Prescription Management
// ===============================

// --- Dashboard Stats ---
document.addEventListener("DOMContentLoaded", () => {
    const totalPatients = document.getElementById("total-patients");
    const activePrescriptions = document.getElementById("active-prescriptions");
    const pendingPrescriptions = document.getElementById("pending-prescriptions");

    if (totalPatients) totalPatients.textContent = "36";
    if (activePrescriptions) activePrescriptions.textContent = "12";
    if (pendingPrescriptions) pendingPrescriptions.textContent = "4";
});

// --- Search Filter for Patients ---
const searchInput = document.getElementById('searchPatient');
const patientList = document.getElementById('patientList');

if (searchInput && patientList) {
    searchInput.addEventListener('keyup', () => {
        const filter = searchInput.value.toLowerCase();
        const rows = patientList.getElementsByTagName('tr');
        Array.from(rows).forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            row.style.display = name.includes(filter) ? '' : 'none';
        });
    });
}

// --- Modal Controls ---
function viewPatient(name, age, contact, medication, dosage, allergies, visits) {
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalAge').textContent = age;
    document.getElementById('modalContact').textContent = contact;
    document.getElementById('modalMedication').textContent = medication;
    document.getElementById('modalDosage').textContent = dosage;
    document.getElementById('modalAllergies').textContent = allergies;
    document.getElementById('modalVisits').textContent = visits;
    document.getElementById('patientModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('patientModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('patientModal');
    const addPatientModal = document.getElementById('addPatientModal');
    if (event.target === modal) modal.style.display = 'none';
    if (event.target === addPatientModal) addPatientModal.style.display = 'none';
};

// --- Sample Patient Data ---
const patientData = {
    "PT001": {
        name: "Maria Santos",
        age: 29,
        contact: "09123456789",
        allergies: "None",
        medications: "Amlodipine 5mg"
    },
    "PT002": {
        name: "Juan Dela Cruz",
        age: 35,
        contact: "09998887777",
        allergies: "Penicillin",
        medications: "Metformin 500mg"
    }
};

// --- Load Selected Patient ---
function loadPatientDetails() {
    const select = document.getElementById('selectPatient');
    const id = select.value;
    const patient = patientData[id];
    const details = document.getElementById('patientDetails');
    const prescription = document.getElementById('prescriptionDetails');

    if (patient) {
        document.getElementById('pFullName').innerText = patient.name;
        document.getElementById('pAge').innerText = patient.age;
        document.getElementById('pContact').innerText = patient.contact;
        document.getElementById('pAllergies').innerText = patient.allergies;
        document.getElementById('pMedications').innerText = patient.medications;

        details.classList.remove('hidden');
        prescription.classList.remove('hidden');
    } else {
        details.classList.add('hidden');
        prescription.classList.add('hidden');
    }
}

// --- Create / Cancel Prescription ---
const createForm = document.getElementById("createPrescriptionForm");
if (createForm) {
    const createBtn = document.getElementById("createPrescriptionBtn");
    const cancelBtn = document.getElementById("cancelPrescriptionBtn");

    createBtn?.addEventListener("click", () => {
        alert("✅ Prescription created successfully!");
        createForm.reset();
    });

    cancelBtn?.addEventListener("click", () => {
        createForm.reset();
    });
}

// --- Add Patient Modal Logic ---
const addPatientModal = document.getElementById("addPatientModal");
const addPatientBtn = document.getElementById("addPatientBtn");
const closeAddPatient = document.getElementById("closeAddPatient");
const cancelAddPatient = document.getElementById("cancelAddPatient");
const addPatientForm = document.getElementById("addPatientForm");

addPatientBtn?.addEventListener("click", () => addPatientModal.style.display = "block");
closeAddPatient?.addEventListener("click", () => addPatientModal.style.display = "none");
cancelAddPatient?.addEventListener("click", () => addPatientModal.style.display = "none");

addPatientForm?.addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("newPatientName").value;
    const age = document.getElementById("newPatientAge").value;
    const contact = document.getElementById("newPatientContact").value;
    const allergies = document.getElementById("newPatientAllergies").value;
    const meds = document.getElementById("newPatientMedications").value;

    const id = `PT${Math.floor(Math.random() * 1000).toString().padStart(3, "0")}`;
    patientData[id] = { name, age, contact, allergies, medications: meds };

    const select = document.getElementById("selectPatient");
    const newOption = document.createElement("option");
    newOption.value = id;
    newOption.textContent = name;
    select.appendChild(newOption);

    alert(`${name} has been added to the patient list!`);
    addPatientModal.style.display = "none";
    addPatientForm.reset();
});
// ===============================
// Doctor Profile Logic
// ===============================
document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.getElementById("editProfileBtn");
    const formCard = document.getElementById("editProfileForm");
    const cancelBtn = document.getElementById("cancelProfileBtn");
    const saveBtn = document.getElementById("saveProfileBtn");
    const changePhotoBtn = document.getElementById("changePhotoBtn");
    const uploadImage = document.getElementById("uploadImage");
    const doctorImage = document.getElementById("doctorImage");

    editBtn.addEventListener("click", () => {
        formCard.classList.remove("hidden");
        window.scrollTo({ top: formCard.offsetTop, behavior: "smooth" });
    });

    cancelBtn.addEventListener("click", () => {
        formCard.classList.add("hidden");
    });

    saveBtn.addEventListener("click", () => {
        alert("Profile updated successfully!");
        formCard.classList.add("hidden");
    });

    changePhotoBtn.addEventListener("click", () => uploadImage.click());

    uploadImage.addEventListener("change", function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => doctorImage.src = e.target.result;
            reader.readAsDataURL(file);
        }
    });
});

