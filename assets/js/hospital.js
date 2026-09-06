/**
 * VaxCare - Hospital Panel Client Script
 * Dark/Light Mode Switcher, Status Modals, and Search Filter
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Dark / Light Mode Switcher (Persisted with localStorage)
  const themeToggleBtn = document.getElementById('themeToggleBtn');
  const currentTheme = localStorage.getItem('vaxcare_theme') || 'light';
  
  document.documentElement.setAttribute('data-bs-theme', currentTheme);
  updateThemeIcon(currentTheme);

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      const activeTheme = document.documentElement.getAttribute('data-bs-theme');
      const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
      
      document.documentElement.setAttribute('data-bs-theme', newTheme);
      localStorage.setItem('vaxcare_theme', newTheme);
      updateThemeIcon(newTheme);
    });
  }

  function updateThemeIcon(theme) {
    if (!themeToggleBtn) return;
    const icon = themeToggleBtn.querySelector('i');
    if (!icon) return;
    if (theme === 'dark') {
      icon.className = 'bi bi-sun-fill text-warning';
    } else {
      icon.className = 'bi bi-moon-fill';
    }
  }

  // 2. Mobile Sidebar Toggle
  const sidebar = document.getElementById('appSidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarBackdrop = document.getElementById('sidebarBackdrop');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('show');
      if (sidebarBackdrop) sidebarBackdrop.classList.toggle('d-none');
    });
  }

  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', () => {
      sidebar.classList.remove('show');
      sidebarBackdrop.classList.add('d-none');
    });
  }

  // 3. Update Vaccination Status Modal
  const statusModal = document.getElementById('updateStatusModal');
  if (statusModal) {
    statusModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;

      const bookingId = button.getAttribute('data-booking-id') || '';
      const childName = button.getAttribute('data-child-name') || '';
      const vaccine = button.getAttribute('data-vaccine') || '';
      const parent = button.getAttribute('data-parent') || '';
      const currentStatus = button.getAttribute('data-current-status') || 'pending';

      statusModal.querySelector('#statusBookingId').value = bookingId;
      statusModal.querySelector('#modalBookingIdDisplay').textContent = '#' + bookingId;
      statusModal.querySelector('#modalChildName').textContent = childName;
      statusModal.querySelector('#modalVaccineName').textContent = vaccine;
      statusModal.querySelector('#modalParentName').textContent = parent;

      const vaccinatedRadio = statusModal.querySelector('#statusVaccinated');
      const notVaccinatedRadio = statusModal.querySelector('#statusNotVaccinated');
      const vaccinatedSection = statusModal.querySelector('#vaccinatedDetailsSection');
      const notVaccinatedSection = statusModal.querySelector('#notVaccinatedReasonSection');

      if (currentStatus === 'vaccinated') {
        if (vaccinatedRadio) vaccinatedRadio.checked = true;
        if (vaccinatedSection) vaccinatedSection.classList.remove('d-none');
        if (notVaccinatedSection) notVaccinatedSection.classList.add('d-none');
      } else if (currentStatus === 'not_vaccinated') {
        if (notVaccinatedRadio) notVaccinatedRadio.checked = true;
        if (vaccinatedSection) vaccinatedSection.classList.add('d-none');
        if (notVaccinatedSection) notVaccinatedSection.classList.remove('d-none');
      } else {
        if (vaccinatedRadio) vaccinatedRadio.checked = true;
        if (vaccinatedSection) vaccinatedSection.classList.remove('d-none');
        if (notVaccinatedSection) notVaccinatedSection.classList.add('d-none');
      }
    });

    const statusRadios = statusModal.querySelectorAll('input[name="vaccine_status"]');
    statusRadios.forEach(radio => {
      radio.addEventListener('change', function () {
        const vaccinatedSection = statusModal.querySelector('#vaccinatedDetailsSection');
        const notVaccinatedSection = statusModal.querySelector('#notVaccinatedReasonSection');

        if (this.value === 'vaccinated') {
          vaccinatedSection.classList.remove('d-none');
          notVaccinatedSection.classList.add('d-none');
        } else {
          vaccinatedSection.classList.add('d-none');
          notVaccinatedSection.classList.remove('d-none');
        }
      });
    });
  }

  // 4. Live Table Search Filter
  const tableSearchInput = document.getElementById('tableSearchInput');
  if (tableSearchInput) {
    tableSearchInput.addEventListener('keyup', function () {
      const query = this.value.toLowerCase();
      const rows = document.querySelectorAll('#vaxcareTableBody tr');

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  }

  // 5. Password Toggle
  const toggleBtns = document.querySelectorAll('.toggle-password-btn');
  toggleBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.getAttribute('data-target');
      const input = document.getElementById(targetId);
      const icon = this.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  });
});
