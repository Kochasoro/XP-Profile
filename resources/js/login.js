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
    showAlert(result.message, result.success ? 'success' : 'error');
    if (result.success) window.location.href = "login.php";
}

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
    showAlert(result.message, result.success ? 'success' : 'error');

    if (result.success) {
        setTimeout(() => {
            showAlert("Logging in...", 'info', true); 

            setTimeout(() => {
                    window.location.href = "/Jsonproj/portfolio";
            }, 2000); 
        }, 1000); 
    }

} catch (error) {
    showAlert("Something went wrong. Please try again later.", 'error');
    console.error("Login error:", error);
}
}

function showAlert(message, type = 'success', showSpinner = false) {
clearAlerts();  

const alertBox = document.createElement('div');
const alertMessage = document.createElement('span');
const alertSpinner = document.createElement('div');

const typeClasses = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    info: 'bg-blue-500',
};

const spinnerColors = {
    success: '#48bb78', 
    error: '#e53e3e',  
    info: '#3182ce',   
};

alertBox.className = `alert p-4 text-white font-medium rounded-lg shadow-lg max-w-xs w-full ${typeClasses[type]} fixed left-1/2 transform -translate-x-1/2 mb-2 flex items-center`;

alertMessage.textContent = message;

alertSpinner.className = "spinner hidden inline-block ml-2";
if (showSpinner) {
    alertSpinner.classList.remove('hidden');
    alertSpinner.style.borderTopColor = spinnerColors[type]; 
}

alertBox.appendChild(alertMessage);
alertBox.appendChild(alertSpinner);

document.body.appendChild(alertBox);

setTimeout(() => {
    alertBox.classList.add('hidden');
    document.body.removeChild(alertBox);
}, 4000);
}

function clearAlerts() {
const alertBox = document.getElementById('alertBox');
alertBox.classList.add('hidden');
}