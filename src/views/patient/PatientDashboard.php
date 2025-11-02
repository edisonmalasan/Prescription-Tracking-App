<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../../../public/assets/css/patient.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2 class="sidebar-header">Patient</h2>
            <ul class="nav-list">
                <li class="nav-item active" data-page="PatientDashboard.php">Dashboard</li>
                <li class="nav-item" data-page="MyPrescriptions.php">My Prescriptions</li>
                <li class="nav-item" data-page="PatientProfile.php">Profile</li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="header">
                <?php 
                // Later you can replace this with actual user data
                echo "Juan Dela Cruz"; 
                ?>
            </header>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Prescriptions</h3>
                    <div class="stat-value">
                        <?php 
                        // Replace with actual count later
                        echo "5"; 
                        ?>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>My Doctors</h3>
                    <div class="stat-value">
                        <?php 
                        // Replace with actual count later
                        echo "2"; 
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="prescription-section">
                <div class="prescription-title">Your Active Prescriptions</div>
                <?php 
                // This is a placeholder. Later you can loop through actual prescriptions
                $samplePrescription = "Amoxicillin 500mg";
                ?>
                <div class="prescription-item">
                    <?php echo $samplePrescription; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="../../../public/assets/js/patient/dashboard.js"></script>
</body>
</html>

