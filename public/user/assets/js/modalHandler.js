// --- 2. BOOK DETAILS MODAL HANDLER & ENGINE ---

document.addEventListener("DOMContentLoaded", function () {
    
    // ==========================================
    // A. ENGINE FOR MAIN GRID CARDS ON PAGE LOAD
    // ==========================================
    const mainGridTimers = document.querySelectorAll(".due-countdown");
    
    mainGridTimers.forEach(timerElement => {
        const dueDateStr = timerElement.getAttribute("data-due");
        if (!dueDateStr) return;
        
        const countDownDate = new Date(dueDateStr.replace(/-/g, "/")).getTime();
        
        const gridInterval = setInterval(function () {
            const now = new Date().getTime();
            const distance = countDownDate - now;
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            if (distance < 0) {
                clearInterval(gridInterval);
                timerElement.innerHTML = "Overdue!";
                timerElement.className = "fw-bold text-danger";
            } else {
                timerElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s left`;
            }
        }, 1000);
    });

    // ==========================================
    // B. MODAL HANDLER AND DYNAMIC INITIALIZATION
    // ==========================================
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
          borrower: card.getAttribute("data-borrower") || 'Another User',
          dueDate: card.getAttribute("data-due") || ''
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
                <a href="${projectRoot}/public/user/eBook?id=${data.id}" 
                   class="btn rounded-pill w-100" 
                   style="background-color: #07427a; color: white;"
                   onclick="return confirm('Warning: Opening this may remove your previous e-book.');">Read Online</a>`;
          } else if (data.status === "available") {
            actionContainer.innerHTML = `
                <a href="${borrowPath}" 
                   class="btn btn-success w-100 rounded-pill fw-bold"
                   onclick="return confirm('Borrow for ${data.loanPeriod}?');">Borrow Book</a>`;
          } else if (data.status === "owned") {
            actionContainer.innerHTML = `<button class="btn btn-secondary w-100 rounded-pill disabled">In Shelf</button>`;
          } else {
            // Dynamic box layout replacing the generic disabled button when 'Unavailable'
            if (data.dueDate) {
              actionContainer.innerHTML = `
                <div class="p-3 bg-dark rounded text-center w-100">
                  <div class="text-white-50 small mb-1">
                    <iconify-icon icon="lucide:user" class="me-1"></iconify-icon>
                    Borrowed by: <strong class="text-white">${data.borrower}</strong>
                  </div>
                  <div class="fw-bold text-warning modal-due-countdown" data-due="${data.dueDate}">
                    Calculating time...
                  </div>
                </div>`;
              
              // Instantly fire the counter loop initialization logic on the newly injected modal container element
              initModalTimer(actionContainer.querySelector(".modal-due-countdown"));
            } else {
              actionContainer.innerHTML = `<button class="btn btn-light w-100 rounded-pill disabled">Unavailable</button>`;
            }
          }
        }
      });
    }

    // ==========================================
    // C. AUXILIARY TIMER INITIALIZATION FUNCTION
    // ==========================================
    function initModalTimer(timerElement) {
      if (!timerElement) return;
      const dueDateStr = timerElement.getAttribute("data-due");
      const countDownDate = new Date(dueDateStr.replace(/-/g, "/")).getTime();

      const x = setInterval(function () {
        const now = new Date().getTime();
        const distance = countDownDate - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        if (distance < 0) {
          clearInterval(x);
          timerElement.innerHTML = "Overdue!";
          timerElement.className = "fw-bold text-danger";
        } else {
          timerElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s left`;
        }
      }, 1000);

      // Clear timers if the user decides to close down the modal midway through countdown initialization loops
      bookModal.addEventListener('hide.bs.modal', () => clearInterval(x), { once: true });
    }
});