document.getElementById('toggle-btn').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('main-content').classList.toggle('active');
});

document.getElementById('user-wrapper').addEventListener('click', function() {
    document.getElementById('user-menu').classList.toggle('active');
});

