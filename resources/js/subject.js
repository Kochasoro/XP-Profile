document.addEventListener("DOMContentLoaded", loadSubjects);

function loadSubjects(){
    fetch("api/Subject_api.php")
        .then(response => response.json())
        .then(data => {
            let subjectsTableBody = document.getElementById("subjectsTableBody");
            subjectsTableBody.innerHTML = "";
            data.subjects.forEach(subject => {
                subjectsTableBody.innerHTML += `
                    <tr>
                        <td>${subject.id}</td>
                        <td>${subject.code}</td>
                        <td>${subject.description}</td>
                        <td>
                            <button onclick="openModal(${subject.id}, '${subject.code}', '${subject.description}')">Edit</button>
                            <button onclick="deleteSubject(${subject.id})"></button>
                        </td>
                    </tr>`;
            });
        })
        .catch(error => console.error("Error fetching subjects:", error));
}

function openModal(id = "", code = "", description = ""){
    document.getElementById("subjectId").value = id;
    document.getElementById("code").value = code;
    document.getElementById("description").value = description;

    if (id) {
        document.getElementById("modalTitle").innerText = "Edit Subject";
        document.getElementById("saveUpdateBtn").innerText = "Update Subject";
        document.getElementById("saveUpdateBtn").setAttribute("onclick", "updateSubject()");
    } else {
        document.getElementById("modalTitle").innerText = "Add Subject";
        document.getElementById("saveUpdateBtn").innerText = "Save Subject";
        document.getElementById("saveUpdateBtn").setAttribute("onclick", "saveSubject()");
    }
    document.getElementById("subjectModal").style.display = "block";
}

function closeModal(){
    document.getElementById("subjectModal").style.display = "none";
}

function saveSubject() {
    if (!confirm("Are you sure you want to save this subject?")) return;

    let code = document.getElementById("code").value;
    let description = document.getElementById("description").value;

    fetch("api/Subject_api.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ code, description})
    })     
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        closeModal();
        loadSubjects();
    })
    .catch(error => console.error("Error saving subject:", error));
}

function updateSubject(){
    if(!confirm("Are you sure you want to update this subject?")) return;

    let id = document.getElementById("subjectId").value;
    let code = document.getElementById("code").value;
    let description = document.getElementById("description").value;

    fetch("api/Subject_api.php",{
        method: "PUT",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({id, code, description})
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        closeModal();
        loadSubjects();
    })
    .catch(error => console.error("Error updating subject:", error));
}

function deleteSubject(){
    if(!confirm("Are you sure you want to delete this subject?")) return;

    fetch(`api/Subject_api.php?id=${id}`,{method: "DELETE"})
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadSubjects();
    })
    .catch(error => console.error("Error deleting subject:", error));
}

function displaySubjects(subjects){
    let subjectsTableBody = document.getElementById("subjectsTableBody");
    subjectsTableBody.innerHTML = "";

    subjects.forEach(subject => {
        subjectsTableBody.innerHTML += `
                    <tr>
                        <td>${subject.id}</td>
                        <td>${subject.code}</td>
                        <td>${subject.description}</td>
                        <td>
                            <div class="button-container">
                                <div class="left-button">
                                    <button class="btn-edit" onclick="openModal(${subject.id}, '${subject.code}', '${subject.description}')">Edit</button>
                                </div>
                                <div class="right-button">
                                    <button class="btn-delete" onclick="deleteSubject(${subject.id})">Delete</button>
                                </div>
                            </div>
                        </td>
                    </tr>`;
    });
}

let subjectsData = [];

function loadSubjects(){
    fetch ("api/Subject_api.php")
        .then(response => response.json())
        .then(data => {
            subjectsData = data.subjects;
            displaySubjects(subjectsData);
        })
        .catch(error => console.error("Error fetching subjects:", error));
}

function searchSubjects(){
    let searchTerm = document.getElementById("searchInput").value.toLowerCase();
    let filteredSubjects = subjectsData.filter(subject =>
        subject.code.toLowerCase().includes(searchTerm) || 
        subject.description.toLowerCase().includes(searchTerm)
    );
    displaySubjects(filteredSubjects);
}
