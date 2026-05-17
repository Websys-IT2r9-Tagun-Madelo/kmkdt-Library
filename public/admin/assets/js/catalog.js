document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const rows = document.querySelectorAll('.book-row');
    const noResultsRow = document.getElementById('noResultsRow');

    function filterTable() {
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedCat = categoryFilter.value.toLowerCase().trim();
        let hasResults = false;

        rows.forEach(row => {
            const titleEl = row.querySelector('.book-title');
            const authorEl = row.querySelector('.book-author');
            const categoryEl = row.querySelector('.book-category');

            const title = titleEl ? titleEl.textContent.toLowerCase() : '';
            const author = authorEl ? authorEl.textContent.toLowerCase() : '';
            
            const rawCategoryText = categoryEl ? categoryEl.textContent.toLowerCase() : '';
            const bookCategories = rawCategoryText.split(',').map(cat => cat.trim());


            const matchesSearch = title.includes(searchText) || author.includes(searchText);
            

            const matchesCategory = selectedCat === "" || bookCategories.includes(selectedCat);


            if (matchesSearch && matchesCategory) {
                row.style.display = "";
                hasResults = true;
            } else {
                row.style.display = "none";
            }
        });

        if (noResultsRow) {
            noResultsRow.style.display = hasResults ? "none" : "";
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (categoryFilter) categoryFilter.addEventListener('change', filterTable);
});
