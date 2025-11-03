<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../../../public/assets/css/doctor.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <h1>Prescription Tracking System</h1>
            <ul class="nav-links">
                <li><a href="DoctorDashboard.php" class="active">Dashboard</a></li>
                <li><a href="PatientManagement.php">Patients</a></li>
                <li><a href="PrescriptionManagement.php">Prescriptions</a></li>
                <li><a href="DoctorProfile.php">Profile</a></li>
                <li><a href="../../../public/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard">
        <section>
            <h2>Welcome, Doctor</h2>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Patients</h3>
                    <p id="total-patients">--</p>
                </div>
                <div class="stat-card">
                    <h3>Active Prescriptions</h3>
                    <p id="active-prescriptions">--</p>
                </div>
                <div class="stat-card">
                    <h3>Pending Requests</h3>
                    <p id="pending-requests">--</p>
                </div>
                <div class="stat-card">
                    <h3>Completed Consultations</h3>
                    <p id="completed-consultations">--</p>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Activities</h3>
                <div id="activity-log">
                    <p>Loading activity data...</p>
                </div>
            </div>
        </section>
    </main>

    <script src="../../../public/assets/js/doctor/dashboard.js"></script>
</body>
</html>
