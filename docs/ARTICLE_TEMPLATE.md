# Article Template

Every article should follow this structure. Not as a formula — as a discipline.

Articles begin with curiosity, not answers. They teach principles, not tips. They illustrate transformation, not aesthetics. Every article should move the reader from preference toward stewardship, and leave them thinking:

> "I've never looked at it that way before."

---

## Frontmatter

Every post in `posts/` carries frontmatter between `---` fences:

```
---
title: The Article Title
date: YYYY-MM-DD
topic: One of the content pillars (see CONTENT_ROADMAP.md)
deck: One or two sentences that earn the click honestly. No clickbait.
tags: two, or three, comma-separated
terms: brand, touchpoint, stewardship
principle: The First Principle this article evidences (see FIRST_PRINCIPLES.md)
featured: true (optional; makes this the lead article on /blog)
shortlist: 1 (optional; numbered position in The Short List)
---
```

Allowed `topic:` values: `Brand & Mission`, `Creative Leadership`, `Systems & Workflow`, `Craft`, `AI`, and `Manifesto` (reserved for cornerstone essays).

Optional frontmatter: `banner:` (overrides the default banner path), `bannerAlt:` (alt text for the banner), `draft: true` (hides the post), `featured: true` (selects the lead article), and `shortlist:` (a positive number selects and orders the post in The Short List).

**Glossary terms.** Part of preparing every post is choosing three to five glossary terms that carry the article's working language. `terms:` takes comma-separated glossary *slugs* (not display labels), matching the filenames in `glossary/`, in the order you want them shown. Invalid slugs are ignored quietly, so check spelling against the glossary. Tags and terms do different jobs: tags connect related articles; terms explain the working language used in this one. The site renders them as "Terms in This Article" beside the piece, with quick definitions on tap.

**Banner art.** Every post gets a custom banner designed at **1200 × 630** (the Open Graph size), saved to `assets/img/blog/<slug>.jpg`. One asset serves three jobs: the social share card, the plate at the top of the article page, and the thumbnail on the blog index. The share card already shows the title and deck as text, so the art does not need the title baked in. Until the banner exists, the site shows an FPO placeholder plate.

**Inline images.** Store supporting images beside the article banners using a suffix such as `assets/img/blog/<slug>-01.jpg`, then reference them from Markdown with an alt description and an optional caption:

```
![A volunteer greeting a visitor](/assets/img/blog/your-church-already-has-a-brand-01.jpg "The first impression starts before the front door.")
```

The server renders standalone images as editorial figures. The alt text serves accessibility; the optional quoted title becomes the visible caption. Inline images do not need to be 1200 × 630 — use the source dimensions that best fit the image, optimized for the web.

**URLs.** Posts live at `pbrentyoung.com/blog/<slug>`. PHP renders the Markdown, contents navigation, taxonomy, related articles, and per-post social/search metadata before the page is sent. Publishing is unchanged: write the Markdown into `posts/`, add the filename to `posts/index.json`, commit, and deploy. The RSS feed and public sitemap update automatically.

---

## The Beats

### 1. Big Idea

One sentence. What is the single conviction this article exists to transfer? If it can't be said in one sentence, it isn't ready to be an article.

### 2. Question

Open with curiosity. A question the reader has felt but may never have said out loud.

### 3. Problem

Name the real problem — usually hiding underneath the stated one. ("We need a new logo" is rarely the problem.)

### 4. Stakes

Why does this matter? Who is affected when this goes unaddressed? Connect it to mission, not metrics.

### 5. Reframe

The turn. Reveal the hidden assumption and replace it. This is where "I've never looked at it that way before" happens.

### 6. Guide

Teach the principle. Use stories and real examples. Short paragraphs. No jargon.

### 7. Before / After

Illustrate the transformation — visual, conceptual, or organizational. The reader should clearly see:

Before → After

### 8. First Step

One thing the reader can do this week. Small, concrete, and free.

### 9. See It In Practice

Link to the case study or project that demonstrates this principle. The work is evidence, not the destination.

### 10. Keep Reading

Link to one or two related articles. The site is a library, not a feed — every article should point somewhere.

### 11. Discussion Question

End with a question worth bringing to a team meeting. Invite the reader to surrender something smaller for something greater.

---

## The Test

Before publishing, ask:

- Does this build trust?
- Does this remove an unnecessary obstacle?
- Does this serve the mission?
- Does this help people understand?
- Does this encourage stewardship?
- Does this reduce preference-driven decision making?
- Does this help tell the right story?
- Which first principle does this article connect back to?

If an article connects to no first principle, it doesn't belong on the site.
