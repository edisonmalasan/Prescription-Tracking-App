<?php

// Change working directory so relative requires inside repository files resolve
chdir(__DIR__ . '/../src/repositories');
require_once 'UserRepository.php';
require_once 'DoctorRepository.php';
require_once 'PatientRepository.php';

date_default_timezone_set('UTC');

try {
	$userRepo = new UserRepository();

	$now = date('Y-m-d H:i:s');

	// User data for Marco Polozki
	$userData = [
		'last_name' => 'Polozki',
		'first_name' => 'Marco',
		'role' => 'doctor',
		'email' => 'marco222@gmail.com',
		'contactno' => '999',
		'pass_hash' => 'pass500',
		'address' => 'pizza tower',
		'created_at' => $now,
		'updated_at' => $now
	];

	echo "Checking for existing user...\n";
	$existing = $userRepo->findByEmail($userData['email']);
	if ($existing) {
		$userId = $existing['user_id'];
		echo "User already exists with ID: $userId\n";
	} else {
		echo "Creating user...\n";
		$userId = $userRepo->create($userData);
		echo "User created with ID: $userId\n";
	}

	// Prepare doctor data compatible with DoctorRepository::create
	$doctor = new stdClass();
	$doctor->user_id = $userId;
	$doctor->prc_license = 'prc999';
	$doctor->specialization = 'Cardiologist';
	$doctor->verified = 1; // repository accepts 'verified' or 'isVerified'
	$doctor->isVerified = 1;
	$doctor->birth_date = '1987-01-20';
	$doctor->clinic_name = 'StayAlive';

	$doctorRepo = new DoctorRepository();
	// Check if doctor record already exists for this user
	$existingDoctor = $doctorRepo->findByUserId($userId);
	if ($existingDoctor) {
		echo "Doctor record already exists for user_id $userId\n";
		$doctorId = $userId;
	} else {
		echo "Creating doctor record...\n";
		$doctorId = $doctorRepo->create($doctor);
		echo "Doctor record created with ID (user_id): $doctorId\n";
	}

	// Fetch back to verify
	$fetchedUser = $userRepo->findById($userId);
	$fetchedDoctor = $doctorRepo->findByUserId($userId);

	echo "\nInserted user row:\n";
	print_r($fetchedUser);

	echo "\nInserted doctor row:\n";
	print_r($fetchedDoctor);

	// --- Create a sample patient: Sam P. Le ---
	echo "\n--- Creating sample patient Sam P. Le ---\n";
	$patientRepo = new PatientRepository();

	$patientData = new stdClass();
	$patientData->first_name = 'Sam P.';
	$patientData->last_name = 'Le';
	$patientData->role = 'patient';
	$patientData->email = 'sam.le@example.com';
	$patientData->contactno = '0900000000';
	$patientData->pass_hash = 'pass123';
	$patientData->address = '123 Test Lane';
	$patientData->birth_date = '2000-01-01';

	// check existing
	$existingPatientUser = $userRepo->findByEmail($patientData->email);
	if ($existingPatientUser) {
		$patientUserId = $existingPatientUser['user_id'];
		echo "Patient user already exists with ID: $patientUserId\n";
	} else {
		echo "Creating patient...\n";
		$patientUserId = $patientRepo->create($patientData);
		if ($patientUserId) {
			echo "Patient user created with ID: $patientUserId\n";
		} else {
			echo "Failed to create patient.\n";
		}
	}

	$fetchedPatientUser = $userRepo->findById($patientUserId);
	$fetchedPatient = $patientRepo->findByUserId($patientUserId);

	echo "\nInserted patient user row:\n";
	print_r($fetchedPatientUser);

	echo "\nInserted patient row:\n";
	print_r($fetchedPatient);

	echo "\nDone.\n";

} catch (Exception $e) {
	echo "Error: " . $e->getMessage() . "\n";
	exit(1);
}

?>

