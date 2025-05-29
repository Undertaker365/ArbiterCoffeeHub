// Main public JS for Arbiter Coffee Hub
// Carousel drag logic (Featured Products)
document.addEventListener('DOMContentLoaded', function() {
  const carousel = document.getElementById('featured-carousel');
  let isDown = false, startX, scrollLeft;
  if (carousel && carousel.parentElement.classList.contains('overflow-x-auto')) {
    carousel.parentElement.addEventListener('mousedown', (e) => {
      isDown = true;
      carousel.parentElement.classList.add('cursor-grabbing');
      startX = e.pageX - carousel.parentElement.offsetLeft;
      scrollLeft = carousel.parentElement.scrollLeft;
    });
    carousel.parentElement.addEventListener('mouseleave', () => {
      isDown = false;
      carousel.parentElement.classList.remove('cursor-grabbing');
    });
    carousel.parentElement.addEventListener('mouseup', () => {
      isDown = false;
      carousel.parentElement.classList.remove('cursor-grabbing');
    });
    carousel.parentElement.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - carousel.parentElement.offsetLeft;
      const walk = (x - startX) * 2;
      carousel.parentElement.scrollLeft = scrollLeft - walk;
    });
  }

  // Quick View Modal logic
  const modal = document.getElementById('quick-view-modal');
  const closeBtn = document.getElementById('close-quick-view');
  document.querySelectorAll('.quick-view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const prod = JSON.parse(this.getAttribute('data-product'));
      document.getElementById('qv-image').src = prod.image;
      document.getElementById('qv-image').alt = prod.name + ' product image';
      document.getElementById('qv-name').textContent = prod.name;
      document.getElementById('qv-description').textContent = prod.description;
      document.getElementById('qv-price').textContent = '₱' + Number(prod.price).toFixed(2);
      let rating = prod.rating ? parseInt(prod.rating) : Math.floor(Math.random()*2)+4;
      let stars = '';
      for (let i=0; i<rating; i++) stars += '<span class="text-yellow-500 text-lg">★</span>';
      for (let i=rating; i<5; i++) stars += '<span class="text-gray-300 text-lg">☆</span>';
      document.getElementById('qv-rating').innerHTML = stars;
      modal.classList.remove('hidden');
      trapFocus(modal);
    });
  });
  if (closeBtn && modal) {
    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
  }

  // Testimonials carousel navigation
  const tCarousel = document.getElementById('testimonial-carousel');
  const tPrev = document.getElementById('testimonial-prev');
  const tNext = document.getElementById('testimonial-next');
  function updateNavButtons() {
    if (tCarousel && tPrev && tNext) {
      tPrev.style.display = tNext.style.display = tCarousel.scrollWidth > tCarousel.clientWidth ? 'flex' : 'none';
    }
  }
  if (tCarousel && tPrev && tNext) {
    updateNavButtons();
    window.addEventListener('resize', updateNavButtons);
    tPrev.addEventListener('click', () => tCarousel.scrollBy({left: -340, behavior: 'smooth'}));
    tNext.addEventListener('click', () => tCarousel.scrollBy({left: 340, behavior: 'smooth'}));
    let hoverScroll;
    tCarousel.addEventListener('mousemove', function(e) {
      if (window.innerWidth < 768) return;
      var bounds = tCarousel.getBoundingClientRect();
      var x = e.clientX - bounds.left;
      var width = bounds.width;
      var edge = 60;
      clearInterval(hoverScroll);
      if (x < edge) {
        hoverScroll = setInterval(function(){tCarousel.scrollBy({left:-10,behavior:'auto'});},10);
      } else if (x > width - edge) {
        hoverScroll = setInterval(function(){tCarousel.scrollBy({left:10,behavior:'auto'});},10);
      }
    });
    tCarousel.addEventListener('mouseleave',()=>{clearInterval(hoverScroll);});
    tCarousel.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowLeft') { tPrev.click(); e.preventDefault(); }
      if (e.key === 'ArrowRight') { tNext.click(); e.preventDefault(); }
    });
  }

  // Floating Social: click to expand/collapse, improved spacing and accessibility
  var floatingSocial = document.getElementById('floating-social');
  var toggleBtn = document.getElementById('social-toggle');
  var socialLinks = document.getElementById('social-links');
  var expanded = false;
  function setExpanded(state) {
    expanded = state;
    floatingSocial.classList.toggle('expanded', expanded);
    toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  }
  if (toggleBtn && socialLinks && floatingSocial) {
    toggleBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      setExpanded(!expanded);
      if (expanded) {
        socialLinks.querySelector('a').focus();
      }
    });
    document.addEventListener('click', function(e) {
      if (expanded && !floatingSocial.contains(e.target)) {
        setExpanded(false);
      }
    });
    floatingSocial.addEventListener('keydown', function(e) {
      if (expanded && e.key === 'Escape') {
        setExpanded(false);
        toggleBtn.focus();
      }
    });
    var links = socialLinks.querySelectorAll('a');
    if (links.length) {
      links[links.length-1].addEventListener('blur', function() {
        setTimeout(function() {
          if (!socialLinks.contains(document.activeElement)) {
            setExpanded(false);
          }
        }, 100);
      });
    }
  }

  // Welcome bar logic
  window.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const username = body.getAttribute('data-welcome-user');
    if (username) {
      const header = document.querySelector('header');
      if (header && !header.querySelector('.arbiter-welcome')) {
        const bar = document.createElement('div');
        bar.className = 'arbiter-welcome w-full bg-black text-white py-3 text-center font-semibold text-lg animate-fade-in-down';
        bar.setAttribute('aria-live','polite');
        bar.innerHTML = 'Welcome back, ' + username + '!';
        header.insertBefore(bar, header.firstChild);
      }
    }
  });

  // --- Live Search Suggestions for Menu Page (Clickable & Mouse Functional) ---
  if (document.getElementById('search-input')) {
    const allProducts = window.allProductsData || [];
    const searchInput = document.getElementById('search-input');
    const suggestionsBox = document.getElementById('search-suggestions');
    searchInput.addEventListener('input', function() {
      const query = searchInput.value.trim().toLowerCase();
      if (!query) { suggestionsBox.innerHTML = ''; suggestionsBox.classList.add('hidden'); return; }
      const matches = allProducts.filter(p =>
        p.name.toLowerCase().includes(query) ||
        (p.description && p.description.toLowerCase().includes(query)) ||
        (p.category && p.category.toLowerCase().includes(query))
      ).slice(0, 6);
      if (matches.length === 0) { suggestionsBox.innerHTML = ''; suggestionsBox.classList.add('hidden'); return; }
      suggestionsBox.innerHTML = matches.map(p => `<div class='px-4 py-2 hover:bg-gray-100 cursor-pointer suggestion-item' tabindex="0">${p.name}</div>`).join('');
      suggestionsBox.classList.remove('hidden');
    });
    document.addEventListener('click', function(e) {
      if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
        suggestionsBox.classList.add('hidden');
      }
    });
    // Make suggestions clickable and mouse functional
    suggestionsBox.addEventListener('mousedown', function(e) {
      const item = e.target.closest('.suggestion-item');
      if (item && e.button === 0) { // left mouse button
        e.preventDefault(); // Prevent input blur
        searchInput.value = item.textContent;
        suggestionsBox.classList.add('hidden');
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
      }
    });
    // Keyboard accessibility
    suggestionsBox.addEventListener('keydown', function(e) {
      if (e.target.classList.contains('suggestion-item') && (e.key === 'Enter' || e.key === ' ')) {
        e.preventDefault();
        searchInput.value = e.target.textContent;
        suggestionsBox.classList.add('hidden');
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
      }
    });
  }

  // --- AJAX Filtering/Search for Announcements Page ---
  if (document.querySelector('.max-w-4xl.mx-auto')) {
    const form = document.getElementById('menu-search-form');
    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');
    const categoryLinks = document.querySelectorAll('.category-filter');
    let typingTimer;
    function fetchMenu(params) {
      const url = 'announcements.php?' + new URLSearchParams(params).toString();
      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const newMain = doc.querySelector('.max-w-4xl.mx-auto');
          document.querySelector('.max-w-4xl.mx-auto').replaceWith(newMain);
          window.history.replaceState({}, '', url);
        });
    }
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
          fetchMenu({
            search: searchInput.value,
            sort: sortSelect ? sortSelect.value : '',
          });
        }, 300);
      });
    }
    if (sortSelect) {
      sortSelect.addEventListener('change', function() {
        fetchMenu({
          search: searchInput ? searchInput.value : '',
          sort: sortSelect.value,
        });
      });
    }
    categoryLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        fetchMenu({
          category: link.getAttribute('data-category'),
        });
      });
    });
  }

  // --- Register Modal Logic ---
  window.openRegisterModal = function() {
    fetch('csrf_token.php')
      .then(res => res.json())
      .then(data => {
        const input = document.querySelector('#register-modal input[name="csrf_token"]');
        if (input && data.token) input.value = data.token;
        const modal = document.getElementById('register-modal');
        modal.classList.remove('hidden');
        trapFocus(modal);
      })
      .catch(() => {
        const modal = document.getElementById('register-modal');
        modal.classList.remove('hidden');
        trapFocus(modal);
      });
  };
  window.closeRegisterModal = function() {
    const modal = document.getElementById('register-modal');
    modal.classList.add('hidden');
  };

  // --- Login Modal Logic ---
  window.openLoginModal = function() {
    // Always fetch a fresh CSRF token when opening the modal
    fetch('csrf_token.php')
      .then(res => res.json())
      .then(data => {
        const input = document.querySelector('#login-modal input[name="csrf_token"]');
        if (input && data.token) {
          input.value = data.token;
          console.log('Updated CSRF token:', data.token); // Debug
        }
        const modal = document.getElementById('login-modal');
        modal.classList.remove('hidden');
        trapFocus(modal);
      })
      .catch((error) => {
        console.error('Failed to fetch CSRF token:', error);
        const modal = document.getElementById('login-modal');
        modal.classList.remove('hidden');
        trapFocus(modal);
      });
  };
  window.closeLoginModal = function() {
    const modal = document.getElementById('login-modal');
    modal.classList.add('hidden');
  };

  // --- Social Share/Copy Link Logic for Announcement Single Page ---
  document.querySelectorAll('[data-copy-link]').forEach(btn => {
    btn.addEventListener('click', function() {
      navigator.clipboard.writeText(window.location.href);
      btn.title = 'Copied!';
      const feedback = document.getElementById('copy-annc-feedback');
      if (feedback) {
        feedback.classList.remove('hidden');
        setTimeout(() => feedback.classList.add('hidden'), 1500);
      }
      setTimeout(() => { btn.title = 'Copy link'; }, 1500);
    });
  });

  // --- Modal Focus Trap Accessibility ---
  function trapFocus(modal) {
    const focusableSelectors = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])';
    const focusableEls = Array.from(modal.querySelectorAll(focusableSelectors)).filter(el => el.offsetParent !== null);
    if (focusableEls.length === 0) return;
    let firstEl = focusableEls[0];
    let lastEl = focusableEls[focusableEls.length - 1];
    function handleTrap(e) {
      if (e.key === 'Tab') {
        if (e.shiftKey) {
          if (document.activeElement === firstEl) {
            e.preventDefault();
            lastEl.focus();
          }
        } else {
          if (document.activeElement === lastEl) {
            e.preventDefault();
            firstEl.focus();
          }
        }
      } else if (e.key === 'Escape') {
        if (modal.id === 'login-modal') closeLoginModal();
        if (modal.id === 'register-modal') closeRegisterModal();
        if (modal.id === 'quick-view-modal') modal.classList.add('hidden');
      }
    }
    modal.addEventListener('keydown', handleTrap);
    // Focus the first element
    setTimeout(() => firstEl.focus(), 50);
    // Remove event on close
    function cleanup() {
      modal.removeEventListener('keydown', handleTrap);
      modal.removeEventListener('transitionend', cleanup);
    }
    modal.addEventListener('transitionend', cleanup);
  }

  // Patch modal open/close logic to use focus trap
  window.openLoginModal = function() {
    // Always fetch a fresh CSRF token when opening the modal
    fetch('csrf_token.php')
      .then(res => res.json())
      .then(data => {
        const input = document.querySelector('#login-modal input[name="csrf_token"]');
        if (input && data.token) {
          input.value = data.token;
          console.log('Updated CSRF token:', data.token); // Debug
        }
        const modal = document.getElementById('login-modal');
        modal.classList.remove('hidden');
        trapFocus(modal);
      })
      .catch((error) => {
        console.error('Failed to fetch CSRF token:', error);
        const modal = document.getElementById('login-modal');
        modal.classList.remove('hidden');
        trapFocus(modal);
      });
  };
  window.closeLoginModal = function() {
    const modal = document.getElementById('login-modal');
    modal.classList.add('hidden');
  };
  window.openRegisterModal = function() {
    fetch('csrf_token.php')
      .then(res => res.json())
      .then(data => {
        const input = document.querySelector('#register-modal input[name="csrf_token"]');
        if (input && data.token) input.value = data.token;
        const modal = document.getElementById('register-modal');
        modal.classList.remove('hidden');
        trapFocus(modal);
      })
      .catch(() => {
        const modal = document.getElementById('register-modal');
        modal.classList.remove('hidden');
        trapFocus(modal);
      });
  };
  window.closeRegisterModal = function() {
    const modal = document.getElementById('register-modal');
    modal.classList.add('hidden');
  };

  // --- Live Update Polling Logic for Barista/Customer ---
  (function() {
    // Utility: get page context
    function getPage() {
      if (document.querySelector('.barista-orders-list')) return 'barista-orders';
      if (document.querySelector('.customer-order-history-list')) return 'customer-order-history';
      return null;
    }
    const page = getPage();
    if (!page) return;

    // Add live update toggle and last updated indicator
    let polling = true;
    let interval = 10000; // 10 seconds
    let pollTimer = null;
    let lastUpdated = null;

    function updateIndicator() {
      let indicator = document.getElementById('live-update-indicator');
      if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'live-update-indicator';
        indicator.className = 'text-xs text-gray-500 mt-2 flex items-center gap-2';
        const parent = document.querySelector('h1, .page-title, .barista-title');
        if (parent && parent.parentNode) parent.parentNode.insertBefore(indicator, parent.nextSibling);
        else document.body.appendChild(indicator);
      }
      indicator.innerHTML = `<span class="mr-2">Live updates: <button id="toggle-live-update" class="underline text-blue-600">${polling ? 'On' : 'Off'}</button></span>` + (lastUpdated ? `<span>Last updated: <span id="last-updated-time">${lastUpdated}</span></span>` : '');
      document.getElementById('toggle-live-update').onclick = function() {
        polling = !polling;
        if (polling) startPolling(); else stopPolling();
        updateIndicator();
      };
    }

    function doReload() {
      if (page === 'barista-orders' && window.reloadBaristaOrders) window.reloadBaristaOrders();
      if (page === 'customer-order-history' && window.reloadOrderHistory) window.reloadOrderHistory();
      lastUpdated = new Date().toLocaleTimeString();
      updateIndicator();
    }
    function startPolling() {
      if (pollTimer) clearInterval(pollTimer);
      doReload();
      pollTimer = setInterval(() => { if (polling) doReload(); }, interval);
    }
    function stopPolling() {
      if (pollTimer) clearInterval(pollTimer);
    }
    // Add indicator and start polling
    updateIndicator();
    startPolling();
  })();

  // --- AJAX Filtering/Search for Menu Page (Enhanced) ---
  if (document.getElementById('menu-search-form')) {
    const form = document.getElementById('menu-search-form');
    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');
    const categoryLinks = document.querySelectorAll('.category-filter');
    const productsGrid = document.getElementById('product-grid');
    const pagination = document.getElementById('menu-pagination');
    const clearBtn = document.getElementById('clear-filters');
    let typingTimer, lastQuery = '', lastSort = '', lastCategory = '', lastPage = 1;
    let spinner = document.getElementById('menu-loading-spinner');
    if (!spinner) {
      spinner = document.createElement('div');
      spinner.id = 'menu-loading-spinner';
      spinner.className = 'w-full flex justify-center py-8 hidden';
      spinner.innerHTML = '<span class="loader border-4 border-[#009245] border-t-transparent rounded-full w-10 h-10 animate-spin" aria-label="Loading"></span>';
      productsGrid.parentNode.insertBefore(spinner, productsGrid);
    }
    // ARIA live region for result count
    let liveRegion = document.getElementById('menu-live-region');
    if (!liveRegion) {
      liveRegion = document.createElement('div');
      liveRegion.id = 'menu-live-region';
      liveRegion.setAttribute('aria-live', 'polite');
      liveRegion.className = 'sr-only';
      productsGrid.parentNode.insertBefore(liveRegion, productsGrid);
    }
    function showSpinner() { spinner.classList.remove('hidden'); }
    function hideSpinner() { spinner.classList.add('hidden'); }
    function getActiveCategory() {
      const active = document.querySelector('.category-filter[aria-pressed="true"]');
      return active ? active.getAttribute('data-category') : '';
    }
    function updateClearBtn() {
      if ((searchInput && searchInput.value) || (sortSelect && sortSelect.value) || getActiveCategory()) {
        clearBtn.classList.remove('hidden');
      } else {
        clearBtn.classList.add('hidden');
      }
    }
    function updateCategoryAria() {
      categoryLinks.forEach(link => {
        if (link.classList.contains('bg-[#009245]')) {
          link.setAttribute('aria-pressed', 'true');
        } else {
          link.setAttribute('aria-pressed', 'false');
        }
      });
    }
    function fetchMenu(params, force = false) {
      if (!force && params.search === lastQuery && params.sort === lastSort && params.category === lastCategory && params.page === lastPage) return;
      lastQuery = params.search; lastSort = params.sort; lastCategory = params.category; lastPage = params.page || 1;
      showSpinner();
      productsGrid.setAttribute('aria-busy', 'true');
      fetch('menu_fetch.php?' + new URLSearchParams(params).toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
          // Split HTML into grid and pagination
          const temp = document.createElement('div');
          temp.innerHTML = html;
          const newGrid = temp.querySelector('#product-grid');
          const newPagination = temp.querySelector('#menu-pagination');
          if (newGrid) productsGrid.innerHTML = newGrid.innerHTML;
          if (newPagination && pagination) pagination.innerHTML = newPagination.innerHTML;
          // Announce result count
          const count = productsGrid.querySelectorAll('.product-card').length;
          liveRegion.textContent = count ? `${count} products found.` : 'No products found.';
          // Focus first product for accessibility
          const firstCard = productsGrid.querySelector('.product-card');
          if (firstCard) firstCard.focus();
        })
        .catch(() => {
          productsGrid.innerHTML = '<div class="text-red-600 text-center py-8">Failed to load products. Please try again.</div>';
          liveRegion.textContent = 'Failed to load products.';
        })
        .finally(() => {
          hideSpinner();
          productsGrid.removeAttribute('aria-busy');
        });
    }
    // Debounced input
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
          fetchMenu({
            search: searchInput.value,
            sort: sortSelect ? sortSelect.value : '',
            category: getActiveCategory(),
            page: 1
          });
          updateClearBtn();
        }, 300);
      });
      searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          fetchMenu({
            search: searchInput.value,
            sort: sortSelect ? sortSelect.value : '',
            category: getActiveCategory(),
            page: 1
          }, true);
          updateClearBtn();
        }
      });
    }
    if (sortSelect) {
      sortSelect.addEventListener('change', function() {
        fetchMenu({
          search: searchInput ? searchInput.value : '',
          sort: sortSelect.value,
          category: getActiveCategory(),
          page: 1
        });
        updateClearBtn();
      });
    }
    categoryLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        categoryLinks.forEach(l => l.classList.remove('bg-[#009245]', 'text-white', 'ring-2', 'ring-[#009245]'));
        link.classList.add('bg-[#009245]', 'text-white', 'ring-2', 'ring-[#009245]');
        updateCategoryAria();
        fetchMenu({
          search: searchInput ? searchInput.value : '',
          sort: sortSelect ? sortSelect.value : '',
          category: link.getAttribute('data-category'),
          page: 1
        }, true);
        link.focus();
        updateClearBtn();
      });
      // Keyboard accessibility
      link.setAttribute('tabindex', '0');
      link.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          link.click();
        }
      });
    });
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      fetchMenu({
        search: searchInput ? searchInput.value : '',
        sort: sortSelect ? sortSelect.value : '',
        category: getActiveCategory(),
        page: 1
      }, true);
      updateClearBtn();
      return false;
    });
    // Search button triggers search
    const searchBtn = form.querySelector('button[type="submit"]');
    if (searchBtn) {
      searchBtn.addEventListener('click', function(e) {
        e.preventDefault();
        fetchMenu({
          search: searchInput ? searchInput.value : '',
          sort: sortSelect ? sortSelect.value : '',
          category: getActiveCategory(),
          page: 1
        }, true);
        updateClearBtn();
        return false;
      });
    }
    // Clear filters button
    if (clearBtn) {
      clearBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (searchInput) searchInput.value = '';
        if (sortSelect) sortSelect.value = '';
        categoryLinks.forEach(l => l.classList.remove('bg-[#009245]', 'text-white', 'ring-2', 'ring-[#009245]'));
        updateCategoryAria();
        fetchMenu({ search: '', sort: '', category: '', page: 1 }, true);
        updateClearBtn();
        searchInput.focus();
      });
    }
    // Pagination click (event delegation)
    if (pagination) {
      pagination.addEventListener('click', function(e) {
        const pageBtn = e.target.closest('a[data-page]');
        if (pageBtn) {
          e.preventDefault();
          const pageNum = parseInt(pageBtn.getAttribute('data-page'));
          fetchMenu({
            search: searchInput ? searchInput.value : '',
            sort: sortSelect ? sortSelect.value : '',
            category: getActiveCategory(),
            page: pageNum
          }, true);
          window.scrollTo({ top: productsGrid.offsetTop - 100, behavior: 'smooth' });
        }
      });
    }
    // Initial load
    fetchMenu({ search: '', sort: '', category: '', page: 1 }, true);
    updateClearBtn();
    updateCategoryAria();
  }

  // --- Product Quick View Modal (for Menu) ---
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.quick-view-btn');
    if (btn) {
      const prod = JSON.parse(btn.getAttribute('data-product'));
      const modal = document.getElementById('quick-view-modal');
      if (modal) {
        modal.querySelector('.qv-image').src = prod.image;
        modal.querySelector('.qv-image').alt = prod.name + ' product image';
        modal.querySelector('.qv-name').textContent = prod.name;
        modal.querySelector('.qv-description').textContent = prod.description;
        modal.querySelector('.qv-price').textContent = '₱' + Number(prod.price).toFixed(2);
        let rating = prod.rating ? parseInt(prod.rating) : 5;
        let stars = '';
        for (let i=0; i<rating; i++) stars += '<span class="text-yellow-500 text-lg">★</span>';
        for (let i=rating; i<5; i++) stars += '<span class="text-gray-300 text-lg">☆</span>';
        modal.querySelector('.qv-rating').innerHTML = stars;
        modal.classList.remove('hidden');
        modal.setAttribute('aria-modal', 'true');
        modal.focus();
      }
    }
  });
  document.querySelectorAll('.close-quick-view').forEach(btn => {
    btn.addEventListener('click', function() {
      document.getElementById('quick-view-modal').classList.add('hidden');
    });
  });

  // --- Ensure "Order Now" for guests always opens login modal ---
  document.addEventListener('click', function(e) {
    const orderBtn = e.target.closest('.order-now-btn');
    if (orderBtn) {
      console.log('Order Now button clicked', orderBtn); // DEBUG
      e.preventDefault();
      if (typeof window.openLoginModal === 'function') {
        window.openLoginModal();
      } else {
        // Fallback: show modal if function not found
        const modal = document.getElementById('login-modal');
        if (modal) modal.classList.remove('hidden');
      }
      return false;
    }
  });

  // Ensure header Login button always opens the login modal
  const headerLoginBtn = document.getElementById('header-login-btn');
  if (headerLoginBtn) {
    headerLoginBtn.addEventListener('click', function(e) {
      e.preventDefault();
      if (typeof window.openLoginModal === 'function') {
        window.openLoginModal();
      } else {
        const modal = document.getElementById('login-modal');
        if (modal) modal.classList.remove('hidden');
      }
    });
  }

  // Enhanced login form submission with CSRF token validation
  const loginForm = document.getElementById('login-form');
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      // Always fetch a fresh CSRF token before form submission
      fetch('csrf_token.php')
        .then(res => res.json())
        .then(data => {
          if (data && data.token) {
            // Update the form's CSRF token
            const csrfInput = document.getElementById('csrf-token');
            if (csrfInput) csrfInput.value = data.token;
            
            // Now manually submit the form
            loginForm.submit();
          }
        })
        .catch(err => {
          console.error('Error fetching CSRF token:', err);
          // Continue with form submission anyway
          loginForm.submit();
        });
        
      e.preventDefault(); // Prevent normal form submission
    });
  }
});
