// --- 1. PAYMENT FORM INTERCEPTOR ---
document.addEventListener("submit", function (event) {
  // Checks if the form target id starts with our custom prefix string
  if (event.target.id && event.target.id.startsWith("formPayment")) {
    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');

    if (btn) {
      // Visual feedback using your presentation's theme logic
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
      
      // Bootstrap style tracking class
      btn.classList.add("disabled");
      
      // HARD FIX: Prevents multi-click double submissions from corrupting active transaction rows
      btn.disabled = true; 
    }
  }
});

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
      category: card.getAttribute("data-category"),
      desc: card.getAttribute("data-desc"),
      img: card.getAttribute("data-img"),
      status: card.getAttribute("data-status"),
      isOnline: card.getAttribute("data-online") === "true",
      loanPeriod: card.getAttribute("data-loan-period"),
    };

    // DOM Target layout container targets
    const elements = {
      title: document.getElementById("modalBookTitle"),
      author: document.getElementById("modalAuthor"),
      category: document.getElementById("modalCategory"),
      desc: document.getElementById("modalDesc"),
      img: document.getElementById("modalImg"),
    };

    // Update text content metrics cleanly inside the DOM structures
    if (elements.title) elements.title.textContent = data.title;
    if (elements.author) elements.author.textContent = "by " + data.author;
    if (elements.category) elements.category.textContent = data.category;
    if (elements.desc) elements.desc.textContent = data.desc;
    if (elements.img) elements.img.src = data.img;

    // Generate context-appropriate dynamic action links inside the modal container
    const actionContainer = document.getElementById("modalActionContainer");
    if (actionContainer) {
      const projectRoot = "/kmkdt-Library";
      const borrowPath = `${projectRoot}/app/controller/process/borrowProcess?id=${data.id}`;

      if (data.isOnline) {
        actionContainer.innerHTML = `
            <a href="${projectRoot}/public/user/eBook?id=${data.id}" 
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