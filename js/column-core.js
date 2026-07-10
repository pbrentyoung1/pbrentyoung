/* ============================================================
   BRENT YOUNG — COLUMN CORE
   Loads Markdown posts from posts/ via posts/index.json and
   provides the lightweight post-loading helpers used by the
   homepage teaser (js/editorial.js). The blog index and article
   pages are rendered by PHP through inc/blog.php.

   Each .md carries frontmatter between --- fences:
     title, date (YYYY-MM-DD), topic, deck        — required
     tags        — comma-separated, optional
     principle   — the First Principle the article evidences
     banner      — image path; defaults to assets/img/blog/<slug>.jpg
     bannerAlt   — alt text for the banner plate
     draft       — any value hides the post
   Slug = filename without .md.
   ============================================================ */
(function () {
  "use strict";

  var ROOT = (function () {
    /* Retained for secondary previews that may run from either depth. */
    return /\/blog\//.test(location.pathname) ? "../" : "";
  })();

  function esc(s) {
    return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }

  function fmtDate(iso) {
    return new Date(iso + "T12:00:00")
      .toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
  }

  function plainWords(post) {
    var t = ((post.title || "") + " " + (post.deck || "") + " " + (post.md || ""))
      .replace(/[#>*_`!\[\]()-]/g, " ")
      .replace(/https?:\S+/g, " ");
    return t.trim().split(/\s+/).length;
  }

  function readTime(post) {
    return Math.max(1, Math.round(plainWords(post) / 200)) + " MIN READ";
  }

  function tagList(post) {
    if (!post.tags) return [];
    return String(post.tags).split(",").map(function (t) { return t.trim(); }).filter(Boolean);
  }

  function bannerPath(post) {
    return ROOT + (post.banner || "assets/img/blog/" + post.slug + ".jpg");
  }

  function thumbnailPath(post) {
    return ROOT + "assets/img/blog/thumbs/" + post.slug + ".jpg";
  }

  function postURL(post) {
    return ROOT + "blog/" + post.slug;
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

  /* pull quotes, figures, opener, endmark — applied to rendered DOM */
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

  /* ---------- loading ---------- */
  function parseFrontmatter(filename, text) {
    var slug = filename.replace(/\.md$/i, "");
    var meta = { slug: slug, title: slug, date: "1970-01-01", topic: "Field Notes", deck: "", md: text };
    var m = text.match(/^---\s*\n([\s\S]*?)\n---\s*\n?/);
    if (m) {
      m[1].split(/\n/).forEach(function (line) {
        var kv = line.match(/^(\w+)\s*:\s*(.*)$/);
        if (kv) meta[kv[1].toLowerCase()] = kv[2].trim();
      });
      meta.md = text.slice(m[0].length);
    }
    return meta;
  }

  function load() {
    return fetch(ROOT + "posts/index.json")
      .then(function (r) { return r.json(); })
      .then(function (files) {
        return Promise.all(files.map(function (f) {
          return fetch(ROOT + "posts/" + f)
            .then(function (r) { return r.ok ? r.text() : null; })
            .then(function (txt) { return txt ? parseFrontmatter(f, txt) : null; })
            .catch(function () { return null; });
        }));
      })
      .then(function (list) {
        return list
          .filter(Boolean)
          .filter(function (p) { return !p.draft; })
          .sort(function (a, b) { return a.date < b.date ? 1 : -1; });
      });
  }

  window.ColumnCore = {
    load: load,
    esc: esc,
    fmtDate: fmtDate,
    plainWords: plainWords,
    readTime: readTime,
    tagList: tagList,
    bannerPath: bannerPath,
    thumbnailPath: thumbnailPath,
    postURL: postURL,
    mdToHTML: mdToHTML,
    typeset: typeset
  };
})();
