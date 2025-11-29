function confirmLogout() {
    document.getElementById("logoutModal").style.display = "block";
}

function closeLogoutModal() {
    document.getElementById("logoutModal").style.display = "none";
}

function logout() {
    window.location.href = "logout.php";
}
