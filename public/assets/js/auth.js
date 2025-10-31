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
      messageElement.style.color = isError ? "red" : "green";
      messageElement.style.fontWeight = "bold";
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

<<<<<<< HEAD
    // Prepare data for API call
=======
    // data for api call
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
    const formData = {
      first_name: firstName,
      last_name: lastName,
      email: email,
      password: password,
      role: role,
      contactno: phoneNumber,
    };

<<<<<<< HEAD
    // Add role-specific fields
=======
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
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
      formData.phar_license = document.getElementById("pharmacyLicense").value;
      formData.open_time = document.getElementById("openTime").value;
      formData.close_time = document.getElementById("closeTime").value;
    }

    console.log("Sending registration data:", formData);

    try {
      const response = await fetch(
<<<<<<< HEAD
        "../../../src/api/authRoutes.php?action=register",
=======
        "../src/api/authRoutes.php?action=register",
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
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

      if (response.ok) {
        showMessage(
          "registrationMessage",
          "Registration successful! Redirecting to your dashboard...",
          false
        );

<<<<<<< HEAD
        // After registration, consider the user logged in and store their basic info.
=======
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
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
              redirectUrl = "../patient/dashboard.html";
              redirectUrl = "./views/patient/dashboard.html";
              break;
            case "DOCTOR":
              redirectUrl = "../doctor/dashboard.html";
              redirectUrl =
                "/Prescription-Tracking-App/public/views/doctor/dashboard.html";
              break;
            case "PHARMACY":
              redirectUrl = "../pharmacy/dashboard.html";
              redirectUrl =
                "/Prescription-Tracking-App/public/views/pharmacy/dashboard.html";
              break;
            case "ADMIN":
              redirectUrl = "../admin/dashboard.html";
              redirectUrl =
                "/Prescription-Tracking-App/public/views/admin/dashboard.html";
              break;
            default:
<<<<<<< HEAD
              // Default redirect to login page if role is unknown
=======
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
              redirectUrl = "login.html";
              redirectUrl =
                "/Prescription-Tracking-App/public/views/auth/login.html";
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

<<<<<<< HEAD
    // Prepare data for API call
=======
    // prepare input data for api call
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
    const formData = {
      email: email,
      password: password,
    };

    try {
<<<<<<< HEAD
      const response = await fetch(
        "/Prescription-Tracking-App/src/api/authRoutes.php?action=login",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(formData),
        }
      );

      const result = await response.json();

      if (response.ok) {
        showMessage("loginMessage", "Login successful! Redirecting...", false);

        // Store user data in localStorage or sessionStorage
        if (rememberMe) {
          localStorage.setItem("loggedInUser", JSON.stringify(result.user));
        } else {
          sessionStorage.setItem("loggedInUser", JSON.stringify(result.user));
        }

        // Redirect to the appropriate dashboard based on the role
=======
      const response = await fetch("../src/api/authRoutes.php?action=login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      const result = await response.json();

      if (response.ok) {
        showMessage("loginMessage", "Login successful! Redirecting...", false);

        // Store user data in sessionStorage
        if (rememberMe) {
          sessionStorage.setItem("loggedInUser", JSON.stringify(result.user));
        }

        // redirect depends on role
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
        setTimeout(() => {
          const role = result.user.role;
          let redirectUrl;

          switch (role) {
            case "PATIENT":
              redirectUrl = "./views/patient/dashboard.html";
              break;
            case "DOCTOR":
              redirectUrl = "./views/doctor/dashboard.html";
              break;
            case "PHARMACY":
              redirectUrl = "./views/pharmacy/dashboard.html";
              break;
            case "ADMIN":
              redirectUrl = "./views/admin/dashboard.html";
              break;
            default:
              redirectUrl = "./index.html";
              break;
          }
          window.location.href = redirectUrl;
        }, 1500);
      } else {
        showMessage("loginMessage", result.error || "Login failed!", true);
      }
    } catch (error) {
      showMessage("loginMessage", "An error occurred during login!", true);
    }
  }
});
<<<<<<< HEAD

// Export functions for use in other scripts
async function registerUser(userData) {
  console.log("registerUser called with:", userData);

  const response = await fetch(
    "../../../src/api/authRoutes.php?action=register",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(userData),
    }
  );

  return await response.json();
}

async function loginUser(credentials) {
  const response = await fetch("../../../src/api/authRoutes.php?action=login", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(credentials),
  });

  return await response.json();
}
=======
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
