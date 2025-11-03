<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management</title>
    <link rel="stylesheet" href="../../../public/assets/css/doctor.css">
</head>
<body>
<header>
    <nav>
        <h1>Prescription Tracking System</h1>
        <ul>
            <li><a href="DoctorDashboard.php">Dashboard</a></li>
            <li><a href="PatientManagement.php" class="active">Patients</a></li>
            <li><a href="PrescriptionManagement.php">Prescriptions</a></li>
            <li><a href="DoctorProfile.php">Profile</a></li>
            <li><a href="../../../public/login.html">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="patient-management">
        <h2>Patient Management</h2>

        <div class="search-container">
            <input type="text" id="searchPatient" placeholder="Search patient by name...">
        </div>

        <table class="patient-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Medications</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="patientList">
                <tr>
                    <td>Maria Santos</td>
                    <td>29</td>
                    <td>Lisinopril</td>
                    <td><span class="status active">Active</span></td>
                    <td><button class="view-btn" onclick="viewPatient('Maria Santos', 29, '09123456789', 'Lisinopril', '10mg daily', 'No known allergies', '2024-10-02, 2024-11-10')">View</button></td>
                </tr>
                <tr>
                    <td>Juan Dela Cruz</td>
                    <td>35</td>
                    <td>Metformin</td>
                    <td><span class="status pending">Pending</span></td>
                    <td><button class="view-btn" onclick="viewPatient('Juan Dela Cruz', 35, '09998887777', 'Metformin', '500mg twice daily', 'Penicillin', '2024-08-15, 2024-09-20')">View</button></td>
                </tr>
                <tr>
                    <td>Ana Lopez</td>
                    <td>42</td>
                    <td>Atorvastatin</td>
                    <td><span class="status closed">Closed</span></td>
                    <td><button class="view-btn" onclick="viewPatient('Ana Lopez', 42, '09876543210', 'Atorvastatin', '20mg nightly', 'Sulfa drugs', '2024-05-03, 2024-06-12')">View</button></td>
                </tr>
            </tbody>
        </table>
    </section>
</main>

<!-- Patient Info Modal -->
<div id="patientModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Patient Information</h3>
        <div class="modal-details">
            <p><strong>Full Name:</strong> <span id="modalName"></span></p>
            <p><strong>Age:</strong> <span id="modalAge"></span></p>
            <p><strong>Contact Number:</strong> <span id="modalContact"></span></p>
            <p><strong>Active Prescription:</strong> <span id="modalMedication"></span></p>
            <p><strong>Dosage & Frequency:</strong> <span id="modalDosage"></span></p>
            <p><strong>Allergies:</strong> <span id="modalAllergies"></span></p>
            <p><strong>Visit History:</strong> <span id="modalVisits"></span></p>
        </div>
    </div>
</div>

<script src="../../../public/assets/js/doctor/dashboard.js"></script>
</body>
</html>
