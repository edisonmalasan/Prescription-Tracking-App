<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../../public/assets/css/admin.css">
</head>
<body>
    <header>
        <nav>
            <h1>Prescription Tracking System</h1>
            <ul>
                <li><a href="DoctorDashboard.php">Dashboard</a></li>
                <li><a href="DoctorProfile.php">Patient Management</a></li>
                <li><a href="PatientManagement.php">Prescriptions</a></li>
                <li><a href="DoctorProfile.php">Profile</a></li>
                <li><a href="../../../public/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="dashboard">
            <h2>Doctor Dashboard</h2>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Patients</h3>
                    <p id="total-patients">-</p>
                </div>
                <div class="stat-card">
                    <h3>Active Prescriptions</h3>
                    <p id="total-prescriptions">-</p>
                </div>
                <div class="stat-card">
                    <h3>This Week</h3>
                    <p id="total-week">-</p>
                </div>
                <div class="stat-card">
                    <h3>This Month</h3>
                    <p id="total-month">-</p>
                </div>
            </div>

        <table id="patient-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Medications</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="patient-tbody">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
    </main>
    
    <script src="../../../public/assets/js/doctor/dashboard.js"></script>
</body>
</html>


