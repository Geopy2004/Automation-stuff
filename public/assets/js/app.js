const body = document.body;
const menuToggle = document.querySelector('.menu-toggle');
const sidebarBackdrop = document.querySelector('.sidebar-backdrop');

function setMenu(open) {
  body.classList.toggle('menu-open', open);
  if (menuToggle) {
    menuToggle.setAttribute('aria-expanded', String(open));
    menuToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
  }
}

function showLoading() {
  body.classList.add('is-loading');
}

menuToggle?.addEventListener('click', () => {
  setMenu(!body.classList.contains('menu-open'));
});

sidebarBackdrop?.addEventListener('click', () => setMenu(false));

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    setMenu(false);
  }
});

document.querySelectorAll('.sidebar a').forEach((link) => {
  link.addEventListener('click', () => {
    setMenu(false);
    showLoading();
  });
});

document.querySelectorAll('a[href]').forEach((link) => {
  link.addEventListener('click', () => {
    const href = link.getAttribute('href') || '';
    const isDownload = link.hasAttribute('download');
    const isExternal = href.startsWith('http') && !href.startsWith(window.location.origin);
    const isAnchor = href.startsWith('#');
    if (!isDownload && !isExternal && !isAnchor) {
      showLoading();
    }
  });
});

document.querySelectorAll('form').forEach((form) => {
  form.addEventListener('submit', () => {
    const button = form.querySelector('button[type="submit"]');
    if (button) {
      button.disabled = true;
      button.dataset.originalText = button.textContent || '';
      button.textContent = 'Loading...';
    }
    showLoading();
  });
});

document.querySelectorAll('.alert').forEach((alert) => {
  window.setTimeout(() => alert.classList.add('is-fading'), 4500);
  window.setTimeout(() => alert.remove(), 4900);
});
