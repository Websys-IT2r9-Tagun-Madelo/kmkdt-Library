document.getElementById('bookModal').addEventListener('show.bs.modal', function (event) {
    const card = event.relatedTarget;
    const id = card.getAttribute('data-id');
    const title = card.getAttribute('data-title');
    const author = card.getAttribute('data-author');
    const category = card.getAttribute('data-category');
    const desc = card.getAttribute('data-desc');
    const img = card.getAttribute('data-img');
    const status = card.getAttribute('data-status');
    const isOnline = card.getAttribute('data-online') === 'true';

    document.getElementById('modalBookTitle').textContent = title;
    document.getElementById('modalAuthor').textContent = 'by ' + author;
    document.getElementById('modalCategory').textContent = category;
    document.getElementById('modalDesc').textContent = desc;
    document.getElementById('modalImg').src = img;

    const actionContainer = document.getElementById('modalActionContainer');
    actionContainer.innerHTML = '';

    if (isOnline) {
        actionContainer.innerHTML = `<a href="Ebook?id=${id}" class="btn rounded-pill w-100" style="background-color: #07427a; color: white;">Read Online</a>`;
    } else if (status === 'available') {
        actionContainer.innerHTML = `<a href="../app/controller/process/borrow_process?id=${id}" class="btn btn-success w-100 rounded-pill fw-bold">Borrow This Book</a>`;
    } else if (status === 'owned') {
        actionContainer.innerHTML = `<button class="btn btn-secondary w-100 rounded-pill disabled">Already in your Shelf</button>`;
    } else {
        actionContainer.innerHTML = `<button class="btn btn-light w-100 rounded-pill disabled">Currently Unavailable</button>`;
    }
});