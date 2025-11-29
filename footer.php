
    <?php if (basename($_SERVER['PHP_SELF']) !== 'portfolio.php'): ?>
        <div id="logoutModal" class="modal">
            <div class="modal-content logout">
                <h3>Are you sure you want to logout?</h3>
                <div class="logout-buttons">
                    <button onclick="logout()">Yes</button>
                    <button onclick="closeLogoutModal()">Cancel</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

 
</body>
</html>
