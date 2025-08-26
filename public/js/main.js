// Toggle functie
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('collapsed');
}

// Event listener toevoegen
document.querySelector('.toggle-btn').addEventListener('click', toggleSidebar);