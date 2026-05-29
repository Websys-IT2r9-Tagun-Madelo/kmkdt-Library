// --- TOGGLE EXTENSION INPUT FIELD VISIBILITY ---
function toggleCustomCategoryField(prefix) {
    const wrapper = document.getElementById(`${prefix}_custom_cat_wrapper`);
    if (wrapper) wrapper.classList.toggle('d-none');
    
    // Clear any leftover inline error messages when toggling
    const errorDiv = document.getElementById(`${prefix}_category_error_msg`);
    if (errorDiv) {
        errorDiv.textContent = "";
        errorDiv.classList.add('d-none');
    }
}

// --- APPEND NEW CATEGORIES SAFELY (With Native Capitalization Fix) ---
function appendCustomCategoryCheckbox(prefix) {
    const inputElement = document.getElementById(`${prefix}_custom_category`);
    const errorDiv = document.getElementById(`${prefix}_category_error_msg`);
    const rawValue = inputElement ? inputElement.value.trim() : "";
    
    // UI Error Handler Helper Logic
    const showError = (message) => {
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');
        } else {
            alert(message);
        }
    };

    // Client-Side Error Check: Empty String
    if (rawValue === "") {
        showError("Category name cannot be empty.");
        return;
    }
    
    // Normalize and Capitalize the First Letter of each word safely
    let formattedValue = rawValue.replace(/[^a-zA-Z0-9\s\-]/g, '');
    const lowerVal = formattedValue.toLowerCase();
    if (lowerVal === 'non fiction' || lowerVal === 'non-ficion' || lowerVal === 'non-fiction') {
        formattedValue = 'Non-Fiction';
    } else {
        // Enforce Capitalization on the first letter
        formattedValue = formattedValue.split(' ')
            .map(word => word.length > 0 ? word.charAt(0).toUpperCase() + word.slice(1) : '')
            .join(' ');
    }
    
    const standardizedSearchVal = formattedValue.toLowerCase().replace(/\s+/g, '_').replace(/-/g, '_');
    const cleanId = `${prefix}_cat_${standardizedSearchVal}`;
    
    // Verify cross-platform item definitions to stop duplicating checkboxes locally
    let targetExists = false;
    document.querySelectorAll(`.${prefix}-cat-check`).forEach(box => {
        // FIXED: Renamed broken variable reference 'normalizadingBoxVal' to 'normalizedBoxVal'
        const normalizedBoxVal = box.value.toLowerCase().replace(/[^a-zA-Z0-9\s\-]/g, '').replace(/\s+/g, '_').replace(/-/g, '_');
        if (normalizedBoxVal === standardizedSearchVal) {
            targetExists = true;
        }
    });
    
    if (targetExists || document.getElementById(cleanId)) {
        showError("This category entry choice already exists.");
        return;
    }

    // Prepare Async Form Payload to communicate with PHP backend controller
    const formData = new FormData();
    formData.append('action', 'add_category');
    formData.append('category_name', formattedValue);

    // Dynamic routing fix: explicitly hit your active form's action endpoint path
    const parentForm = inputElement.closest('form');
    const requestUrl = parentForm ? parentForm.getAttribute('action') : window.location.href;

    fetch(requestUrl, {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        if (!response.ok) {
            throw new Error(`Server returned status code HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Backend Validation Failure Logic
        if (!data.success) {
            showError(data.message || "Database validation pipeline rejected execution mapping.");
            return;
        }

        // Database verified success: Build checkbox node elements safely
        if (errorDiv) errorDiv.classList.add('d-none'); 
        
        const formCheckDiv = document.createElement('div');
        formCheckDiv.className = 'form-check me-3';
        
        const inputCheckbox = document.createElement('input');
        inputCheckbox.type = 'checkbox';
        inputCheckbox.name = 'category[]';
        inputCheckbox.value = data.category; // Now matches capitalized string!
        inputCheckbox.id = cleanId;
        inputCheckbox.className = `form-check-input ${prefix}-cat-check`;
        inputCheckbox.checked = true;
        
        const labelElement = document.createElement('label');
        labelElement.className = 'form-check-label small text-capitalize';
        labelElement.htmlFor = cleanId;
        labelElement.textContent = data.category;
        
        formCheckDiv.appendChild(inputCheckbox);
        formCheckDiv.appendChild(labelElement);
        
        // Smooth inline grouping injection layout logic
        const targetContainer = document.getElementById(`${prefix}_base_categories_group`);
        if (targetContainer) {
            targetContainer.appendChild(formCheckDiv);
        } else {
            const mainContainer = document.getElementById(`${prefix}_category_list_box`);
            if (mainContainer) {
                const divider = mainContainer.querySelector('.designation-divider');
                if (divider) {
                    mainContainer.insertBefore(formCheckDiv, divider);
                } else {
                    mainContainer.appendChild(formCheckDiv);
                }
            }
        }
        
        // Clear input payload properties upon execution success
        inputElement.value = "";
        toggleCustomCategoryField(prefix);
    })
    .catch(error => {
        console.error('AJAX Error Context tracking payload:', error);
        showError("System configuration failure checking connectivity pipeline layout anchors.");
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // --- A. TABLE FILTER LOGIC (SEARCH & CATEGORY DROPDOWN) ---
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const rows = document.querySelectorAll('.book-row');
    const noResultsRow = document.getElementById('noResultsRow');

    function filterTable() {
        const searchText = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedCat = categoryFilter ? categoryFilter.value.toLowerCase().trim() : '';
        let hasResults = false;

        rows.forEach(row => {
            const titleEl = row.querySelector('.book-title');
            const authorEl = row.querySelector('.book-author');
            const genreEl = row.querySelector('.book-genre');
            const categorySpan = row.querySelector('.book-category span.d-none');

            const title = titleEl ? titleEl.textContent.toLowerCase().trim() : '';
            const author = authorEl ? authorEl.textContent.toLowerCase().trim() : '';
            const genre = genreEl ? genreEl.textContent.toLowerCase().trim() : '';
            const rawCategoryText = categorySpan ? categorySpan.textContent.toLowerCase().trim() : '';
            
            // Parse categories into an array of clean, standardized strings
            const bookCategories = rawCategoryText.split(',').map(cat => {
                let trimmed = cat.trim();
                if (trimmed === 'non fiction' || trimmed === 'non-ficion' || trimmed === 'non-fiction') {
                    return 'non-fiction';
                }
                return trimmed;
            }).filter(cat => cat !== "");

            // Global cross-column text matching criteria rule setup
            const matchesSearch = title.includes(searchText) || 
                                  author.includes(searchText) || 
                                  genre.includes(searchText) ||
                                  bookCategories.some(cat => cat.includes(searchText));
            
            // Dropdown option validation check
            const matchesCategory = selectedCat === "" || bookCategories.includes(selectedCat);

            // Toggle table row display states dynamically
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


    // --- B. EDIT MODAL CHECKBOX MATCHING SYNC ---
    const editModal = document.getElementById('editBookModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            // Map simple input elements
            document.getElementById('edit_id').value = button.getAttribute('data-id') || '';
            document.getElementById('edit_title').value = button.getAttribute('data-title') || '';
            document.getElementById('edit_author').value = button.getAttribute('data-author') || '';
            document.getElementById('edit_genre').value = button.getAttribute('data-genre') || '';
            document.getElementById('edit_status').value = button.getAttribute('data-status') || '';
            document.getElementById('edit_description').value = button.getAttribute('data-description') || '';

            // Extract original categories raw string from target row
            const rawCategories = (button.getAttribute('data-category') || '').toLowerCase();
            const categoryList = rawCategories.split(',')
                .map(c => c.trim())
                .map(c => (c === 'non fiction' || c === 'non-ficion' || c === 'non-fiction') ? 'non-fiction' : c)
                .filter(c => c !== "");

            // Set checked states for checkboxes (even elements newly added to DOM)
            document.querySelectorAll('.edit-cat-check').forEach(box => {
                const boxValueNormalized = box.value.toLowerCase().trim();
                
                box.checked = categoryList.some(cat => {
                    return cat === boxValueNormalized || 
                           cat.replace(/-/g, ' ') === boxValueNormalized ||
                           cat === boxValueNormalized.replace(/\s+/g, '-');
                });
            });
        });
    }

    // --- C. DELETE CONFIRMATION INTERCEPT ---
    const deleteModal = document.getElementById('deleteBookModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('delete_id').value = button.getAttribute('data-id') || '';
            const placeholder = deleteModal.querySelector('.id-title-placeholder');
            if (placeholder) {
                placeholder.textContent = button.getAttribute('data-title') || 'This Item';
            }
        });
    }

    // --- D. PREVENT DUPLICATE SUBMISSIONS VIA CLICK SPAM ---
    const modalForms = document.querySelectorAll('.modal form');
    modalForms.forEach(form => {
        form.addEventListener('submit', function () {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                // Disable button and add standard visual feedback context
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            }
        });
    });
});
