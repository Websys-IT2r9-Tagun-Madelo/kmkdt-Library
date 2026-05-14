document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const table = document.getElementById('catalogTable');
    const noResultsRow = document.getElementById('noResultsRow');

    if (!searchInput || !categoryFilter || !table || !noResultsRow) return;

    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedCat = categoryFilter.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr:not(#noResultsRow)');
        let visibleCount = 0;

        rows.forEach(row => {
            const title = row.cells[1].textContent.toLowerCase();
            const author = row.cells[2].textContent.toLowerCase();
            const category = row.cells[3].textContent.toLowerCase().trim();

            const matchesSearch = title.includes(query) || author.includes(query);
            const matchesCategory = (selectedCat === "") || (category === selectedCat);

            if (matchesSearch && matchesCategory) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        // Toggle the 'No Results' row based on whether visibleCount is 0
        noResultsRow.style.display = (visibleCount === 0) ? "" : "none";
    }

    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);
});