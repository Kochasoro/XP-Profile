let users = [];

function fetchUsers() {
    fetch('api/User_api.php')
        .then(response => response.json())
        .then(data => {
            users = data.users;
            loadUsers();
        })
        .catch(error => console.error("Failed to fetch users:", error));
}

function loadUsers() {
    const tbody = document.querySelector("#userTable tbody");
    tbody.innerHTML = "";
    users.forEach(user => {
        const isVerified = user.status === "Verified";
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${user.id}</td>
            <td>${user.firstname} ${user.middlename} ${user.lastname}</td>
            <td>${user.email}</td>
            <td>${user.status}</td>
            <td>
                ${
                    user.role === "admin"
                        ? "Admin"
                        : `<button 
                                class="${user.status === 'Verified' ? 'unverified' : 'verified'}" 
                                onclick="toggleVerification(${user.id}, '${user.status}')">
                                ${user.status === 'Verified' ? 'Unverify' : 'Verify'}
                        </button>`
                }
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function toggleVerification(userId, currentStatus) {
    const newStatus = currentStatus === "Verified" ? "Unverified" : "Verified";

    fetch('api/User_api.php', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: userId, verified: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const user = users.find(u => u.id === userId);
            if (user) user.status = newStatus;
            loadUsers();
        } else {
            alert(data.message || "Something went wrong.");
        }
    })
    .catch(err => {
        console.error("Error updating verification:", err);
        alert("Request failed.");
    });
}

document.addEventListener('DOMContentLoaded', fetchUsers);
