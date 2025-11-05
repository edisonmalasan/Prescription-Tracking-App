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
                <li><a href="AdminDashboard.php">Dashboard</a></li>
                <li><a href="UserManagement.php">Users</a></li>
                <li><a href="DatabaseManagement.php">Database</a></li>
                <li><a href="../../../public/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="dashboard">
            <h2>Admin Dashboard</h2>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p id="total-users">-</p>
                </div>
                <div class="stat-card">
                    <h3>Doctors</h3>
                    <p id="total-doctors">-</p>
                </div>
                <div class="stat-card">
                    <h3>Patients</h3>
                    <p id="total-patients">-</p>
                </div>
                <div class="stat-card">
                    <h3>Pharmacies</h3>
                    <p id="total-pharmacies">-</p>
                </div>
            </div>
            
            <div class="patients-table">
                <h3>Overview</h3>
                <div id="system-stats">
                    <p>...</p>
                </div>
            </div>
        </section>
    </main>
    
    <script src="../../../public/assets/js/admin/dashboard.js"></script>
</body>
</html>


