<?php include 'header.php'; ?>
<link rel="stylesheet" href="resources/css/portfolio.css">
<div class="container">
    <div class="section-header">
    </div>

    <br>
    <br><br>
    <div class="desktop">
    <div id="portfoliosTableBody" class="portfolio-grid"></div>
    </div>
    <div class="taskbar">
        <div class="start-button" onclick="toggleStartMenu()">
            <img src="resources/images/start.png" alt="Start" style="height: 70px;">
        </div>

        <div class="taskbar-items" style="display: flex; align-items: center; gap: 10px; margin-left: -500px;">
            <img src="resources/images/paint.png" alt="paint" style="height: 70px;">
        </div>

        <h3>Portfolio Management</h3>

        <div class="clock">Loading...</div>
    </div>
    <div class="start-menu" id="startMenu" style="display: none;">
        <?php
            if ($_SESSION['role'] === 'admin') {
                echo '<button onclick="window.location.href=\'dashboard\'">Dashboard</button>';
            }
        ?>            
        <button onclick="window.location.href='subject'">Subject</button>
        <button onclick="window.location.href='portfolio'">My Project</button>
        <button class="add-account-btn" onclick="openModal()">+ Add Portfolio</button>
        <button onclick="logoutConfirm()">
            <img src="resources/images/logoff.jpg" alt="logoff" class="logoff-icon"> Logoff
        </button>
        <input type="text" id="searchInput" placeholder="Search portfolios..." onkeyup="searchPortfolios()">
    </div>




</div>

<div id="portfolioModal" class="custom-modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add Portfolio</h2>
        <input type="hidden" id="portfolioId">
        <input type="text" id="title" name="title" placeholder="Title" required>
        <textarea id="summary" name="summary" placeholder="Summary" required></textarea>
        <input type="file" id="profile_image">
        <div id="profileImageWrapper" style="position: relative; width: 100px; height: 100px;">
            <img id="profileImagePreview" src="resources/images/default.jpg" alt="Profile Preview" 
                style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
            <button id="deleteImageBtn" style="display: none; position: absolute; top: 50%; left: 50%; 
                    transform: translate(-50%, -50%); background: red; color: white; border: none; padding: 5px; 
                    cursor: pointer; font-size: 16px; border-radius: 50%; width: 30px; height: 30px;">X</button>
        </div>

        <button id="saveUpdateBtn" onclick="savePortfolio()">Save Portfolio</button>
    </div>
</div>


<div id="portfolioDetailsPopup" class="popup" style="display:none;">
    
    <div class="popup-header">
        <span id="popupClose" class="popup-close" onclick="closePopup()">×</span>
        <h2 id="detailTitle">Portfolio Details</h2>
    </div>
    <div id="portfolioDetailsContainer">
        <div id="profileSection" class="profile-section">
            <img id="popupProfileImage" src="resources/images/default.jpg" alt="Profile Image">
            <h3 id="popupName">Portfolio Owner</h3>
        </div>
        <div id="subCategoryButtons">
        <button onclick="openSubcategoryWindow('Education', currentPortfolioId)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v6m0 0l3-3m-3 3l-3-3m0 12l3-3m-3 3l-3-3m12 6V9m0 0l-3 3m3-3l-3-3M3 12l3 3m0 0l-3 3m0-6l3-3m6 6l3 3"></path></svg>
            Education
        </button>

        <button onclick="openSubcategoryWindow('Experience', currentPortfolioId)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7l10 10m-3-10l-3 10"></path></svg>
            Experience
        </button>

        <button onclick="openSubcategoryWindow('Skills', currentPortfolioId)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h20M12 2v20"></path></svg>
            Skills
        </button>

        <button onclick="openSubcategoryWindow('Projects', currentPortfolioId)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 13h18M3 19h18M3 5h18M9 3v18"></path></svg>
            Projects
        </button>

        <button onclick="openSubcategoryWindow('About Me', currentPortfolioId)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"></circle><path d="M6 21v-4a6 6 0 0 1 12 0v4"></path></svg>
            About Me
        </button>
        </div>
    </div>
</div>         

<div id="logoutModal" class="custom-modal">
  <div class="modal-content logout-xp">
    <div class="xp-header">
      <span>Logging Off...</span>
      <img src="resources/images/windowsxp.png" alt="Windows XP" class="win-logo" />
    </div>
    <div class="xp-body" onclick="logout()">
      <img src="resources/images/shutdown.png" alt="User" class="user-icon" />
      <p>Log Out</p>
    </div>
  </div>
</div>


<script src="resources/js/portfolio.js"></script>
<script>
    

    window.onclick = function(event) {
    const logoutModal = document.getElementById('logoutModal');
    if (event.target === logoutModal) {
        logoutModal.style.display = "none";
    }
}

document.querySelector('.start-button').addEventListener('click', function () {
    const menu = document.getElementById('startMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
});


function logoutConfirm() {
    document.getElementById('logoutModal').style.display = 'block';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

function logout() {
    window.location.href = 'logout.php';
}
function updateClock() {
    const clockElement = document.querySelector('.clock');
    const now = new Date();

    let hours = now.getHours();
    const minutes = now.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12 || 12; 
    const paddedMinutes = minutes.toString().padStart(2, '0');

    clockElement.textContent = `${hours}:${paddedMinutes} ${ampm}`;
  }

  updateClock();

  setInterval(updateClock, 1000);
  
  let currentDragData = null;


  const items = document.querySelectorAll('.portfolio-item');
  let draggedItem = null;
  let offsetX = 0;
  let offsetY = 0;
  itemDiv.querySelectorAll('img, a, button').forEach(el => {
    el.addEventListener('dragstart', (e) => e.preventDefault());
});

  items.forEach(item => {
    item.addEventListener('dragstart', (e) => {
      draggedItem = item;
      draggedItem.classList.add('dragging');

      const rect = item.getBoundingClientRect();
      offsetX = e.clientX - rect.left;
      offsetY = e.clientY - rect.top;
    });

    item.addEventListener('dragend', (e) => {
      draggedItem.classList.remove('dragging');

      const parentRect = item.parentElement.getBoundingClientRect();
      const x = e.clientX - parentRect.left - offsetX;
      const y = e.clientY - parentRect.top - offsetY;

      draggedItem.style.left = `${x}px`;
      draggedItem.style.top = `${y}px`;
    });
  });

  document.getElementById('desktop').addEventListener('dragover', (e) => {
    e.preventDefault();
  });
</script>
<?php include 'footer.php'; ?>
