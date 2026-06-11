document.querySelectorAll('.alert').forEach((alert) => {
  window.setTimeout(() => alert.classList.add('is-fading'), 4500);
});
