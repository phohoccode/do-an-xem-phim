// toggle-dropdown.js
const sortToggle = document.getElementById('sortToggle');
const sortMenu = document.getElementById('sortMenu');

sortToggle.addEventListener('click', function (e) {
  e.preventDefault();
  sortMenu.classList.toggle('hidden');
});

// Ẩn khi click ra ngoài
document.addEventListener('click', function (e) {
  if (!sortToggle.contains(e.target) && !sortMenu.contains(e.target)) {
    sortMenu.classList.add('hidden');
  }
});
