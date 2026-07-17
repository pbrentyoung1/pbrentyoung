/* A Working Glossary — copy-link controls.
   The anchors work without JavaScript (they are plain hash links);
   this layer adds copy-to-clipboard with visible + spoken feedback. */
(function () {
  "use strict";

  var live = document.getElementById("glossLive");

  function announce(text) {
    if (live) { live.textContent = ""; window.setTimeout(function () { live.textContent = text; }, 30); }
  }

  document.querySelectorAll(".gloss-anchor").forEach(function (anchor) {
    anchor.addEventListener("click", function (e) {
      var url = anchor.getAttribute("data-url") || (location.origin + location.pathname + anchor.getAttribute("href"));
      if (!(navigator.clipboard && navigator.clipboard.writeText)) return; /* no clipboard: behave as a normal hash link */
      e.preventDefault();
      history.replaceState(null, "", anchor.getAttribute("href"));
      navigator.clipboard.writeText(url).then(function () {
        var note = anchor.querySelector(".gloss-copied");
        if (note) {
          note.hidden = false;
          window.setTimeout(function () { note.hidden = true; }, 1600);
        }
        var term = anchor.getAttribute("aria-label") || "";
        announce(term.replace(/^Copy link to /i, "Link to ") + " copied");
      });
    });
  });
})();
