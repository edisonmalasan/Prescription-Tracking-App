<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management</title>
    <link rel="stylesheet" href="../../../public/assets/css/admin.css">
</head>
<body>
    <header>
        <nav>
            <h1>Prescription Tracking System</h1>
            <ul>
                <li><a href="AdminDashboard.php">Dashboard</a></li>
                <li><a href="UserManagement.php">Users</a></li>
                <li><a href="DatabaseManagement.php">Database</a></li>
                <li><a href="../../../public/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="database-management">
            <h2>Database Management</h2>
            
            <div class="database-stats">
                <div class="stat-card">
                    <h3>Total Records</h3>
                    <p id="total-records">-</p>
                </div>
                <div class="stat-card">
                    <h3>Prescriptions</h3>
                    <p id="total-prescriptions">-</p>
                </div>
                <div class="stat-card">
                    <h3>Drugs</h3>
                    <p id="total-drugs">-</p>
                </div>
                <div class="stat-card">
                    <h3>Medical Records</h3>
                    <p id="total-medical-records">-</p>
                </div>
            </div>
            
            <div class="database-actions">
                <h3>Database Operations</h3>
                <button id="refresh-stats-btn">Refresh Statistics</button>
            </div>
        </section>
    </main>

    
    <script src="../../../public/assets/js/admin/database-management.js"></script>
</body>
</html>


