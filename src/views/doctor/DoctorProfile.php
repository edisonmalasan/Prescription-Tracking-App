<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="../../../public/assets/css/doctor.css">
</head>
<body>
<header>
    <nav>
        <h1>Prescription Tracking System</h1>
        <ul>
            <li><a href="DoctorDashboard.php">Dashboard</a></li>
            <li><a href="PatientManagement.php">Patients</a></li>
            <li><a href="PrescriptionManagement.php">Prescriptions</a></li>
            <li><a href="DoctorProfile.php" class="active">Profile</a></li>
            <li><a href="../../../public/login.html">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="doctor-profile">
        <h2>Doctor Profile</h2>

        <div class="profile-container">
            <!-- Profile Picture -->
            <div class="profile-picture">
                <img src="../../../public/assets/images/default-doctor.png" alt="Doctor Picture" id="doctorImage">
                <input type="file" id="uploadImage" accept="image/*" style="display:none;">
                <button id="changePhotoBtn" class="edit-btn">Change Photo</button>
            </div>

            <!-- Profile Info -->
            <div class="profile-info">
                <h3 id="doctorName">Dr. Adrian Ramos</h3>
                <p><strong>Specialty:</strong> <span id="doctorSpecialty">Cardiology</span></p>
                <p><strong>PRC License:</strong> <span id="doctorLicense">1234567</span></p>
                <button id="editProfileBtn" class="edit-btn">Edit Profile</button>
            </div>
        </div>

        <!-- Editable Profile Form -->
        <div id="editProfileForm" class="hidden edit-profile-form">
            <h3>Edit Profile Details</h3>
            <form>
                <div class="form-grid">
                    <div>
                        <label>First Name</label>
                        <input type="text" id="firstName" value="Adrian">
                    </div>
                    <div>
                        <label>Last Name</label>
                        <input type="text" id="lastName" value="Ramos">
                    </div>
                    <div>
                        <label>Contact Number</label>
                        <input type="text" id="contactNumber" value="09123456789">
                    </div>
                    <div>
                        <label>PRC License Number</label>
                        <input type="text" id="licenseNumber" value="1234567">
                    </div>
                    <div>
                        <label>Specialization</label>
                        <input type="text" id="specialization" value="Cardiology">
                    </div>
                    <div>
                        <label>Hospital / Clinic</label>
                        <input type="text" id="hospitalClinic" value="Saint Louis Hospital">
                    </div>
                    <div class="full-width">
                        <label>Hospital / Clinic Address</label>
                        <textarea id="clinicAddress" rows="2">Baguio City, Philippines</textarea>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" id="saveProfileBtn" class="create-btn">Save</button>
                    <button type="button" id="cancelProfileBtn" class="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </section>
</main>

<script src="../../../public/assets/js/doctor/dashboard.js"></script>
</body>
</html>
