<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title>
    <link rel="stylesheet" href="../../public/assets/css/pharmacy.css">
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
                Sales Management
                <div class="sales-actions">
                    <button class="btn">Record Sale</button>
                </div>
            </header>
            
            <div class="recent-transactions">
                <h2 class="section-title">Recent Transactions</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Medicine</th>