<?php

return [
    // public pages
    'home' => '/public/index.html',
    'login' => '/public/login.html',
    'register' => '/public/register.html',
    
    // doctor routes
    'doctor/dashboard' => '/src/views/doctor/DoctorDashboard.php',
    'doctor/profile' => '/src/views/doctor/DoctorProfile.php',
    'doctor/patients' => '/src/views/doctor/PatientManagement.php',
    'doctor/prescriptions' => '/src/views/doctor/PrescriptionManagement.php',
    
    // patient routes
    'patient/dashboard' => '/src/views/patient/PatientDashboard.php',
    'patient/profile' => '/src/views/patient/PatientProfile.php',
    'patient/prescriptions' => '/src/views/patient/MyPrescription.php',
    
    // pharma routes
    'pharmacy/dashboard' => '/src/views/pharmacy/PharmacyDashboard.php',
    'pharmacy/profile' => '/src/views/pharmacy/PharmacyProfile.php',
    
    // API routes (handled by router.php automatically)
    'api/auth' => '/src/api/authRoutes.php',
    'api/doctor' => '/src/api/doctorRoutes.php',
    'api/patient' => '/src/api/patientRoutes.php',
    'api/pharmacy' => '/src/api/pharmacyRoutes.php',
    'api/prescription' => '/src/api/prescriptionRoutes.php',
    'api/drug' => '/src/api/drugRoutes.php',
];




