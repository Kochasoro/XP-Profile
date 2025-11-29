<!DOCTYPE html>
<html lang="en">
<head>
<script>
async function loginUser(event) {
    event.preventDefault();
    let form = document.forms['loginForm'];
    let loginData = {
        email: form['email'].value.trim(),
        password: form['password'].value.trim(),
    };

    try {
        let response = await fetch("api/login_api.php", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(loginData)
        });

        let result = await response.json();
        alert(result.message);
        
        if (result.success) {
            if (result.role === 'admin') {
                // window.location.href = "admin/dashboard.php";
                window.location.href = "/dashboard";

            } else {
                window.location.href = "/portfolio";
            }
        }
    } catch (error) {
        alert("Something went wrong. Please try again later.");
        console.error("Login error:", error);
    }
}

</script>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_GET['error']) && $_GET['error'] == 'security') {
            echo "<p style='color: red;'>sESSION SECURITY VIOLATION DETECTED. PLEASE LOG IN AGAIN</p>";
        } elseif (isset($_GET['timeout'])) {
            echo "<p style='color: red;'>Session timed out. Please log in again.</p>";
        }
        ?>
        <h1>Login</h1>
        <form id="loginForm" onsubmit="loginUser(event)">
            <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
            <button type="submit">Login</button>
            </div>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <p><a href="forgot_password.php">Forgot Password?</a></p>


</body>
</html>