/* Mobile navigation — the table of contents drawer */
(function () {
  "use strict";
  var t = document.getElementById("menuToggle");
  var n = document.getElementById("siteNav");
  if (!t || !n) return;

  var mq = window.matchMedia("(max-width: 759px)");
  var blog = n.querySelector(".nav-blog");
  var blogToggle = blog && blog.querySelector(".nav-blog-toggle");

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
    if (e.target.closest(".nav-blog-toggle")) return;
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

  /* The BLOG menu is a taxonomy view, not a second content source. */
  if (blog && blogToggle) {
    var setBlog = function (open) {
      blog.classList.toggle("is-open", open);
      blogToggle.setAttribute("aria-expanded", open ? "true" : "false");
    };

    blog.addEventListener("mouseenter", function () { if (!mq.matches) setBlog(true); });
    blog.addEventListener("mouseleave", function () { if (!mq.matches) setBlog(false); });
    blog.addEventListener("focusin", function () { if (!mq.matches) setBlog(true); });
    blog.addEventListener("focusout", function (e) {
      if (!mq.matches && !blog.contains(e.relatedTarget)) setBlog(false);
    });
    blogToggle.addEventListener("click", function (e) {
      if (!mq.matches) return;
      e.preventDefault();
      setBlog(!blog.classList.contains("is-open"));
    });
    document.addEventListener("click", function (e) {
      if (!blog.contains(e.target)) setBlog(false);
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && blog.classList.contains("is-open")) {
        setBlog(false);
        blogToggle.focus();
      }
    });
  }

  var shortlistHost = document.querySelector("[data-nav-shortlist]");
  if (shortlistHost) {
    var parse = function (filename, text) {
      var match = text.match(/^---\s*\n([\s\S]*?)\n---\s*\n?/);
      var post = { slug: filename.replace(/\.md$/i, ""), title: "", shortlist: 0 };
      if (match) match[1].split(/\n/).forEach(function (line) {
        var pair = line.match(/^(\w+)\s*:\s*(.*)$/);
        if (pair) post[pair[1].toLowerCase()] = pair[2].trim();
      });
      return post;
    };
    fetch("/posts/index.json")
      .then(function (r) { return r.json(); })
      .then(function (files) { return Promise.all(files.map(function (file) {
        return fetch("/posts/" + file).then(function (r) { return r.ok ? r.text() : null; })
          .then(function (text) { return text ? parse(file, text) : null; });
      })); })
      .then(function (posts) {
        posts = posts.filter(function (post) { return post && Number(post.shortlist || 0) > 0; })
          .sort(function (a, b) { return Number(a.shortlist) - Number(b.shortlist); });
        if (!posts.length) return;
        shortlistHost.innerHTML = posts.map(function (post) {
          return '<a href="/blog/' + encodeURIComponent(post.slug) + '">' + esc(post.title) + '</a>';
        }).join("");
      })
      .catch(function () { /* static links remain available */ });
  }

  function esc(value) {
    return String(value).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }
})();
