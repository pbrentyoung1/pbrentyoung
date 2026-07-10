/* Article-page enhancements. The complete article is rendered by PHP. */
(function () {
  "use strict";

  var copyButton = document.getElementById("copyLink");
  if (!copyButton) return;

  function showCopied() {
    var note = document.getElementById("copiedNote");
    if (!note) return;
    note.hidden = false;
    window.setTimeout(function () { note.hidden = true; }, 1600);
  }

  copyButton.addEventListener("click", function () {
    var url = copyButton.getAttribute("data-url") || window.location.href;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(showCopied);
      return;
    }

    var field = document.createElement("textarea");
    field.value = url;
    field.setAttribute("readonly", "");
    field.style.position = "fixed";
    field.style.opacity = "0";
    document.body.appendChild(field);
    field.select();
    try { document.execCommand("copy"); showCopied(); } catch (error) { /* no-op */ }
    field.remove();
  });
})();
