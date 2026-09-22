document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('navToggle');
  const nav = document.getElementById('mainNav');
  if (toggle && nav) toggle.addEventListener('click', () => nav.classList.toggle('open'));

  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  const showPass = document.getElementById('showPass');
  if (showPass) {
    showPass.addEventListener('change', () => {
      document.querySelectorAll('#regPassword, input[name="confirm_password"]').forEach(i => i.type = showPass.checked ? 'text' : 'password');
    });
  }

  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', e => {
      const p = registerForm.querySelector('input[name="password"]');
      const cp = registerForm.querySelector('input[name="confirm_password"]');
      if (p && cp && p.value !== cp.value) { e.preventDefault(); alert('Passwords do not match.'); cp.focus(); }
    });
  }

  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => { alert.style.opacity = '0'; alert.style.transform = 'translateY(-6px)'; setTimeout(() => alert.remove(), 400); }, 4500);
  });
});
