/* ============================================================
   BRENT YOUNG — COLUMN CORE
   Loads Markdown posts from posts/ via posts/index.json.
   Each .md carries frontmatter between --- fences:
   title, date (YYYY-MM-DD), topic, deck.
   Slug = filename without .md.
   ============================================================ */
(function () {
  "use strict";

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
    return fetch("posts/index.json")
      .then(function (r) { return r.json(); })
      .then(function (files) {
        return Promise.all(files.map(function (f) {
          return fetch("posts/" + f)
            .then(function (r) { return r.ok ? r.text() : null; })
            .then(function (txt) { return txt ? parseFrontmatter(f, txt) : null; })
            .catch(function () { return null; });
        }));
      })
      .then(function (list) {
        return list
          .filter(Boolean)
          .sort(function (a, b) { return a.date < b.date ? 1 : -1; });
      });
  }

  window.ColumnCore = { load: load };
})();
