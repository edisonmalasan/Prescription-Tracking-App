<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Management</title>
    <link rel="stylesheet" href="../../../public/assets/css/doctor.css">
</head>
<body>
<header>
    <nav>
        <h1>Prescription Tracking System</h1>
        <ul>
            <li><a href="DoctorDashboard.php">Dashboard</a></li>
            <li><a href="PatientManagement.php">Patients</a></li>
            <li><a href="PrescriptionManagement.php" class="active">Prescriptions</a></li>
            <li><a href="DoctorProfile.php">Profile</a></li>
            <li><a href="../../../public/login.html">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="prescription-management">
        <h2>Prescription Management</h2>

        <div class="prescription-form">
            <!-- Select + Add Patient -->
            <label for="selectPatient">Select Patient:</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <select id="selectPatient" onchange="loadPatientDetails()">
                    <option value="">-- Choose Patient --</option>
                    <option value="PT001">Maria Santos</option>
                    <option value="PT002">Juan Dela Cruz</option>
                </select>
                <button id="addPatientBtn" class="secondary-btn">+ Add Patient</button>
            </div>

            <!-- Patient Info -->
            <div id="patientDetails" class="patient-details hidden">
                <h3>Patient Details</h3>
                <p><strong>Full Name:</strong> <span id="pFullName"></span></p>
                <p><strong>Age:</strong> <span id="pAge"></span></p>
                <p><strong>Contact:</strong> <span id="pContact"></span></p>
                <p><strong>Allergies:</strong> <span id="pAllergies"></span></p>
                <p><strong>Current Medications:</strong> <span id="pMedications"></span></p>
            </div>

            <!-- Prescription Form -->
            <div id="prescriptionDetails" class="prescription-details hidden">
                <h3>Create Prescription</h3>

                <form id="createPrescriptionForm">
                    <label>Medication Name:</label>
                    <input type="text" id="medicationName" required>

                    <label>Dosage:</label>
                    <input type="text" id="dosage" required>

                    <label>Quantity:</label>
                    <input type="number" id="quantity" required>

                    <label>Frequency:</label>
                    <input type="text" id="frequency" placeholder="e.g. Twice a day" required>

                    <label>Duration:</label>
                    <input type="text" id="duration" placeholder="e.g. 7 days" required>

                    <label>Instructions:</label>
                    <textarea id="instructions" rows="3" placeholder="e.g. Take after meals" required></textarea>

                    <div class="btn-group">
                        <button type="button" class="create-btn" id="createPrescriptionBtn">Create Prescription</button>
                        <button type="reset" class="cancel-btn" id="cancelPrescriptionBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

  <!-- Add Patient Modal -->
        <div id="addPatientModal" class="modal">
            <div class="modal-content">
                <span id="closeAddPatient" class="close">&times;</span>
                <h2>Add New Patient</h2>
                <form id="addPatientForm">
                    <input type="text" id="newPatientName" placeholder="Full Name" required>
                    <input type="number" id="newPatientAge" placeholder="Age" required>
                    <input type="text" id="newPatientContact" placeholder="Contact Number" required>
                    <input type="text" id="newPatientAllergies" placeholder="Allergies" required>
                    <input type="text" id="newPatientMedications" placeholder="Current Medications" required>
                    <div class="button-row">
                        <button type="submit" class="create-btn">Add Patient</button>
                        <button type="button" id="cancelAddPatient" class="cancel-btn">Cancel</button>
                    </div>
        </form>
    </div>
</div>

<script src="../../../public/assets/js/doctor/dashboard.js"></script>
</body>
</html>
