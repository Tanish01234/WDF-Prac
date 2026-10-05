
(function () {
  'use strict';

  // Debounce helper
  window.debounce = function (func, wait = 250) {
    let timeout;
    return function (...args) {
      const context = this;
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(context, args), wait);
    };
  };

  // String matching helper
  window.stringMatches = function (sourceText, query) {
    if (!sourceText || !query) return false;
    return sourceText.toLowerCase().includes(query.trim().toLowerCase());
  };
})();
