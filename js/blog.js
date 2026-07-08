/* ============================================================
   BRENT YOUNG — THE BLOG
   Markdown-powered, typeset in the A List Apart tradition.
   Writing conventions inside a post's .md:
     ## Subhead                → section heading
     > ! Quote text            → pull quote
     > Quote text              → blockquote
     ![alt](src "CAPTION")     → figure with caption
   ============================================================ */
(function () {
  "use strict";

  var POSTS = [];

  function esc(s) {
    return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }

  function fmtDate(iso) {
    return new Date(iso + "T12:00:00")
      .toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
  }

  function plainWords(post) {
    var t = (post.title + " " + post.deck + " " + post.md)
      .replace(/[#>*_`!\[\]()-]/g, " ")
      .replace(/https?:\S+/g, " ");
    return t.trim().split(/\s+/).length;
  }

  function readTime(post) {
    return Math.max(1, Math.round(plainWords(post) / 200)) + " MIN READ";
  }

  /* ---------- markdown → html ---------- */
  function fallbackMd(md) {
    return md.split(/\n\s*\n/).map(function (chunk) {
      var c = chunk.trim();
      if (!c) return "";
      if (/^##\s+/.test(c)) return "<h2>" + esc(c.replace(/^##\s+/, "")) + "</h2>";
      if (/^>\s*/.test(c)) {
        var q = c.replace(/^>\s?/gm, "");
        return "<blockquote><p>" + esc(q) + "</p></blockquote>";
      }
      var inline = esc(c)
        .replace(/\*\*([^*]+)\*\*/g, "<strong>$1</strong>")
        .replace(/\*([^*]+)\*/g, "<em>$1</em>");
      return "<p>" + inline + "</p>";
    }).join("");
  }

  function mdToHTML(md) {
    if (window.marked && window.marked.parse) {
      return window.marked.parse(md, { mangle: false, headerIds: false });
    }
    return fallbackMd(md);
  }

  /* pull quotes, figures — applied to the rendered DOM */
  function typeset(container) {
    container.querySelectorAll("blockquote").forEach(function (bq) {
      var p = bq.querySelector("p");
      if (p && /^\s*!\s*/.test(p.textContent)) {
        var aside = document.createElement("aside");
        aside.className = "pull";
        aside.setAttribute("aria-hidden", "true");
        aside.innerHTML = p.innerHTML.replace(/^\s*!\s*/, "");
        bq.replaceWith(aside);
      }
    });
    container.querySelectorAll("p > img:only-child").forEach(function (img) {
      var fig = document.createElement("figure");
      fig.className = "article-fig";
      var cap = img.getAttribute("title");
      img.parentNode.replaceWith(fig);
      fig.appendChild(img);
      img.loading = "lazy";
      if (cap) {
        var fc = document.createElement("figcaption");
        fc.className = "figcap";
        fc.innerHTML = "<span>" + esc(cap) + "</span>";
        fig.appendChild(fc);
      }
    });
    var first = container.querySelector("p");
    if (first) {
      var parts = first.innerHTML.split(" ");
      if (parts.length >= 5) {
        first.innerHTML = '<span class="opener">' + parts.slice(0, 4).join(" ") + "</span> " + parts.slice(4).join(" ");
      }
    }
    var end = document.createElement("span");
    end.className = "endmark";
    end.setAttribute("aria-hidden", "true");
    end.innerHTML = "&#10086;";
    container.appendChild(end);
  }

  /* ---------- views ---------- */
  function byline(post) {
    return '<p class="entry-byline">BY BRENT YOUNG &middot; ' +
      fmtDate(post.date).toUpperCase() + " &middot; " + readTime(post) + "</p>";
  }

  function entryHTML(post, featured) {
    var no = "NO. " + String(POSTS.length - POSTS.indexOf(post)).padStart(3, "0");
    return '<article class="entry' + (featured ? " entry-featured" : "") + '">' +
      '<p class="entry-meta"><span class="kicker">' + esc(post.topic || "Field Notes") + "</span>" +
      '<span class="mono">' + no + "</span></p>" +
      '<h2 class="entry-title"><a href="#' + esc(post.slug) + '">' + esc(post.title) + "</a></h2>" +
      '<p class="entry-deck">' + esc(post.deck || "") + "</p>" +
      byline(post) +
      "</article>";
  }

  function renderList() {
    var host = document.getElementById("postList");
    var view = document.getElementById("postView");
    view.hidden = true;
    host.hidden = false;

    if (!POSTS.length) {
      host.innerHTML = "<p>Nothing filed yet.</p>";
      return;
    }
    var html = entryHTML(POSTS[0], true);
    if (POSTS.length > 1) {
      html += '<p class="entries-rule mono">MORE FROM THE DESK</p>';
      POSTS.slice(1).forEach(function (p) { html += entryHTML(p, false); });
    }
    host.innerHTML = html;
    document.title = "Blog — Brent Young";
  }

  function renderPost(slug) {
    var post = null;
    for (var i = 0; i < POSTS.length; i++) if (POSTS[i].slug === slug) post = POSTS[i];
    if (!post) { renderList(); return; }

    var host = document.getElementById("postView");
    document.getElementById("postList").hidden = true;
    host.hidden = false;

    host.innerHTML =
      '<a class="mono back-link" href="#">&larr; ALL POSTS</a>' +
      '<p class="entry-meta article-meta"><span class="kicker">' + esc(post.topic || "Field Notes") + "</span>" +
      '<span class="mono">' + fmtDate(post.date).toUpperCase() + "</span></p>" +
      '<h1 class="article-title">' + esc(post.title) + "</h1>" +
      '<p class="article-lede">' + esc(post.deck || "") + "</p>" +
      '<p class="entry-byline article-byline">BY BRENT YOUNG &middot; ' + readTime(post) +
      " &middot; " + plainWords(post).toLocaleString() + " WORDS</p>" +
      '<div class="article-body">' + mdToHTML(post.md) + "</div>" +
      '<div class="slugline article-slug"><span>FILED: ' + esc(post.slug).toUpperCase() +
      "</span><span>SET &amp; PROOFED: B. YOUNG</span></div>";

    typeset(host.querySelector(".article-body"));
    document.title = post.title + " — Brent Young";
    window.scrollTo(0, 0);
  }

  function route() {
    var slug = location.hash.replace(/^#/, "");
    if (slug) renderPost(slug);
    else renderList();
  }

  window.ColumnCore.load()
    .then(function (posts) {
      POSTS = posts;
      route();
      window.addEventListener("hashchange", route);
    })
    .catch(function () {
      document.getElementById("postList").innerHTML =
        "<p>Could not load posts. If you opened this file directly, run it from a web server.</p>";
    });
})();
