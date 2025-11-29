<!DOCTYPE html>
<html lang="en">
<head>
<script>
    async function registerUser(event){
        event.preventDefault();
        let form = document.forms['registerForm'];
        let userData = {
            firstname: form['first_name'].value.trim(),
            middlename: form['middle_name'].value.trim(),
            lastname: form['last_name'].value.trim(),
            email: form['email'].value.trim(),
            mobile: form['phone'].value.trim(),
            password: form['password'].value.trim(),
            confirm_password: form['confirm_password'].value.trim(),
        };
        let response = await fetch("api/Register_api.php", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        let result = await response.json();
        alert(result.message);
        if (result.success) window.location.href = "login.php";
    }
</script>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form name="registerForm" onsubmit="registerUser(event)">

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name:</label>
                <input type="text" id="middle_name" name="middle_name" placeholder="Enter your middle name">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <div class="form-group">
                <button type="submit">Register</button>
            </div>
        </form>
        <p>Already have an account? <a href="Login.php">Login here</a></p>

    </div>
</body>
</html>