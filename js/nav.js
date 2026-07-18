/* Microsoft Clarity — site-wide behavior analytics */
(function (c, l, a, r, i, t, y) {
  c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments); };
  t = l.createElement(r);
  t.async = 1;
  t.src = "https://www.clarity.ms/tag/" + i;
  y = l.getElementsByTagName(r)[0];
  y.parentNode.insertBefore(t, y);
})(window, document, "clarity", "script", "xnj6ziehcb");

/* Mobile navigation — the table of contents drawer */
(function () {
  "use strict";
  var t = document.getElementById("menuToggle");
  var n = document.getElementById("siteNav");
  if (!t || !n) return;

  var mq = window.matchMedia("(max-width: 759px)");
  var dropdowns = Array.prototype.slice.call(n.querySelectorAll(".nav-dropdown"));

  function set(open) {
    open = open && mq.matches;
    n.classList.toggle("open", open);
    n.setAttribute("aria-hidden", open ? "false" : "true");
    t.setAttribute("aria-expanded", open ? "true" : "false");
    t.textContent = open ? "CLOSE" : "MENU";
    document.body.classList.toggle("nav-open", open);
  }

  n.setAttribute("aria-hidden", mq.matches ? "true" : "false");
  t.addEventListener("click", function () { set(!n.classList.contains("open")); });
  n.addEventListener("click", function (e) {
    if (e.target.closest(".nav-dropdown-toggle")) return;
    if (e.target.closest("a")) set(false);
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && n.classList.contains("open")) { set(false); t.focus(); }
  });
  function handleViewportChange(e) {
    if (e.matches) {
      n.setAttribute("aria-hidden", n.classList.contains("open") ? "false" : "true");
    } else {
      set(false);
      n.setAttribute("aria-hidden", "false");
    }
  }

  if (mq.addEventListener) mq.addEventListener("change", handleViewportChange);
  else if (mq.addListener) mq.addListener(handleViewportChange);

  function setDropdown(dropdown, open) {
    var toggle = dropdown.querySelector(".nav-dropdown-toggle");
    dropdown.classList.toggle("is-open", open);
    if (toggle) toggle.setAttribute("aria-expanded", open ? "true" : "false");
  }

  dropdowns.forEach(function (dropdown) {
    var toggle = dropdown.querySelector(".nav-dropdown-toggle");
    if (!toggle) return;

    dropdown.addEventListener("mouseenter", function () {
      if (!mq.matches) setDropdown(dropdown, true);
    });
    dropdown.addEventListener("mouseleave", function () {
      if (!mq.matches) setDropdown(dropdown, false);
    });
    dropdown.addEventListener("focusin", function () {
      if (!mq.matches) setDropdown(dropdown, true);
    });
    dropdown.addEventListener("focusout", function (e) {
      if (!mq.matches && !dropdown.contains(e.relatedTarget)) setDropdown(dropdown, false);
    });
    toggle.addEventListener("click", function (e) {
      if (!mq.matches) return;
      e.preventDefault();
      var opening = !dropdown.classList.contains("is-open");
      dropdowns.forEach(function (other) { setDropdown(other, other === dropdown && opening); });
    });
  });

  document.addEventListener("click", function (e) {
    dropdowns.forEach(function (dropdown) {
      if (!dropdown.contains(e.target)) setDropdown(dropdown, false);
    });
  });
  document.addEventListener("keydown", function (e) {
    if (e.key !== "Escape") return;
    dropdowns.forEach(function (dropdown) {
      if (!dropdown.classList.contains("is-open")) return;
      var toggle = dropdown.querySelector(".nav-dropdown-toggle");
      setDropdown(dropdown, false);
      if (toggle) toggle.focus();
    });
  });

})();
