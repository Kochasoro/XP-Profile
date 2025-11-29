<?php include 'header.php'; ?>
    <script src="resources/js/subject.js"></script>

    <div class="container">
        <div class="section-header">
            <h3>Subjects Management</h3>
            <div class="button-container">
                <button class="add-account-btn" onclick="openModal()">+ Add Subject</button>
            </div>
        </div>
        <br>
        <input type="text" id="searchInput" placeholder="Search subjects..." onkeyup="searchSubjects()">
        <br><br>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="subjectsTableBody"></tbody>
        </table>
    </div>

    <div id="subjectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Subject</h2>
            <input type="hidden" id="subjectId">
            <input type="text" id="code" placeholder="Subject Code">
            <input type="text" id="description" placeholder="Subject Description">
            <button id="saveUpdateBtn" onclick="saveSubject()">Save Subject</button>
        </div>
    </div>

<?php include 'footer.php'; ?>

