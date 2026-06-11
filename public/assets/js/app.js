const body = document.body;
const menuToggle = document.querySelector('.menu-toggle');
const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
const loadingDuration = 3000;
const loaderPhases = ['Loading', 'Preparing', 'Opening', 'Almost ready'];
let loadingFrame = 0;
let loadingStartedAt = 0;

function setMenu(open) {
  body.classList.toggle('menu-open', open);
  if (menuToggle) {
    menuToggle.setAttribute('aria-expanded', String(open));
    menuToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
  }
}

function showLoading() {
  window.cancelAnimationFrame(loadingFrame);
  loadingStartedAt = performance.now();
  body.classList.add('is-loading');

  document.querySelectorAll('.loader').forEach((loader) => {
    const text = loader.querySelector('.loader-text');
    loader.classList.remove('is-primed');
    loader.style.setProperty('--loader-progress', '0%');
    if (text) {
      text.textContent = loaderPhases[0];
    }

    window.requestAnimationFrame(() => {
      loader.classList.add('is-primed');
    });
  });

  updateLoadingEffect(loadingStartedAt);
}

function updateLoadingEffect(now) {
  const elapsed = Math.min(now - loadingStartedAt, loadingDuration);
  const progress = elapsed / loadingDuration;
  const progressPercent = `${Math.round(progress * 100)}%`;
  const phaseIndex = Math.min(loaderPhases.length - 1, Math.floor(progress * loaderPhases.length));

  document.querySelectorAll('.loader').forEach((loader) => {
    const text = loader.querySelector('.loader-text');
    loader.style.setProperty('--loader-progress', progressPercent);
    if (text) {
      text.textContent = loaderPhases[phaseIndex];
    }
  });

  if (elapsed < loadingDuration) {
    loadingFrame = window.requestAnimationFrame(updateLoadingEffect);
  }
}

function shouldDelayLink(link) {
  const href = link.getAttribute('href') || '';
  const target = link.getAttribute('target') || '';
  const isDownload = link.hasAttribute('download');
  const isExternal = href.startsWith('http') && !href.startsWith(window.location.origin);
  const isAnchor = href.startsWith('#');
  return href && !isDownload && !isExternal && !isAnchor && target !== '_blank';
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

document.querySelectorAll('a[href]').forEach((link) => {
  link.addEventListener('click', (event) => {
    if (!shouldDelayLink(link)) {
      return;
    }

    event.preventDefault();
    setMenu(false);
    showLoading();
    window.setTimeout(() => {
      window.location.href = link.href;
    }, loadingDuration);
  });
});

document.querySelectorAll('form').forEach((form) => {
  form.addEventListener('submit', (event) => {
    if (form.dataset.loadingSubmit === 'true') {
      return;
    }

    event.preventDefault();
    const button = form.querySelector('button[type="submit"]');
    if (button) {
      button.disabled = true;
      button.dataset.originalText = button.textContent || '';
      button.textContent = 'Loading...';
    }
    showLoading();

    window.setTimeout(() => {
      form.dataset.loadingSubmit = 'true';
      form.submit();
    }, loadingDuration);
  });
});

document.querySelectorAll('.alert').forEach((alert) => {
  window.setTimeout(() => alert.classList.add('is-fading'), 4500);
  window.setTimeout(() => alert.remove(), 4900);
});
