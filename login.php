<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="resources/css/styling.css">
    <title>Login form</title>
    <link rel="stylesheet" href="resources/css/output.css">
    <script src="resources/js/login.js"></script>


</head>
<body>    


<input type="checkbox" id="chk" aria-hidden="true" checked>
<div id="alertBox"  class="alert hidden fixed  left-1/2 transform -translate-x-1/2 p-4 text-white font-medium rounded-lg shadow-lg max-w-xs w-full">
    <span id="alertMessage"></span>
    <div id="alertSpinner" class="hidden inline-block ml-2 spinner"></div>
</div>

<div class="suisui">
    <img src="resources/images/sui.png" alt="Suichan">
</div>

<div class="main">
    <label for="chk" class="floating-signup-label" aria-hidden="true">Sign Up</label>

    <div class="signup">
        <form name="registerForm" onsubmit="registerUser(event)">
            <input type="text" name="first_name" placeholder="First name" required>
            <input type="text" name="middle_name" placeholder="Middle name">
            <input type="text" name="last_name" placeholder="Last name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="phone" placeholder="Phone number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm password" required>
            <button type="submit">Sign Up</button>
        </form>
    </div>

    <div class="login">
        <form id="loginForm" onsubmit="loginUser(event)">
            <label for="chk" aria-hidden="true">Login</label>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</div>


</body>
</html>