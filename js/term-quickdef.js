/* Quick definitions for glossary terms (.term-ref links).
   A plain click opens a small panel (desktop) or bottom sheet (mobile)
   populated from the #termData JSON. The links are real anchors to
   /glossary#slug, so everything works without JavaScript, and
   modifier clicks / middle clicks / new-tab all pass through untouched. */
(function () {
  "use strict";

  var host = document.getElementById("termQuickdef");
  var dataEl = document.getElementById("termData");
  if (!host || !dataEl) return;

  var data;
  try { data = JSON.parse(dataEl.textContent); } catch (err) { return; }

  var panel = host.querySelector(".term-quickdef__panel");
  var backdrop = host.querySelector(".term-quickdef__backdrop");
  var closeBtn = host.querySelector(".term-quickdef__close");
  var titleEl = host.querySelector(".term-quickdef__term");
  var defEl = host.querySelector(".term-quickdef__def");
  var contexts = host.querySelector(".term-quickdef__contexts");
  var moreEl = host.querySelector(".term-quickdef__more");
  var mq = window.matchMedia("(max-width: 759px)");
  var CONTEXT_LIMIT = 220;
  var opener = null;

  function fill(slot, text) {
    var dt = contexts.querySelector('[data-qd="' + slot + '-dt"]');
    var dd = contexts.querySelector('[data-qd="' + slot + '"]');
    var show = !!text && text.length <= CONTEXT_LIMIT;
    dt.hidden = dd.hidden = !show;
    dd.textContent = show ? text : "";
    return show;
  }

  function positionPanel(link) {
    if (mq.matches) { panel.style.top = panel.style.left = ""; return; }
    var rect = link.getBoundingClientRect();
    var width = 340;
    var left = Math.min(rect.right + 14, window.innerWidth - width - 16);
    var top = Math.max(16, Math.min(rect.top - 8, window.innerHeight - 320));
    panel.style.left = Math.max(16, left) + "px";
    panel.style.top = top + "px";
  }

  function open(link) {
    var entry = data[link.getAttribute("data-term")];
    if (!entry) return false;

    opener = link;
    titleEl.textContent = entry.term;
    defEl.textContent = entry.definition;
    var any = fill("business", entry.business);
    any = fill("church", entry.church) || any;
    contexts.hidden = !any;
    moreEl.setAttribute("href", entry.url);

    host.hidden = false;
    host.classList.toggle("is-sheet", mq.matches);
    positionPanel(link);
    if (mq.matches) document.body.classList.add("term-sheet-open");
    panel.focus();
    return true;
  }

  function close() {
    host.hidden = true;
    host.classList.remove("is-sheet");
    document.body.classList.remove("term-sheet-open");
    if (opener) { opener.focus(); opener = null; }
  }

  document.querySelectorAll(".term-ref").forEach(function (link) {
    link.addEventListener("click", function (e) {
      /* modifier or non-primary clicks fall through to the glossary page */
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
      if (open(link)) e.preventDefault();
    });
  });

  closeBtn.addEventListener("click", close);
  backdrop.addEventListener("click", close);

  document.addEventListener("keydown", function (e) {
    if (host.hidden) return;
    if (e.key === "Escape") { e.preventDefault(); close(); return; }
    if (e.key === "Tab") {
      /* keep keyboard focus inside the panel while it is open */
      var focusables = panel.querySelectorAll("a[href], button");
      if (!focusables.length) return;
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      if (e.shiftKey && (document.activeElement === first || document.activeElement === panel)) {
        e.preventDefault(); last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault(); first.focus();
      }
    }
  });

  window.addEventListener("resize", function () {
    if (!host.hidden) close();
  });
})();
