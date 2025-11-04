<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard</title>
    <link rel="stylesheet" href="../../../public/assets/css/pharmacy.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2 class="sidebar-header">Pharmacy</h2>
            <ul class="nav-list">
                <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) === 'Dashboard.php') echo ' active'; ?>">
                    <a href="Dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) === 'PrescriptionManagement.php') echo ' active'; ?>">
                    <a href="prescriptionManagement.php">Prescription Management</a>
                </li>
                <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) === 'Prescriptions.php') echo ' active'; ?>">
                    <a href="Prescriptions.php">Prescriptions</a>
                </li>
                <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) === 'Sales.php') echo ' active'; ?>">
                    <a href="Sales.php">Sales</a>
                </li>
                <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) === 'Profile.php') echo ' active'; ?>">
                    <a href="Profile.php">Profile</a>
                </li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="header">
                Dashboard
            </header>
            
            <div class="dashboard-overview">
                <div class="card">
                    <h3>Pending Rx</h3>
                    <p>12</p> <!-- Example dynamic data -->
                </div>
                <div class="card">
                    <h3>Filled Today</h3>
                    <p>8</p> <!-- Example dynamic data -->
                </div>
                <div class="card">
                    <h3>Active Patients</h3>
                    <p>45</p> <!-- Example dynamic data -->
                </div>
                <div class="card">
                    <h3>Total Sales (Today)</h3>
                    <p>$1250</p> <!-- Example dynamic data -->
                </div>
            </div>

            <div class="pending-prescriptions">
                <h2 class="section-title">Pending Prescriptions (12)</h2>
                <div class="prescriptions-list">
                    <div class="prescription-item">
                        <div>
                            <p><strong>Juan Dela Cruz</strong></p>
                            <p>Amoxicillin 500mg: 30 caps</p>
                            <p>15 mins ago</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-view">View</button>
                            <button class="btn btn-process">Process</button>
                        </div>
                    </div>
                    <div class="prescription-item">
                        <div>
                            <p><strong>Maria Garcia</strong></p>
                            <p>Losartan 50mg: 15 caps</p>
                            <p>18 mins ago</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-view">View</button>
                            <button class="btn btn-process">Process</button>
                        </div>
                    </div>
                    <div class="prescription-item">
                        <div>
                            <p><strong>Pedro Santos</strong></p>
                            <p>Metformin 500mg: 60 caps</p>
                            <p>60 mins ago</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-view">View</button>
                            <button class="btn btn-process">Process</button>
                        </div>
                    </div>
                </div>
                <a href="Prescriptions.php" class="view-all-button">View All</a>
            </div>

            <div class="recent-activities">
                <h2 class="section-title">Recent Sales</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Medicine</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Oct, 17 2025</td>
                            <td>Juan Dela Cruz</td>
                            <td>2</td>
                            <td>Amoxicillin</td>
                        </tr>
                        <tr>
                            <td>Oct, 17 2025</td>
                            <td>Maria Garcia</td>
                            <td>2</td>
                            <td>Losartan</td>
                        </tr>
                        <tr>
                            <td>Oct, 17 2025</td>
                            <td>Pedro Santos</td>
                            <td>3</td>
                            <td>Metformin</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>
</html>