document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", handleLogin);
  }

  // login
  async function handleLogin(event) {
    event.preventDefault();

    const email = document.getElementById("loginEmail").value;
    const password = document.getElementById("loginPassword").value;
    const rememberMe = document.getElementById("rememberMe").checked;

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
        const role = result.user.role;
        const isVerified =
          result.user.isVerified === 1 || result.user.isVerified === true;

        if ((role === "DOCTOR" || role === "PHARMACY") && !isVerified) {
          showVerificationModal();
          return;
        }

        showMessage("loginMessage", "Login successful! Redirecting...", false);

        if (role === "ADMIN") {
          sessionStorage.setItem("admin", JSON.stringify(result.user));
          sessionStorage.setItem("loggedInUser", JSON.stringify(result.user));

          setTimeout(() => {
            window.location.href = "/admin/frontend/dashboard.html";
          }, 1500);
          return;
        }

        sessionStorage.setItem("loggedInUser", JSON.stringify(result.user));

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
