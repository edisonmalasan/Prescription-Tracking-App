// public/assets/js/auth.js

document.addEventListener("DOMContentLoaded", () => {
  // Check if we are on the registration page
  const registrationForm = document.getElementById("registrationForm");
  if (registrationForm) {
    registrationForm.addEventListener("submit", handleRegistration);
  }

  // Check if we are on the login page
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", handleLogin);
  }

  // --- Utility Functions ---
  function showMessage(elementId, message, isError = false) {
    const messageElement = document.getElementById(elementId);
    if (messageElement) {
      messageElement.textContent = message;
      messageElement.className = `px-4 py-3 rounded-lg text-sm font-medium ${
        isError
          ? "bg-red-50 border border-red-200 text-red-700"
          : "bg-green-50 border border-green-200 text-green-700"
      }`;
      messageElement.classList.remove("hidden");
    }
  }

  // --- Registration Logic ---
  async function handleRegistration(event) {
    event.preventDefault(); // Prevent default form submission

    const email = document.getElementById("regEmail").value;
    const password = document.getElementById("regPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const phoneNumber = document.getElementById("phoneNumber").value;
    const role = document.getElementById("regRole").value;
    const agreeTerms = document.getElementById("agreeTerms").checked;

    // Basic client-side validation
    if (password !== confirmPassword) {
      showMessage("registrationMessage", "Passwords do not match!", true);
      return;
    }

    if (!agreeTerms) {
      showMessage(
        "registrationMessage",
        "You must agree to the Terms of Service and Privacy Policy.",
        true
      );
      return;
    }

    // data for api call
    const formData = {
      first_name: firstName,
      last_name: lastName,
      email: email,
      password: password,
      role: role,
      contactno: phoneNumber,
    };

    if (role === "PATIENT") {
      formData.birth_date = document.getElementById("patientBirthDate").value;
    } else if (role === "DOCTOR") {
      formData.birth_date = document.getElementById("doctorBirthDate").value;
      formData.specialization = document.getElementById(
        "doctorSpecialization"
      ).value;
      formData.prc_license = document.getElementById("doctorLicense").value;
      formData.clinic_name = document.getElementById("clinicName").value;
    } else if (role === "PHARMACY") {
      formData.pharmacy_name = document.getElementById("pharmacyName").value;
      formData.address = document.getElementById("address").value;
      formData.operating_hours =
        document.getElementById("operatingHours").value;
    }

    console.log("Sending registration data:", formData);

    try {
      const response = await fetch(
        "../src/api/authRoutes.php?action=register",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(formData),
        }
      );

      console.log("Response status:", response.status);
      console.log("Response headers:", [...response.headers.entries()]);

      const result = await response.json();
      console.log("Response data:", result);

      if (response.ok && result.success) {
        showMessage(
          "registrationMessage",
          "Registration successful! Redirecting to your dashboard...",
          false
        );

        const user = {
          email: formData.email,
          role: formData.role,
          user_id: result.user_id,
        };
        sessionStorage.setItem("loggedInUser", JSON.stringify(user));

        // Redirect to the appropriate dashboard based on the role
        setTimeout(() => {
          const role = formData.role;
          let redirectUrl;

          switch (role) {
            case "PATIENT":
              redirectUrl = "../src/views/patient/PatientDashboard.php";
              break;
            case "DOCTOR":
              redirectUrl = "../src/views/doctor/DoctorDashboard.php";
              break;
            case "PHARMACY":
              redirectUrl = "../src/views/pharmacy/PharmacyDashboard.php";
              break;
            default:
              redirectUrl = "login.html";
              break;
          }
          window.location.href = redirectUrl;
        }, 2000);
      } else {
        showMessage(
          "registrationMessage",
          result.error || "Registration failed!",
          true
        );
      }
    } catch (error) {
      console.error("Registration error:", error);
      showMessage(
        "registrationMessage",
        "An error occurred during registration: " + error.message,
        true
      );
    }
  }

  // --- Login Logic ---
  async function handleLogin(event) {
    event.preventDefault(); // Prevent default form submission

    const email = document.getElementById("loginEmail").value;
    const password = document.getElementById("loginPassword").value;
    const rememberMe = document.getElementById("rememberMe").checked;

    // prepare input data for api call
    const formData = {
      email: email,
      password: password,
    };

    try {
      const response = await fetch("../src/api/authRoutes.php?action=login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      const result = await response.json();

      if (response.ok && result.success && result.user) {
        showMessage("loginMessage", "Login successful! Redirecting...", false);

        const role = result.user.role;

        // If admin, redirect to admin frontend and store as admin session
        if (role === "ADMIN") {
          // Store admin data in sessionStorage (admin frontend expects 'admin' key)
          sessionStorage.setItem("admin", JSON.stringify(result.user));
          // Also store as loggedInUser for consistency
          sessionStorage.setItem("loggedInUser", JSON.stringify(result.user));

          setTimeout(() => {
            window.location.href = "/admin/frontend/dashboard.html";
          }, 1500);
          return;
        }

        // For non-admin users, store user data in sessionStorage
        sessionStorage.setItem("loggedInUser", JSON.stringify(result.user));

        // redirect depends on role
        setTimeout(() => {
          let redirectUrl;

          switch (role) {
            case "PATIENT":
              redirectUrl = "/views/patient/PatientDashboard.php";
              break;
            case "DOCTOR":
              redirectUrl = "/views/doctor/DoctorDashboard.php";
              break;
            case "PHARMACY":
              redirectUrl = "/views/pharmacy/PharmacyDashboard.php";
              break;
            default:
              redirectUrl = "/home";
              break;
          }
          window.location.href = redirectUrl;
        }, 1500);
      } else {
        showMessage("loginMessage", result.error || "Login failed!", true);
      }
    } catch (error) {
      showMessage(
        "loginMessage",
        "An error occurred during login: " + error.message,
        true
      );
    }
  }
});
