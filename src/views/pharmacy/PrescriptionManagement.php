<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Management</title>
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
                    <a href="PrescriptionManagement.php">Prescription Management</a>
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
                My Patients
            </header>
            
            <div class="search-bar">
                <input type="text" placeholder="Search Patients">
                <button>Search</button>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Medications</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Juan Dela Cruz</td>
                        <td>45</td>
                        <td>Amoxicillin</td>
                        <td>Active</td>
                        <td><button class="action-button">View</button></td>
                    </tr>
                    <tr>
                        <td>Maria Garcia</td>
                        <td>32</td>
                        <td>Amoxicillin</td>
                        <td>Active</td>
                        <td><button class="action-button">View</button></td>
                    </tr>
                    <tr>
                        <td>Pedro Santos</td>
                        <td>40</td>
                        <td>Losartan</td>
                        <td>Pending</td>
                        <td><button class="action-button">View</button></td>
                    </tr>
                    <tr>
                        <td>Ana Reyes</td>
                        <td>58</td>
                        <td>Metformin</td>
                        <td>Closed</td>
                        <td><button class="action-button">View</button></td>
                    </tr>
                    <tr>
                        <td>Mark Santos</td>
                        <td>50</td>
                        <td>Losartan</td>
                        <td>Active</td>
                        <td><button class="action-button">View</button></td>
                    </tr>
                </tbody>
            </table>

        </main>
    </div>
</body>
</html>