document.addEventListener('DOMContentLoaded', function () {
    // Edit Modal Data Binder
    const editMemberModal = document.getElementById('editMemberModal');
    if (editMemberModal) {
        editMemberModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const fullName = button.getAttribute('data-fullname');
            const username = button.getAttribute('data-username');
            const email = button.getAttribute('data-email');
            const role = button.getAttribute('data-role');
            const street = button.getAttribute('data-street');
            const barangay = button.getAttribute('data-barangay');
            const city = button.getAttribute('data-city');

            document.getElementById('edit_fullName').value = fullName || '';
            document.getElementById('edit_username_hidden').value = username || '';
            document.getElementById('edit_username_display').value = username || '';
            document.getElementById('edit_emailAddress').value = email || '';
            document.getElementById('edit_role').value = role || '';
            document.getElementById('edit_street').value = street || '';
            document.getElementById('edit_barangay').value = barangay || '';
            document.getElementById('edit_city').value = city || '';
        });
    }

    // Delete Modal Data Binder
    const deleteMemberModal = document.getElementById('deleteMemberModal');
    if (deleteMemberModal) {
        deleteMemberModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const username = button.getAttribute('data-username');
            const fullName = button.getAttribute('data-fullname');

            document.getElementById('delete_username').value = username || '';
            document.getElementById('delete_username_display').textContent = username ? '@' + username : '';
            document.getElementById('delete_fullname_display').textContent = fullName || '';
        });
    }
});
