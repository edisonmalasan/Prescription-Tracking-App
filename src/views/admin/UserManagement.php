<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
        <section class="user-management">
            <h2>User Management</h2>
            
            <div class="filters">
                <select id="role-filter">
                    <option value="">All Roles</option>
                    <option value="DOCTOR">Doctors</option>
                    <option value="PATIENT">Patients</option>
                    <option value="PHARMACY">Pharmacies</option>
                    <option value="ADMIN">Admins</option>
                </select>
                <input type="text" id="search-input" placeholder="Search users...">
                <button id="search-btn">Search</button>
                <button id="refresh-btn">Refresh</button>
            </div>
            
            <table id="users-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="users-tbody">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
    
    <script src="../../../public/assets/js/admin/user-management.js"></script>
</body>
</html>


