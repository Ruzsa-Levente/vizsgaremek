function toggleSearch() {
    const searchContainer = document.querySelector('.search-container');
    searchContainer.classList.toggle('active');
    document.getElementById('search-input').focus();
}

function hideSearch() {
    const searchInput = document.getElementById('search-input');
    if (!searchInput.value) {
        const searchContainer = document.querySelector('.search-container');
        searchContainer.classList.remove('active');
    }
}
// Ha a felhasználó Enter-t nyom a keresőben
document.getElementById('search-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        // Ellenőrizze, hogy a keresett kifejezés nem üres
        const searchTerm = this.value.trim();
        if (searchTerm) {
            // Az oldal újratöltése és keresési kifejezés átadása
            window.location.href = `clothes.php?search=${encodeURIComponent(searchTerm)}`;
        }
    }
});
