<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../../../public/assets/css/patient.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <h1>Prescription Tracking System</h1>
            <ul class="nav-links">
                <li><a href="PatientDashboard.php" class="active">Dashboard</a></li>
                <li><a href="MyPrescription.php">My Prescriptions</a></li>
                <li><a href="PatientProfile.php">Profile</a></li>
                <li><a href="../../../public/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard">
        <section>
            <h2>Welcome, <?php echo "Juan Dela Cruz"; // Replace with actual user data ?></h2>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Active Prescriptions</h3>
                    <p id="active-prescriptions">5</p>
                </div>
                <div class="stat-card">
                    <h3>My Doctors</h3>
                    <p id="total-doctors">2</p>
                </div>
                <div class="stat-card">
                    <h3>Completed Prescriptions</h3>
                    <p id="completed-prescriptions">3</p>
                </div>
                <div class="stat-card">
                    <h3>Next Appointment</h3>
                    <p id="next-appointment">--</p>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Prescriptions</h3>
                <div id="prescription-log">
                    <?php 
                    // This will be populated with actual prescription data
                    ?>
                    <div class="prescription-item">
                        <p>Amoxicillin 500mg</p>
                        <span class="status active">Active</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../../../public/assets/js/patient/dashboard.js"></script>
</body>
</html>
