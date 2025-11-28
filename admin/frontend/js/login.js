const API_BASE =
  (typeof getAdminApiBase === "function" && getAdminApiBase()) ||
  window.ADMIN_API_BASE ||
  "http://localhost:4000/api/admin";

document.getElementById("loginForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const errorDiv = document.getElementById("errorMessage");
  const submitBtn = e.target.querySelector('button[type="submit"]');

  errorDiv.classList.add("hidden");
  submitBtn.disabled = true;
  submitBtn.textContent = "Signing in...";

  try {
    const response = await fetch(`${API_BASE}/auth/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email, password }),
    });

    const data = await response.json();

    if (response.ok && data.success) {
      sessionStorage.setItem("admin", JSON.stringify(data.admin));
      window.location.href = "dashboard.html";
    } else {
      errorDiv.textContent =
        data.message || "Invalid credentials. Please try again.";
      errorDiv.classList.remove("hidden");
    }
  } catch (error) {
    errorDiv.textContent =
      "Network error. Please check if the server is running.";
    errorDiv.classList.remove("hidden");
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = "Sign In";
  }
});
