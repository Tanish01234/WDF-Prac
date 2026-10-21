
(function () {
  'use strict';

  // Validation regex patterns
  const REGEX = {
    name: /^[A-Za-z\s]{3,50}$/,
    email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    mobile: /^[6-9]\d{9}$/,
    hasLower: /[a-z]/,
    hasUpper: /[A-Z]/,
    hasNumber: /\d/,
    hasSpecial: /[@$!%*?&#^()_+\-=\[\]{}|;:,.<>]/
  };

  document.addEventListener('DOMContentLoaded', () => {
    initRegistrationValidation();
    initContactValidation();
    initFeedbackValidation();
  });

  // Registration form validation
  function initRegistrationValidation() {
    const form = document.getElementById('registrationForm');
    if (!form) return;

    const nameInput = document.getElementById('regName');
    const emailInput = document.getElementById('regEmail');
    const mobileInput = document.getElementById('regMobile');
    const passwordInput = document.getElementById('regPassword');
    const confirmPasswordInput = document.getElementById('regConfirmPassword');
    const courseSelect = document.getElementById('regCourse');
    const yearSelect = document.getElementById('regYear');
    const termsCheck = document.getElementById('regTerms');
    const meterFill = document.querySelector('.meter-fill');
    const meterText = document.querySelector('.meter-status');

    // Real-time input listeners
    if (nameInput) {
      nameInput.addEventListener('input', () => validateField(nameInput, validateName));
      nameInput.addEventListener('blur', () => validateField(nameInput, validateName));
    }

    if (emailInput) {
      emailInput.addEventListener('input', () => validateField(emailInput, validateEmail));
      emailInput.addEventListener('blur', () => validateField(emailInput, validateEmail));
    }

    if (mobileInput) {
      mobileInput.addEventListener('input', () => validateField(mobileInput, validateMobile));
      mobileInput.addEventListener('blur', () => validateField(mobileInput, validateMobile));
    }

    if (passwordInput) {
      passwordInput.addEventListener('input', () => {
        updatePasswordStrength(passwordInput.value, meterFill, meterText);
        validateField(passwordInput, validatePassword);
        if (confirmPasswordInput && confirmPasswordInput.value) {
          validateField(confirmPasswordInput, () => validateConfirmPassword(passwordInput.value, confirmPasswordInput.value));
        }
      });
      passwordInput.addEventListener('blur', () => validateField(passwordInput, validatePassword));
    }

    if (confirmPasswordInput) {
      confirmPasswordInput.addEventListener('input', () => {
        validateField(confirmPasswordInput, () => validateConfirmPassword(passwordInput.value, confirmPasswordInput.value));
      });
      confirmPasswordInput.addEventListener('blur', () => {
        validateField(confirmPasswordInput, () => validateConfirmPassword(passwordInput.value, confirmPasswordInput.value));
      });
    }

    if (courseSelect) {
      courseSelect.addEventListener('change', () => validateField(courseSelect, validateSelection));
    }

    if (yearSelect) {
      yearSelect.addEventListener('change', () => validateField(yearSelect, validateSelection));
    }

    if (termsCheck) {
      termsCheck.addEventListener('change', () => validateField(termsCheck, validateTerms));
    }

    // Submit Handler
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      const isNameValid = validateField(nameInput, validateName);
      const isEmailValid = validateField(emailInput, validateEmail);
      const isMobileValid = validateField(mobileInput, validateMobile);
      const isPasswordValid = validateField(passwordInput, validatePassword);
      const isConfirmValid = validateField(confirmPasswordInput, () => validateConfirmPassword(passwordInput.value, confirmPasswordInput.value));
      const isCourseValid = validateField(courseSelect, validateSelection);
      const isYearValid = validateField(yearSelect, validateSelection);
      const isGenderValid = validateGender();
      const isTermsValid = validateField(termsCheck, validateTerms);

      const allValid = isNameValid && isEmailValid && isMobileValid && isPasswordValid &&
                       isConfirmValid && isCourseValid && isYearValid && isGenderValid && isTermsValid;

      if (allValid) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Create Synora Account';
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span>Saving to Storage...</span>';
        }

        const formData = new FormData(form);
        const endpoint = form.action || '../backend/forms/register.php';

        fetch(endpoint, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }

          if (data && data.success) {
            const successBanner = document.getElementById('regSuccessBanner');
            if (successBanner) {
              successBanner.style.display = 'block';
              successBanner.scrollIntoView({ behavior: 'smooth' });
            }
            if (window.showToast) {
              window.showToast(data.message || 'Registration recorded in storage successfully!', 'success', 5000);
            }
            form.reset();
            document.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
            if (meterFill) {
              meterFill.className = 'meter-fill';
              meterFill.style.width = '0%';
            }
            if (meterText) meterText.textContent = 'None';
          } else {
            const errMsg = data && data.message ? data.message : 'Server validation failed.';
            if (window.showToast) {
              window.showToast(errMsg, 'error', 5000);
            }
          }
        })
        .catch(() => {
          // Graceful fallback for static/offline preview without local PHP server
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
          const successBanner = document.getElementById('regSuccessBanner');
          if (successBanner) {
            successBanner.style.display = 'block';
            successBanner.scrollIntoView({ behavior: 'smooth' });
          }
          if (window.showToast) {
            window.showToast(`Validated: ${nameInput.value}! (Start local PHP server to append to registrations.csv)`, 'info', 5000);
          }
          form.reset();
          document.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
          if (meterFill) {
            meterFill.className = 'meter-fill';
            meterFill.style.width = '0%';
          }
          if (meterText) meterText.textContent = 'None';
        });
      } else {
        if (window.showToast) {
          window.showToast('Please fix the errors highlighted in the registration form.', 'error');
        }
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) firstInvalid.focus();
      }
    });
  }

  // Validation rules
  function validateName(val) {
    if (!val || !val.trim()) return { valid: false, message: 'Full name is required.' };
    if (!REGEX.name.test(val.trim())) return { valid: false, message: 'Name must contain 3-50 letters only.' };
    return { valid: true, message: 'Looks good!' };
  }

  function validateEmail(val) {
    if (!val || !val.trim()) return { valid: false, message: 'Email address is required.' };
    if (!REGEX.email.test(val.trim())) return { valid: false, message: 'Please enter a valid academic/personal email.' };
    return { valid: true, message: 'Valid email address!' };
  }

  function validateMobile(val) {
    if (!val || !val.trim()) return { valid: false, message: 'Mobile number is required.' };
    const cleaned = val.replace(/[\s\-]/g, '');
    if (!REGEX.mobile.test(cleaned)) return { valid: false, message: 'Enter a valid 10-digit Indian mobile number (e.g. 9876543210).' };
    return { valid: true, message: 'Valid mobile number!' };
  }

  function validatePassword(val) {
    if (!val) return { valid: false, message: 'Password is required.' };
    if (val.length < 8) return { valid: false, message: 'Password must be at least 8 characters long.' };
    if (!REGEX.hasUpper.test(val)) return { valid: false, message: 'Must contain at least one uppercase letter (A-Z).' };
    if (!REGEX.hasLower.test(val)) return { valid: false, message: 'Must contain at least one lowercase letter (a-z).' };
    if (!REGEX.hasNumber.test(val)) return { valid: false, message: 'Must contain at least one digit (0-9).' };
    if (!REGEX.hasSpecial.test(val)) return { valid: false, message: 'Must contain at least one special symbol (@$!%*?&#).' };
    return { valid: true, message: 'Strong password requirement met!' };
  }

  function validateConfirmPassword(password, confirm) {
    if (!confirm) return { valid: false, message: 'Please confirm your password.' };
    if (password !== confirm) return { valid: false, message: 'Passwords do not match.' };
    return { valid: true, message: 'Passwords match!' };
  }

  function validateSelection(val) {
    if (!val || val === '' || val === 'default') return { valid: false, message: 'Please make a valid selection.' };
    return { valid: true, message: '' };
  }

  function validateTerms(input) {
    if (!input.checked) return { valid: false, message: 'You must accept the terms and campus policy.' };
    return { valid: true, message: '' };
  }

  function validateGender() {
    const genders = document.querySelectorAll('input[name="gender"]');
    const feedback = document.getElementById('genderFeedback');
    let checked = false;
    genders.forEach(g => { if (g.checked) checked = true; });

    if (!checked) {
      if (feedback) {
        feedback.textContent = 'Please select a gender.';
        feedback.className = 'form-feedback error';
      }
      return false;
    } else {
      if (feedback) {
        feedback.textContent = '';
        feedback.className = 'form-feedback';
      }
      return true;
    }
  }

  function validateField(input, validator) {
    if (!input) return true;
    const value = input.type === 'checkbox' ? input : input.value;
    const result = validator(value);
    const feedbackEl = document.getElementById(`${input.id}Feedback`);

    if (result.valid) {
      input.classList.remove('is-invalid');
      input.classList.add('is-valid');
      if (feedbackEl) {
        feedbackEl.textContent = result.message || '';
        feedbackEl.className = 'form-feedback success';
      }
      return true;
    } else {
      input.classList.remove('is-valid');
      input.classList.add('is-invalid');
      if (feedbackEl) {
        feedbackEl.textContent = result.message;
        feedbackEl.className = 'form-feedback error';
      }
      return false;
    }
  }

  // Password strength meter
  function updatePasswordStrength(val, barEl, textEl) {
    if (!barEl || !textEl) return;
    if (!val) {
      barEl.className = 'meter-fill';
      barEl.style.width = '0%';
      textEl.textContent = 'None';
      return;
    }

    let score = 0;
    if (val.length >= 8) score++;
    if (REGEX.hasLower.test(val) && REGEX.hasUpper.test(val)) score++;
    if (REGEX.hasNumber.test(val)) score++;
    if (REGEX.hasSpecial.test(val)) score++;

    barEl.className = 'meter-fill';
    if (score <= 1) {
      barEl.classList.add('weak');
      textEl.textContent = 'Weak (Needs symbols & mixed case)';
      textEl.style.color = 'var(--danger)';
    } else if (score === 2) {
      barEl.classList.add('fair');
      textEl.textContent = 'Fair (Add digits or symbols)';
      textEl.style.color = 'var(--warning)';
    } else if (score === 3) {
      barEl.classList.add('good');
      textEl.textContent = 'Good (Almost optimal)';
      textEl.style.color = 'var(--info)';
    } else {
      barEl.classList.add('strong');
      textEl.textContent = 'Strong (Excellent complexity)';
      textEl.style.color = 'var(--success)';
    }
  }

  // Contact form validation
  function initContactValidation() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('contactName');
      const email = document.getElementById('contactEmail');
      const subject = document.getElementById('contactSubject');
      const message = document.getElementById('contactMessage');

      const isNameValid = validateField(name, validateName);
      const isEmailValid = validateField(email, validateEmail);
      const isSubjectValid = validateField(subject, val => val && val.trim().length >= 4 ? { valid: true } : { valid: false, message: 'Subject must be at least 4 characters.' });
      const isMessageValid = validateField(message, val => val && val.trim().length >= 10 ? { valid: true } : { valid: false, message: 'Message must be at least 10 characters.' });

      if (isNameValid && isEmailValid && isSubjectValid && isMessageValid) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : 'Submit Inquiry →';
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span>Sending Inquiry...</span>';
        }

        const formData = new FormData(form);
        const endpoint = form.action || '../backend/forms/contact.php';

        fetch(endpoint, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
          if (data && data.success) {
            if (window.showToast) {
              window.showToast(data.message || 'Your inquiry has been sent to the IT Department desk.', 'success', 5000);
            }
            form.reset();
            form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
          } else {
            if (window.showToast) {
              window.showToast(data.message || 'Unable to submit inquiry.', 'error');
            }
          }
        })
        .catch(() => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
          if (window.showToast) {
            window.showToast('Thank you! Inquiry validated. (Start local PHP server to save to contacts.csv)', 'info', 5000);
          }
          form.reset();
          form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
        });
      }
    });
  }

  // Feedback form validation
  function initFeedbackValidation() {
    const form = document.getElementById('feedbackForm');
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('fbName');
      const email = document.getElementById('fbEmail');
      const category = document.getElementById('fbCategory');
      const comments = document.getElementById('fbComments');

      const isNameValid = validateField(name, validateName);
      const isEmailValid = validateField(email, validateEmail);
      const isCatValid = validateField(category, validateSelection);
      const isCommentsValid = validateField(comments, val => val && val.trim().length >= 10 ? { valid: true } : { valid: false, message: 'Please provide at least 10 characters of feedback.' });

      if (isNameValid && isEmailValid && isCatValid && isCommentsValid) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : 'Submit Feedback →';
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span>Saving Feedback...</span>';
        }

        const formData = new FormData(form);
        const endpoint = form.action || '../backend/forms/feedback.php';

        fetch(endpoint, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
          if (data && data.success) {
            if (window.showToast) {
              window.showToast(data.message || 'Your feedback has been recorded in feedback.json.', 'success', 5000);
            }
            form.reset();
            form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
          } else {
            if (window.showToast) {
              window.showToast(data.message || 'Unable to record feedback.', 'error');
            }
          }
        })
        .catch(() => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
          if (window.showToast) {
            window.showToast('Thank you! Feedback validated. (Start local PHP server to save to feedback.json)', 'info', 5000);
          }
          form.reset();
          form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
        });
      }
    });
  }
})();
