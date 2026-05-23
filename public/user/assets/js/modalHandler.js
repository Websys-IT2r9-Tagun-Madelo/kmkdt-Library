// --- BOOK DETAILS MODAL HANDLER & ENGINE ---

document.addEventListener("DOMContentLoaded", function () {
  
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
        if (!card) return; 

        const rawOnlineVal = String(card.getAttribute("data-online") || '').toLowerCase();
        const rawStatusVal = String(card.getAttribute("data-status") || '').toLowerCase();

        const data = {
          id: card.getAttribute("data-id"),
          title: card.getAttribute("data-title"),
          author: card.getAttribute("data-author"),
          category: card.getAttribute("data-category") || 'General',
          genre: card.getAttribute("data-genre"),
          desc: card.getAttribute("data-desc"),
          img: card.getAttribute("data-img"),
          status: rawStatusVal,
          isOnline: rawOnlineVal === "true" || rawOnlineVal.includes("online") || rawStatusVal === "online",
          loanPeriod: card.getAttribute("data-loan-period") || '7 Days', 
          borrower: card.getAttribute("data-borrower") || 'Another User',
          dueDate: card.getAttribute("data-due") || '',
          activeReadingId: card.getAttribute("data-active-reading") // Accurately reading the injected code attribute
        };

        // Output diagnostics directly to console window
        console.warn("🚀 Modal Loaded. Book ID:", data.id, " | Active ID:", data.activeReadingId);

        const elements = {
          title: document.getElementById("modalBookTitle"),
          author: document.getElementById("modalAuthor"),
          categoryContainer: document.getElementById("modalCategory"), 
          desc: document.getElementById("modalDesc"),
          img: document.getElementById("modalImg"),
        };

        if (elements.title) elements.title.textContent = data.title;
        if (elements.author) elements.author.textContent = "by " + data.author;
        if (elements.desc) elements.desc.textContent = data.desc;
        if (elements.img) elements.img.src = data.img;

        if (elements.categoryContainer) {
          let tagsHTML = `<span class="badge rounded-pill px-3 py-2 text-white fw-semibold tag-badge">${data.category}</span>`;
          if (data.genre && data.genre.toLowerCase() !== data.category.toLowerCase()) {
              tagsHTML += `<span class="badge rounded-pill px-3 py-2 text-white fw-semibold tag-badge">${data.genre}</span>`;
          }
          elements.categoryContainer.innerHTML = tagsHTML;
          elements.categoryContainer.className = "d-flex align-items-center gap-2 mb-3"; 
        }

        const actionContainer = document.getElementById("modalActionContainer");
        if (actionContainer) {
          const projectRoot = "/kmkdt-Library";
          const borrowPath = `${projectRoot}/app/controller/process/borrowProcess.php?id=${data.id}`;

          if (data.isOnline) {
            const cleanBookId = String(data.id || '').trim();
            const cleanSessionId = String(data.activeReadingId || '').trim();

            // Safe protection layout matching step validation rules
            const isValidSessionId = cleanSessionId !== "" && 
                                     cleanSessionId.toLowerCase() !== "null" && 
                                     cleanSessionId.toLowerCase() !== "undefined";

            const hasActiveSession = (isValidSessionId && cleanBookId !== "" && cleanSessionId === cleanBookId) || 
                                     data.status.includes("active") || 
                                     data.status.includes("reading");

            if (hasActiveSession) {
              actionContainer.innerHTML = `
                <div class="d-flex flex-column gap-2 w-100">
                    <div class="badge-active text-white small fw-bold py-2 rounded-pill d-inline-flex align-items-center justify-content-center border">
                        <iconify-icon icon="lucide:book-open" class="me-1 fs-5"></iconify-icon>
                        <span>Currently Active</span>
                    </div>
                    <a href="${projectRoot}/public/user/eBook?id=${data.id}"
                    class="btn btn-online-custom text-white rounded-pill w-100 fw-bold shadow-sm">
                       Resume Reading
                    </a>
                </div>`;
            } else {
              actionContainer.innerHTML = `
                <a href="${projectRoot}/public/user/eBook?id=${data.id}" 
                class="btn rounded-pill w-100 mt-2 btn-read-online fw-bold shadow-sm" 
                onclick="return confirm('Warning: Opening this may remove your previous e-book.');">
                Read Online</a>`;
            }
          } else if (data.status === "available") {
            actionContainer.innerHTML = `
                <a href="${borrowPath}" 
                   class="btn btn-success w-100 rounded-pill fw-bold"
                   onclick="return confirm('Borrow for ${data.loanPeriod}?');">Borrow Book</a>`;
          } else if (data.status === "owned") {
            actionContainer.innerHTML = `
                <div class="d-flex flex-column gap-2 mt-2">
                <div class="badge-borrowed text-center small fw-bold py-2 rounded-pill d-inline-flex align-items-center justify-content-center border">
                  <iconify-icon icon="lucide:clock" class="me-1 fs-5"></iconify-icon>
                  <span>Borrowed</span>
                </div>
                <a href="/kmkdt-Library/public/user/myBooks"
                  class="btn btn-borrowed-custom text-white rounded-pill w-100 fw-bold shadow-sm">
                  In Your Borrowed Books
                </a></div>`;
          } else {
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
              initModalTimer(actionContainer.querySelector(".modal-due-countdown"));
            } else {
              actionContainer.innerHTML = `<button class="btn btn-light w-100 rounded-pill disabled">Unavailable</button>`;
            }
          }
        }
      });
    }

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

      bookModal.addEventListener('hide.bs.modal', () => clearInterval(x), { once: true });
    }
});