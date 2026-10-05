
(function () {
  'use strict';

  const PROFILE_STORAGE_KEY = 'synora-profile';

  document.addEventListener('DOMContentLoaded', () => {
    initProfile();
  });

  function initProfile() {
    const editBtn = document.getElementById('editProfileBtn');
    const profileForm = document.getElementById('profileEditForm');

    loadStoredProfile();

    if (editBtn) {
      editBtn.addEventListener('click', () => {
        populateEditModal();
        if (window.openModal) {
          window.openModal('editProfileModal');
        }
      });
    }

    if (profileForm) {
      profileForm.addEventListener('submit', (e) => {
        e.preventDefault();
        saveProfile();
      });
    }
  }

  function populateEditModal() {
    const nameEl = document.getElementById('profileNameDisplay');
    const emailEl = document.getElementById('profileEmailDisplay');
    const mobileEl = document.getElementById('profileMobileDisplay');
    const bioEl = document.getElementById('profileBioDisplay');

    const editName = document.getElementById('editProfileName');
    const editEmail = document.getElementById('editProfileEmail');
    const editMobile = document.getElementById('editProfileMobile');
    const editBio = document.getElementById('editProfileBio');

    if (editName && nameEl) editName.value = nameEl.textContent.trim();
    if (editEmail && emailEl) editEmail.value = emailEl.textContent.trim();
    if (editMobile && mobileEl) editMobile.value = mobileEl.textContent.trim();
    if (editBio && bioEl) editBio.value = bioEl.textContent.trim();
  }

  function saveProfile() {
    const editName = document.getElementById('editProfileName');
    const editEmail = document.getElementById('editProfileEmail');
    const editMobile = document.getElementById('editProfileMobile');
    const editBio = document.getElementById('editProfileBio');

    if (!editName || !editEmail) return;

    const profileData = {
      name: editName.value.trim() || 'Tanis Bedia',
      email: editEmail.value.trim() || '25dit006@charusat.edu.in',
      mobile: editMobile ? editMobile.value.trim() : '+91 98765 43210',
      bio: editBio ? editBio.value.trim() : '3rd Semester B.Tech Information Technology student at DEPSTAR, CHARUSAT.'
    };

    localStorage.setItem(PROFILE_STORAGE_KEY, JSON.stringify(profileData));
    applyProfileData(profileData);

    if (window.closeModal) {
      window.closeModal('editProfileModal');
    }

    if (window.showToast) {
      window.showToast('Profile updated successfully (Frontend Demo)!', 'success');
    }
  }

  function loadStoredProfile() {
    const saved = localStorage.getItem(PROFILE_STORAGE_KEY);
    if (!saved) return;
    try {
      const data = JSON.parse(saved);
      applyProfileData(data);
    } catch (e) {
      console.error('Error parsing stored profile', e);
    }
  }

  function applyProfileData(data) {
    const nameEl = document.getElementById('profileNameDisplay');
    const emailEl = document.getElementById('profileEmailDisplay');
    const mobileEl = document.getElementById('profileMobileDisplay');
    const bioEl = document.getElementById('profileBioDisplay');
    const sidebarNameEl = document.querySelector('.student-name');

    if (nameEl && data.name) nameEl.textContent = data.name;
    if (emailEl && data.email) emailEl.textContent = data.email;
    if (mobileEl && data.mobile) mobileEl.textContent = data.mobile;
    if (bioEl && data.bio) bioEl.textContent = data.bio;
    if (sidebarNameEl && data.name) sidebarNameEl.textContent = data.name;
  }
})();
