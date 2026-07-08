/* Mobile navigation — the table of contents drawer */
(function () {
  "use strict";
  var t = document.getElementById("menuToggle");
  var n = document.getElementById("siteNav");
  if (!t || !n) return;

  var mq = window.matchMedia("(max-width: 759px)");

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
  n.addEventListener("click", function (e) { if (e.target.closest("a")) set(false); });
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
})();
