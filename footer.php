
</div><!-- content-area -->
</div><!-- main-content -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Toggle sidebar — DIRECT margin control (no broken CSS selectors)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const isCollapsed = sidebar.classList.toggle('collapsed');
    
    const margin = isCollapsed ? '70px' : '260px';
    
    // Directly shift every element that needs to move
    const mainContent = document.querySelector('.main-content');
    const contentArea = document.querySelector('.content-area');
    const topNavbar = document.querySelector('.top-navbar');
    
    if (mainContent) mainContent.style.marginLeft = margin;
    if (contentArea) contentArea.style.marginLeft = margin;
    if (topNavbar) topNavbar.style.marginLeft = margin;
    
    // Save state
    localStorage.setItem('sidebarCollapsed', isCollapsed ? 'true' : 'false');
}

// Restore on page load
document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.add('collapsed');
        
        // Apply collapsed margins immediately
        const margin = '70px';
        const mainContent = document.querySelector('.main-content');
        const contentArea = document.querySelector('.content-area');
        const topNavbar = document.querySelector('.top-navbar');
        
        if (mainContent) mainContent.style.marginLeft = margin;
        if (contentArea) contentArea.style.marginLeft = margin;
        if (topNavbar) topNavbar.style.marginLeft = margin;
    }
});
</script>

</body>
</html>