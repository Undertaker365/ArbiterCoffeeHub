// Shared admin panel JS utilities
// --- Sidebar Toggle Logic ---
(function() {
  const sidebar = document.getElementById('adminSidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const sidebarClose = document.getElementById('sidebarClose');
  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebarOverlay.classList.remove('hidden');
  }
  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
  }
  if (sidebar && sidebarToggle && sidebarOverlay && sidebarClose) {
    sidebarToggle.addEventListener('click', openSidebar);
    sidebarClose.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
  }
})();
// --- Barista Sidebar Toggle Logic ---
(function() {
  const sidebar = document.getElementById('baristaSidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const sidebarClose = document.getElementById('sidebarClose');
  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebarOverlay.classList.remove('hidden');
  }
  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
  }
  if (sidebar && sidebarToggle && sidebarOverlay && sidebarClose) {
    sidebarToggle.addEventListener('click', openSidebar);
    sidebarClose.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
  }
})();
// --- Customer Sidebar Toggle Logic ---
(function() {
  const sidebar = document.getElementById('customerSidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const sidebarClose = document.getElementById('sidebarClose');
  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebarOverlay.classList.remove('hidden');
  }
  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
  }
  if (sidebar && sidebarToggle && sidebarOverlay && sidebarClose) {
    sidebarToggle.addEventListener('click', openSidebar);
    sidebarClose.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
  }
})();
// Toast notification
window.showToast = function(message, type = 'info') {
  const container = document.getElementById('toast-container');
  if (!container) return;
  const toast = document.createElement('div');
  toast.className = `px-4 py-2 rounded shadow text-white font-semibold toast-${type}`;
  toast.style.background = type === 'success' ? '#009245' : (type === 'error' ? '#d32f2f' : '#1A1A1A');
  toast.textContent = message;
  container.appendChild(toast);
  setTimeout(() => { toast.remove(); }, 3500);
};
// Loading indicator
window.showLoading = function() {
  const el = document.getElementById('loading-indicator');
  if (el) el.classList.remove('hidden');
};
window.hideLoading = function() {
  const el = document.getElementById('loading-indicator');
  if (el) el.classList.add('hidden');
};
// Flash animation for table rows
(function(){
  if (!document.getElementById('row-flash-style')) {
    const style = document.createElement('style');
    style.id = 'row-flash-style';
    style.innerHTML = `@keyframes flash { 0%{opacity:1;} 50%{opacity:.5;} 100%{opacity:1;} } .animate-flash{animation:flash 1.2s;}`;
    document.head.appendChild(style);
  }
})();
// --- Audit Logs Reload Logic ---
window.reloadAuditLogs = function() {
  window.showLoading && window.showLoading();
  fetch('audit_logs_fetch.php')
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.text();
    })
    .then(html => {
      const tbody = document.querySelector('table tbody');
      const temp = document.createElement('tbody');
      temp.innerHTML = html;
      const newLogIds = Array.from(temp.querySelectorAll('tr')).map(row => row.children[0]?.textContent.trim());
      Array.from(temp.querySelectorAll('tr')).forEach((row, idx) => {
        const id = row.children[0]?.textContent.trim();
        // Optionally highlight new/changed rows
      });
      tbody.replaceWith(temp);
    })
    .catch(() => {
      window.showToast && window.showToast('Failed to reload logs.', 'error');
    })
    .finally(() => { window.hideLoading && window.hideLoading(); });
};
// --- Products Page Logic ---
window.fetchCategories = function() {
  const categories = [
    'Specialty Coffee',
    'Signature Beverages',
    'Rice Bowls',
    'Noodles',
    'Snacks',
    'Desserts'
  ];
  const select = document.getElementById('categoryFilter');
  if (select) {
    select.innerHTML = '<option value="">All Categories</option>' + categories.map(cat => `<option value="${cat}">${cat}</option>`).join('');
  }
};
window.fetchProducts = function(query = '', category = '', featured = '') {
  window.showLoading && window.showLoading();
  let url = `products_fetch.php?search=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}&featured=${encodeURIComponent(featured)}`;
  fetch(url)
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.json();
    })
    .then(data => {
      const tbody = document.getElementById('productsBody');
      if (!tbody) return;
      tbody.innerHTML = '';
      if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center p-4 text-gray-400">No products found.</td></tr>';
        window.lastProductIds = [];
        return;
      }
      data.forEach((product, idx) => {
        const id = String(product.id);
        let highlight = '';
        if (window.lastProductIds && window.lastProductIds.length && (!window.lastProductIds.includes(id) || window.lastProductIds[idx] !== id)) {
          highlight = 'bg-yellow-100 animate-flash';
          setTimeout(() => {
            const row = tbody.querySelector(`tr[data-id='${id}']`);
            if (row) row.classList.remove('bg-yellow-100', 'animate-flash');
          }, 1200);
        }
        tbody.innerHTML += `
          <tr class="border-b ${highlight}" data-id="${id}">
            <td class="px-4 py-2">${product.image_filename ? `<img src="../uploads/${product.image_filename}" alt="${product.name}" class="h-16 w-16 object-cover rounded"/>` : '<span class="text-gray-400">No Image</span>'}</td>
            <td class="px-4 py-2">${product.name}</td>
            <td class="px-4 py-2">${product.category}</td>
            <td class="px-4 py-2">₱${parseFloat(product.price).toFixed(2)}</td>
            <td class="px-4 py-2">${product.featured == 1 ? '✅' : '❌'}</td>
            <td class="px-4 py-2">${product.is_new == 1 ? 'Yes' : 'No'}</td>
            <td class="px-4 py-2 space-x-2">
              <a href="edit_product.php?id=${product.id}" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded"><i class="fas fa-edit"></i> Edit</a>
              <button data-delete-product="${product.id}" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"><i class="fas fa-trash"></i> Delete</button>
            </td>
          </tr>`;
      });
      window.lastProductIds = data.map(p => String(p.id));
    })
    .catch(() => {
      window.showToast && window.showToast('Failed to fetch products.', 'error');
    })
    .finally(() => { window.hideLoading && window.hideLoading(); });
};
document.addEventListener('click', function(e) {
  const btn = e.target.closest('[data-delete-product]');
  if (btn) {
    const id = btn.getAttribute('data-delete-product');
    if (confirm('Are you sure you want to delete this product?')) {
      window.showLoading && window.showLoading();
      fetch('products_delete.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          window.showToast && window.showToast('Product deleted!', 'success');
          window.fetchProducts && window.fetchProducts();
        } else {
          window.showToast && window.showToast(data.message || 'Delete failed.', 'error');
        }
      })
      .catch(() => {
        window.showToast && window.showToast('Failed to delete product.', 'error');
      })
      .finally(() => { window.hideLoading && window.hideLoading(); });
    }
  }
});
// --- Users Page Logic ---
document.addEventListener('click', function(e) {
  // Toggle user status
  const toggleBtn = e.target.closest('[data-toggle-user]');
  if (toggleBtn) {
    const id = toggleBtn.getAttribute('data-toggle-user');
    const currentStatus = toggleBtn.getAttribute('data-status');
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    if (confirm(`Are you sure you want to ${action} this user?`)) {
      window.showLoading && window.showLoading();
      fetch('users_toggle_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}&action=${action}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          window.showToast && window.showToast(`User ${action}d successfully!`, 'success');
          location.reload();
        } else {
          window.showToast && window.showToast(data.message || 'Action failed.', 'error');
        }
      })
      .catch(() => {
        window.showToast && window.showToast('Failed to update user status. Please check your connection.', 'error');
      })
      .finally(() => { window.hideLoading && window.hideLoading(); });
    }
  }
  // Show user profile modal
  const showProfileBtn = e.target.closest('[data-show-user-profile]');
  if (showProfileBtn) {
    const id = showProfileBtn.getAttribute('data-show-user-profile');
    const modal = document.getElementById('userProfileModal');
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      const content = document.getElementById('userProfileContent');
      if (content) {
        content.innerHTML = 'Loading...';
        setTimeout(() => {
          content.innerHTML = 'User details for ID ' + id + ' (implement AJAX fetch)';
        }, 500);
      }
    }
  }
  // Close user profile modal
  const closeProfileBtn = e.target.closest('[data-close-user-profile]');
  if (closeProfileBtn) {
    const modal = document.getElementById('userProfileModal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }
});
window.reloadUsers = function() {
  window.showLoading && window.showLoading();
  fetch('users_fetch.php')
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.text();
    })
    .then(html => {
      const tbody = document.getElementById('usersBody');
      if (tbody) tbody.innerHTML = html;
    })
    .catch(() => {
      window.showToast && window.showToast('Failed to reload users.', 'error');
    })
    .finally(() => { window.hideLoading && window.hideLoading(); });
};
// --- Orders Page Logic ---
document.addEventListener('change', function(e) {
  const statusSelect = e.target.closest('[data-update-order-status]');
  if (statusSelect) {
    const id = statusSelect.getAttribute('data-update-order-status');
    const status = statusSelect.value;
    window.showLoading && window.showLoading();
    fetch('update_order_status.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `id=${id}&status=${status}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        window.showToast && window.showToast('Order status updated!', 'success');
      } else {
        window.showToast && window.showToast(data.message || 'Update failed.', 'error');
      }
    })
    .catch(() => {
      window.showToast && window.showToast('Failed to update order status. Please check your connection.', 'error');
    })
    .finally(() => { window.hideLoading && window.hideLoading(); });
  }
});
document.addEventListener('submit', function(e) {
  const form = e.target.closest('#orderSearchForm');
  if (form) {
    e.preventDefault();
    window.showToast && window.showToast('Search/filter not yet implemented (demo only)', 'error');
  }
});
document.addEventListener('change', function(e) {
  const filter = e.target.closest('#statusFilter');
  if (filter) {
    window.showToast && window.showToast('Search/filter not yet implemented (demo only)', 'error');
  }
});
window.reloadOrders = function() {
  window.showLoading && window.showLoading();
  fetch('orders_fetch.php')
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.text();
    })
    .then(html => {
      const tbody = document.getElementById('ordersBody');
      const temp = document.createElement('tbody');
      temp.innerHTML = html;
      const newOrderIds = Array.from(temp.querySelectorAll('tr')).map(row => row.children[0]?.textContent.trim());
      Array.from(temp.querySelectorAll('tr')).forEach((row, idx) => {
        const id = row.children[0]?.textContent.trim();
        if (window.lastOrderIds && window.lastOrderIds.length && (!window.lastOrderIds.includes(id) || window.lastOrderIds[idx] !== id)) {
          row.classList.add('bg-yellow-100', 'animate-flash');
          setTimeout(() => row.classList.remove('bg-yellow-100', 'animate-flash'), 1200);
        }
      });
      if (tbody) tbody.innerHTML = temp.innerHTML;
      window.lastOrderIds = newOrderIds;
    })
    .catch(() => {
      window.showToast && window.showToast('Failed to reload orders. Please check your connection.', 'error');
    })
    .finally(() => { window.hideLoading && window.hideLoading(); });
};
// --- View Order Page Logic ---
document.addEventListener('change', function(e) {
  const statusSelect = e.target.closest('[data-update-order-status]');
  if (statusSelect) {
    const id = statusSelect.getAttribute('data-update-order-status');
    const status = statusSelect.value;
    window.showLoading && window.showLoading();
    fetch('update_order_status.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `id=${id}&status=${status}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        window.showToast && window.showToast('Order status updated!', 'success');
      } else {
        window.showToast && window.showToast(data.message || 'Update failed.', 'error');
      }
    })
    .catch(() => {
      window.showToast && window.showToast('Failed to update order status. Please check your connection.', 'error');
    })
    .finally(() => { window.hideLoading && window.hideLoading(); });
  }
});
// --- Barista Orders Page Logic ---
window.reloadBaristaOrders = function() {
  window.showLoading && window.showLoading();
  fetch('orders_fetch.php')
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.text();
    })
    .then(html => {
      const list = document.querySelector('.divide-y');
      const temp = document.createElement('ul');
      temp.innerHTML = html;
      const newOrderIds = Array.from(temp.querySelectorAll('li')).map(li => li.textContent.match(/Order #(\d+)/)?.[1]);
      Array.from(temp.querySelectorAll('li')).forEach((li, idx) => {
        const id = li.textContent.match(/Order #(\d+)/)?.[1];
        if (window.lastOrderIds && window.lastOrderIds.length && (!window.lastOrderIds.includes(id) || window.lastOrderIds[idx] !== id)) {
          li.classList.add('bg-yellow-100', 'animate-flash');
          setTimeout(() => li.classList.remove('bg-yellow-100', 'animate-flash'), 1200);
        }
      });
      if (list) list.innerHTML = temp.innerHTML;
      window.lastOrderIds = newOrderIds;
    })
    .catch(() => {
      window.showToast && window.showToast('Failed to reload orders.', 'error');
    })
    .finally(() => { window.hideLoading && window.hideLoading(); });
};
// --- Customer Order History Page Logic ---
window.reloadOrderHistory = function() {
  window.showLoading && window.showLoading();
  fetch('order_history_fetch.php')
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.text();
    })
    .then(html => {
      const list = document.querySelector('.divide-y');
      const temp = document.createElement('ul');
      temp.innerHTML = html;
      const newOrderIds = Array.from(temp.querySelectorAll('li')).map(li => li.textContent.match(/Order #(\d+)/)?.[1]);
      Array.from(temp.querySelectorAll('li')).forEach((li, idx) => {
        const id = li.textContent.match(/Order #(\d+)/)?.[1];
        if (window.lastOrderIds && window.lastOrderIds.length && (!window.lastOrderIds.includes(id) || window.lastOrderIds[idx] !== id)) {
          li.classList.add('bg-yellow-100', 'animate-flash');
          setTimeout(() => li.classList.remove('bg-yellow-100', 'animate-flash'), 1200);
        }
      });
      if (list) list.innerHTML = temp.innerHTML;
      window.lastOrderIds = newOrderIds;
    })
    .catch(() => {
      window.showToast && window.showToast('Failed to reload orders.', 'error');
    })
    .finally(() => { window.hideLoading && window.hideLoading(); });
};
// --- Live Update Polling Logic ---
(function() {
  // Utility: get page context
  function getPage() {
    if (document.getElementById('ordersBody')) return 'admin-orders';
    if (document.getElementById('usersBody')) return 'admin-users';
    if (document.querySelector('table.min-w-full.bg-white')) return 'admin-announcements';
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
      const parent = document.querySelector('h1, .page-title, .admin-title, .barista-title');
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
    if (page === 'admin-orders') window.reloadOrders && window.reloadOrders();
    if (page === 'admin-users') window.reloadUsers && window.reloadUsers();
    if (page === 'admin-announcements') window.reloadAnnouncements && window.reloadAnnouncements();
    if (page === 'barista-orders') window.reloadBaristaOrders && window.reloadBaristaOrders();
    if (page === 'customer-order-history') window.reloadOrderHistory && window.reloadOrderHistory();
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
  // Announcements reload for admin
  if (page === 'admin-announcements' && !window.reloadAnnouncements) {
    window.reloadAnnouncements = function() {
      window.showLoading && window.showLoading();
      fetch('announcements_fetch.php')
        .then(res => res.text())
        .then(html => {
          const tbody = document.querySelector('table.min-w-full.bg-white tbody');
          if (tbody) tbody.innerHTML = html;
        })
        .catch(() => { window.showToast && window.showToast('Failed to reload announcements.', 'error'); })
        .finally(() => { window.hideLoading && window.hideLoading(); });
    };
  }
  // Add indicator and start polling
  updateIndicator();
  startPolling();
})();
