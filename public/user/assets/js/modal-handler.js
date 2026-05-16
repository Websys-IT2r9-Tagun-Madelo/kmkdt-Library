// --- 2. BOOK DETAILS MODAL HANDLER ---
// Populates the global "Browse Books" modal dynamically on click
const bookModal = document.getElementById("bookModal");
if (bookModal) {
  bookModal.addEventListener("show.bs.modal", function (event) {
    const card = event.relatedTarget;
    if (!card) return; // Safety bailout if card trigger breaks

    // Extract structural dataset information from our component card element properties
    const data = {
      id: card.getAttribute("data-id"),
      title: card.getAttribute("data-title"),
      author: card.getAttribute("data-author"),
      category: card.getAttribute("data-category") || 'General',
      genre: card.getAttribute("data-genre"),
      desc: card.getAttribute("data-desc"),
      img: card.getAttribute("data-img"),
      status: card.getAttribute("data-status"),
      isOnline: card.getAttribute("data-online") === "true",
      loanPeriod: card.getAttribute("data-loan-period") || '7 Days', 
    };

    // DOM Target layout container targets
    const elements = {
      title: document.getElementById("modalBookTitle"),
      author: document.getElementById("modalAuthor"),
      categoryContainer: document.getElementById("modalCategory"), // Serves as our tag wrapper
      desc: document.getElementById("modalDesc"),
      img: document.getElementById("modalImg"),
    };

    // Update text content metrics cleanly inside the DOM structures
    if (elements.title) elements.title.textContent = data.title;
    if (elements.author) elements.author.textContent = "by " + data.author;
    if (elements.desc) elements.desc.textContent = data.desc;
    if (elements.img) elements.img.src = data.img;

    // Dynamically generate Category AND Genre pill tags side-by-side
    if (elements.categoryContainer) {
      // Pill 1: Main Category
      let tagsHTML = `<span class="badge rounded-pill px-3 py-2 text-white fw-semibold" style="background-color: #22c55e; font-size: 0.85rem; display: inline-flex; align-items: center;">${data.category}</span>`;
      
      // Pill 2: Genre (Only appends as a completely separate pill if it exists and isn't a duplicate)
      if (data.genre && data.genre.toLowerCase() !== data.category.toLowerCase()) {
        tagsHTML += `<span class="badge rounded-pill px-3 py-2 text-white fw-semibold" style="background-color: #22c55e; font-size: 0.85rem; display: inline-flex; align-items: center;">${data.genre}</span>`;
      }
  
      // Inject the individual pills into the clean wrapper row
      elements.categoryContainer.innerHTML = tagsHTML;
      elements.categoryContainer.className = "d-flex align-items-center gap-2 mb-3"; 
    }

    // Generate context-appropriate dynamic action links inside the modal container
    const actionContainer = document.getElementById("modalActionContainer");
    if (actionContainer) {
      const projectRoot = "/kmkdt-Library";
      const borrowPath = `${projectRoot}/app/controller/process/borrowProcess.php?id=${data.id}`;

      if (data.isOnline) {
        actionContainer.innerHTML = `
            <a href="${projectRoot}/public/user/Ebook?id=${data.id}" 
               class="btn rounded-pill w-100" 
               style="background-color: #07427a; color: white;"
               onclick="return confirm('Open E-book reader?');">Read Online</a>`;
      } else if (data.status === "available") {
        actionContainer.innerHTML = `
            <a href="${borrowPath}" 
               class="btn btn-success w-100 rounded-pill fw-bold"
               onclick="return confirm('Borrow for ${data.loanPeriod}?');">Borrow Book</a>`;
      } else if (data.status === "owned") {
        actionContainer.innerHTML = `<button class="btn btn-secondary w-100 rounded-pill disabled">In Shelf</button>`;
      } else {
        actionContainer.innerHTML = `<button class="btn btn-light w-100 rounded-pill disabled">Unavailable</button>`;
      }
    }
  });
}

