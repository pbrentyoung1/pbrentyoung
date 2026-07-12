/* Article-page enhancements. The complete article is rendered by PHP. */
(function () {
  "use strict";

  var copyButtons = document.querySelectorAll(".copy-link");
  if (!copyButtons.length) return;

  function showCopied(button) {
    var note = button.parentNode.querySelector(".share-copied");
    if (!note) return;
    note.hidden = false;
    window.setTimeout(function () { note.hidden = true; }, 1600);
  }

  copyButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      var url = button.getAttribute("data-url") || window.location.href;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function () { showCopied(button); });
        return;
      }

      var field = document.createElement("textarea");
      field.value = url;
      field.setAttribute("readonly", "");
      field.style.position = "fixed";
      field.style.opacity = "0";
      document.body.appendChild(field);
      field.select();
      try { document.execCommand("copy"); showCopied(button); } catch (error) { /* no-op */ }
      field.remove();
    });
  });
})();
