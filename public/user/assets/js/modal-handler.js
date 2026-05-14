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
    const loanPeriod = card.getAttribute('data-loan-period');


    document.getElementById('modalBookTitle').textContent = title;
    document.getElementById('modalAuthor').textContent = 'by ' + author;
    document.getElementById('modalCategory').textContent = category;
    document.getElementById('modalDesc').textContent = desc;
    document.getElementById('modalImg').src = img;

    const actionContainer = document.getElementById('modalActionContainer');
    actionContainer.innerHTML = '';

    const projectRoot = '/kmkdt-Library';
    const correctPath = `${projectRoot}/app/controller/process/borrowProcess.php?id=${id}`;
    
    if (isOnline) {
        actionContainer.innerHTML = `<a href="${projectRoot}/public/user/eBook?id=${id}" 
        class="btn rounded-pill w-100" 
        style="background-color: #07427a; color: white;"
           onclick="return confirm('You are about to open the E-book reader. Continue?');">
           Read Online
        </a>`;
    } else if (status === 'available') {
        actionContainer.innerHTML = `
        <a href="${correctPath}" 
           class="btn btn-success w-100 rounded-pill fw-bold"
           onclick="return confirm('Are you sure you want to borrow this book for ${loanPeriod}?');">
           Borrow Book
        </a>`;
    } else if (status === 'owned') {
        actionContainer.innerHTML = `<button class="btn btn-secondary w-100 rounded-pill disabled">Already in your Shelf</button>`;
    } else {
        actionContainer.innerHTML = `<button class="btn btn-light w-100 rounded-pill disabled">Currently Unavailable</button>`;
    }
});