# Session Handoff

Last updated: 2026-07-15 (editorial homepage promotion)

## Homepage Promotion — 2026-07-15

The editorial rebuild is now the default homepage at `/` and in `index.html`.
The former classic homepage is preserved as `index_old.html` and marked
`noindex, nofollow` so it remains available as a reference without competing
with the canonical homepage.

All shared blog navigation and article links now return to `/` and its homepage
sections. Requests for the former `/index-new.html` preview URL and explicit
`/index.html` requests permanently redirect to `/` in both Apache and the local
PHP router. Case File links using stable IDs such as `#forge`, `#mts-workbook`,
and `#wired-in` now open the corresponding record in The File.

## Nightly Handoff — 2026-07-14

### The Right Story, Told the Right Way

`posts/the-right-story-told-the-right-way.md` received a substantial voice and
content pass. The article now centers on one conviction: every church has a
God-given story worth telling, and church communications is the work of
faithfully stewarding that story.

The current structure is:

- Communication is ministry. We begin caring for people before we know who
  they are.
- Design is hospitality. An invitation asks someone to show up; a welcome
  prepares a place for them. The grandparent and *Bluey* example carries this
  idea, along with Jesus saying he would prepare a place for us.
- Story builds trust. The mission promise and the experience created by our
  touchpoints must agree.
- Systems create room for creativity. Chaotic systems cannot scale, especially
  in church communications where it can feel like the team has twenty
  supervisors.
- The close describes post-COVID church communications as preaching to the
  future congregation. The first campus many people visit is digital, which
  makes this work a form of evangelism.

The article links to the new Future Congregation Journey tool. A final
continuity read is still worthwhile before declaring the manifesto finished,
but the core argument and voice are now established.

The final banner was replaced at
`assets/img/blog/the-right-story-told-the-right-way.jpg`. Its 600 × 315 card
derivative was regenerated at
`assets/img/blog/thumbs/the-right-story-told-the-right-way.jpg` with
`scripts/generate-blog-thumbnails.sh`.

### Future Congregation Journey

The interactive field tool lives at `/future-congregation-journey` and is
implemented by `future-congregation-journey.php`.

Its journey is intentionally framed as:

- Awareness
- Visit
- Attend
- Member
- Minister

Jordan is the fictional future-congregation persona. The persona copy reads:
“A persona is more than just a demographic. Jordan is a person God has called
us to prepare a place for.” The Attend-stage question is: “Do I want to come
back?”

The closing note makes clear that the journey is shared but the timeline is
not fixed. Some people move through it quickly, some take years, some begin on
campus, and some arrive having already moved through Awareness and Visit
online.

Design and behavior decisions:

- The map is a wide editorial field tool that remains visually connected to
  the portfolio site.
- Stage headings form one continuous chevron progression rather than five
  unrelated buttons.
- Stage labels use IBM Plex Sans at a stronger weight. Selected stages use the
  site blue.
- On mobile, the progression remains horizontal and scrollable instead of
  breaking into rows.
- Panel changes use a subtle fade, rise, and animated height adjustment.
- Reduced-motion preferences disable the movement.
- The closing guide note runs the full width of the frame.
- Jordan's portrait is stored at `assets/img/journey/jordan-persona.png`.

Production routing was added to `.htaccess`; local routing was added to
`router.php`; and the tool was added to `sitemap.php`, even while blog indexing
remains disabled.

Validation completed:

- PHP syntax checks passed for the journey page, router, and sitemap.
- The inline JavaScript passed a syntax check.
- `git diff --check` passed before commit.
- The page was visually checked at desktop and 390px mobile widths.
- Stage selection, blue active state, transition behavior, and mobile
  horizontal scrolling were exercised in the browser.

### Related Philosophy and Voice Work

`docs/FIRST_PRINCIPLES.md` now closes with “Stewarding the Story,” reinforcing
that our responsibility is not to invent a more impressive story but to
faithfully steward the one God entrusted to us.

`docs/VOICE_NOTES.md` contains the durable writing choices learned while
editing these posts. Continue adding to it as the collaborative editing process
refines the voice.

### Commits Published Tonight

- `3bf2896 Add future congregation journey`
  - Publishes the manifesto rewrite, stewardship note, interactive journey,
    Jordan portrait, routes, sitemap entry, responsive design, and motion.
- `ae15c25 Update Right Story artwork`
  - Publishes the final banner and regenerated 600 × 315 thumbnail.

Both commits are on `main` and pushed to `origin/main`.

### Working Tree at Pack-Up

The branch is synchronized with `origin/main`. The following local items were
intentionally not included in tonight's commits:

- Modified `.DS_Store`
- Modified `assets/.DS_Store`
- Untracked `docs/.DS_Store`
- Deleted `assets/img/blog/brand-is-the-referee.png`

The deleted PNG appeared after the published work and was not touched or
staged. Confirm whether it is an intentional cleanup before committing it.

The GitHub CLI token currently reports as invalid, but the repository's normal
Git/SSH push authentication is working.

### Blog Sharing, Images, and UI Polish

- Blog and site pages now emit complete Open Graph and Twitter metadata. Site
  pages use `images/og-image.png`; individual articles use their full banner
  image. Social cards require absolute HTTPS image URLs.
- `/robots.txt` allows social preview crawlers to fetch `/blog`. When
  `blog_public` is false, pages remain `noindex, follow` and are omitted from
  the sitemap so previews work without making the blog searchable.
- Each banner has a separate 600 × 315 derivative at
  `assets/img/blog/thumbs/<slug>.jpg`. Regenerate one after changing banner art:

  ```sh
  magick assets/img/blog/<slug>.jpg \
    -resize 600x315^ -gravity center -extent 600x315 \
    -strip -quality 82 assets/img/blog/thumbs/<slug>.jpg
  ```

- The homepage Field Notes and blog Short List use the derivative; article
  heroes and social sharing use the full banner.
- Author name, bio, and avatar source remain centralized in
  `inc/blog-config.php`. The current avatar artwork is committed in
  `assets/img/brentAvatarSquare.png` and `assets/img/brentHedcut.png`.
- `css/editorial.css` uses rem-based typography, shared clamp type tokens,
  68ch article text measure, widow/orphan protection, controlled hyphenation,
  and responsive non-splitting rules for figures and pull quotes.
- The flat-file pencil is desktop/tablet-only and now sits 40px higher so it
  clears the `SPEC SHEET: RESUME.PDF` button. The legacy mobile resume button
  is constrained to its content width.

### Local End-of-Day State

- The PHP server was stopped at pack-up. Start it from the repository root with
  `php -S localhost:8000 router.php` when testing locally.
- Verify `/`, `/blog`, and a post such as
  `/blog/brand-is-the-referee` after starting the server.

## Blog Architecture (NEW — 2026-07-10)

The blog is now server rendered from the Markdown files in `posts/`:

- **Canonical URLs:** `/blog` is the only index URL. `/blog/` and
  `/blog.html` permanently redirect to it. Posts live at `/blog/<slug>`.
- **Shared source of truth:** `inc/blog.php` parses frontmatter, loads and
  sorts posts, renders Markdown, builds heading IDs, scores related posts,
  filters taxonomy/search results, and creates canonical asset URLs.
- **Markdown:** articles remain `.md` files. `league/commonmark` renders the
  body on the server; its locked dependencies are committed so deployment
  has no install step. The server must run PHP 8.2 or newer.
- **Blog index:** `blog.php` renders the featured story, RSS subscription,
  The Short List, topic navigation, full-text search, tag filters, a
  responsive article grid, and 24-post pagination. Topic, tag, search, and
  page state are shareable query-string URLs.
- **Article page:** `blog-post.php` renders a centered wide title/banner,
  an 800px reading column, sticky desktop contents
  for posts with 3+ H2s, a native mobile contents disclosure, linked tags
  and first principle, RSS subscription, an author bio block using
  `assets/img/brentAvatarSquare.png`, and three automatically related banner
  cards.
  The desktop contents list is unnumbered. Circular social share icons sit
  beneath it. Pull-quote keylines remain within the article column so they do
  not collide with the sidebar. The article deck is italic.
  The author name, bio, and avatar path live in `inc/blog-config.php` so they
  can be changed once for every post. On mobile, the portrait is centered
  above the category-colored panel and the name and bio are centered inside it;
  the RSS subscription row follows the author panel. `js/article.js` only
  enhances copy-link behavior.
- **Selection frontmatter:** `featured: true` selects the lead story.
  `shortlist: 1` (or another positive number) selects and orders The Short
  List. `draft: true` hides a post. See `docs/ARTICLE_TEMPLATE.md`.
- **Banners:** development posts have JPG artwork at
  `assets/img/blog/<slug>.jpg`. The same file appears
  in index cards, article heroes, related cards, Open Graph, Twitter, and
  JSON-LD. Replace working art as each article is finalized, then regenerate
  its 600 × 315 thumbnail.
- **Generated discovery routes:** `/feed.xml`, `/sitemap.xml`, and
  `/robots.txt` are rendered from the same post data. They never require
  manual post lists.
- **Development indexing:** `blog_public` in `inc/blog-config.php` is the
  single launch flag. While false, blog pages emit `noindex, nofollow`,
  robots disallows `/blog`, and blog URLs are omitted from the sitemap.
- **Local server:** start with `php -S 127.0.0.1:8000 router.php`. PHP's
  built-in server does not read `.htaccess`; `router.php` mirrors production.
- Blog navigation returns to the editorial homepage at `/`.
- Analytics is intentionally deferred until launch.

## Homepage Preview and Navigation

- `index.html` includes a server-data-compatible **Field Notes**
  section between The File and Contact. It renders the featured post plus the
  three `shortlist:` posts from `posts/`, with the same topic links,
  dates, read times, banners, and thumbnails used by the blog index.
- The `THE BLOG` label sits above the Field Notes heading. Short List entries
  use their artwork instead of numerical markers, and category labels use the
  color associated with each editorial theme.
- `js/editorial.js` loads the shared post index for the homepage preview. If no
  post is marked `featured: true`, the newest visible post is used as the
  fallback feature.
- Blog cards use 600 × 315 derivatives in `assets/img/blog/thumbs/`. Rebuild
  them after replacing banner art with:
  `scripts/generate-blog-thumbnails.sh`.
- The BLOG item in the shared navigation is a taxonomy dropdown. It includes
  All Articles, the five main topics, and The Short List. Desktop opens it on
  hover/focus; mobile opens it as a nested menu from the main navigation.
  The desktop trigger and dropdown include a hover bridge so the menu remains
  open while the pointer moves into it.

## Current Repository State

- `main` and `origin/main` are synchronized at
  `ae15c25 Update Right Story artwork`.
- The complete blog rebuild, homepage Field Notes preview, thumbnail workflow,
  shared BLOG dropdown, manifesto rewrite, and Future Congregation Journey are
  committed and published.
- `index.html` is the editorial homepage. The former classic homepage is
  preserved as `index_old.html`.

## Important Recent Commits

- `ebbfbdf Make flat file a slide table`
  - Adds the slide-table flat-file treatment.
  - Adds generated thumbnail assets under `assets/img/thumbs/`.
  - Adds local impact-label fonts and examples under `assets/fonts/impact_label/`.
  - Adds loupe and non-repro blue pencil graphics.
  - Repoints portfolio poster fields to smaller thumbnail assets.
- `51be6a9 Promote editorial page to homepage`
  - Promoted `index-new.html` into `index.html`.
  - This was later identified as premature; `index.html` has now been restored locally but that restore has not been committed yet.
- `54d6abf Use hand-drawn principle cues`
  - Replaces the First Principles SVG cues with hand-drawn PNGs.

## Flat File Slide Table Decisions

The flat file is now designed as mounted slides on a light table:

- Slide cards use a single warm off-white mount color.
- The film aperture uses a `3 / 2` aspect ratio.
- Card title sits centered in the top label area using Google font `Permanent Marker`.
- Bottom slide area is reserved for mono ephemera/job markings.
- Cards get stable seeded randomness through CSS variables set in `js/editorial.js`.
- Hover behavior:
  - slow transition,
  - slight lift,
  - stronger shadow,
  - subtle zoom,
  - card straightens on hover.
- Impact-label filters:
  - use local `Impact Label Reversed` font,
  - appear as loose black embossed tape strips,
  - are not sticky,
  - do not straighten on hover,
  - hover/active state uses brightness/color treatment only.
- Loupe and pencil:
  - loupe uses transparent `assets/img/loupeExample.png`,
  - pencil uses transparent `assets/img/nonReproBluePencil.png`,
  - both are desktop/tablet margin ephemera and hidden on mobile.

## Thumbnail Performance Work

The active editorial portfolio uses `data/portfolio.json`.

Changes made:

- Generated 66 thumbnail assets in `assets/img/thumbs/`.
- Replaced all active `poster` references in `data/portfolio.json` with thumbnail paths.
- Also updated legacy `data/projects.json` where possible.
- Kept full-size `src` values intact so opened modals/detail views still use original media.
- Original unique active poster payload was about `61.9 MB`.
- New thumbnail payload is about `5.3 MB`.

One legacy image could not be thumbnailed because ImageMagick reported an invalid PNG header:

- `assets/img/vlcsnap-2020-07-20-18h13m25s366.png`

## Video Hosting (RESOLVED 2026-07-10)

Videos are self-hosted on `video.pbrentyoung.com`, set up as a STANDALONE
website in hPanel (not a subdomain of pbrentyoung.com), so its document
root sits outside pbrentyoung.com's deploy-managed tree and mirror
deploys cannot delete it. (An earlier attempt used media.pbrentyoung.com
as a subdomain, but Hostinger forces subdomain roots inside public_html.)

How it works:

- The full contents of local `assets/video/` are uploaded flat to the
  video.pbrentyoung.com site root, keeping original filenames.
- `data/portfolio.json` and `data/projects.json` keep plain local
  `assets/video/...` paths — never edit them for hosting reasons.
- `mediaSrc()` in BOTH `js/editorial.js` and `js/main.js` translates at
  render time: on production hosts, `assets/video/<file>` becomes
  `https://video.pbrentyoung.com/<file>`; on localhost the relative path
  is kept so local dev plays from the local folder.
- Verified 2026-07-10: all 29 unique video references across both JSON
  files return HTTP 200 with video/* content types on the subdomain,
  and the server supports range requests (HTTP 206) for seeking.
- New videos: drop the file in local `assets/video/`, upload the same
  file to the video.pbrentyoung.com site root, reference it in JSON as
  `assets/video/<file>` — no code changes needed.

## Deployment Notes

- GitHub is current through `1115d29`.
- The git deployment MIRRORS the repo: server files not in the repo get deleted on deploy. This is what wiped `assets/video/` from the host (it is gitignored). Do not re-upload videos to the deploy-managed path — they will be deleted again on the next deploy.
- 2026-07-10: Brent manually uploaded `index.html` (classic homepage) and `css/editorial.css` to the live host; verified live — pbrentyoung.com serves the classic homepage and the current editorial.css. The repo now matches this state, so future deploys won't clobber it.
- Hostinger has previously served stale/old files after GitHub pushes.
- If live site does not update, verify whether Hostinger is pulling from GitHub or needs manual upload/deploy.
- The editorial homepage was approved for the root URL on 2026-07-15. Keep
  `index_old.html` only as a non-indexed reference unless Brent asks to remove it.

## Outstanding Follow-Ups

- Brent will replace the ten development article drafts with final copy.
- Replace each working-art blog banner with final 1200×630 art as the article
  is finalized.
- Review the provisional `tags:` and `principle:` values during copy editing.
- Revisit the title “The Short List” if a stronger editorial label emerges.
- At launch, set `blog_public` to `true`, choose analytics, then verify
  production rewrites and social cards.
- Decide whether to remove `.DS_Store` files from tracking in a future cleanup.

## Conversation Handoff: Flat File Context Review (2026-07-15)

### Current direction

The homepage portfolio has been consolidated into a single section called **The
File**. The former standalone case-study section was removed. Detailed work now
lives inside the same slide-table archive as the rest of the portfolio.

The goal is not to present a gallery of finished objects alone. The File should
show what Brent was asked to solve, who the work served, what his role was, what
was made, what changed, and what the work taught him. The work is evidence of the
ideas and principles developed throughout the site.

Three records currently function as detailed case files:

- Mending the Soul: the Native American workbook
- Wired In: building a national broadcast network
- Forge: a church plant, from blank page to launch day

Their archive entries point to detailed records through `caseStudy` IDs in
`data/portfolio.json`. Detailed records currently support client, discipline
codes, problem (`challenge`), role, response (`craft`), outcome (`impact`),
lesson, principle, related essay, and multiple media items.

### Next working session

Review the 65 items in `data/portfolio.json` one at a time, in the order they
appear in The File. This should be a collaborative editorial interview, not a
bulk rewrite.

For each item:

- Show Brent the title and existing media.
- Ask for the context that cannot be learned from the asset alone.
- Clarify the organization or client, the original need, Brent's role, notable
  constraints, what he made, the outcome, and what he learned.
- Decide whether the item needs a concise contextual note or should become a
  full Case File.
- Connect it to a first principle or Field Note only when that relationship is
  meaningful.
- Update `data/portfolio.json`, verify the opened job jacket, and then move to
  the next item.

Do not number the public-facing records unless Brent specifically asks for it.
Use direct, warm, practical language. Do not inflate a project into a case study
when the honest context is more useful. Preserve the rule that the work supports
the ideas rather than becoming the destination of the site.

### Homepage copy

The revised copy in Before the Work, The File, Field Notes, and Contact is now
part of the default `index.html` homepage. It clarifies the narrative progression
as calling, evidence, lessons, and invitation.

### Repository state at handoff

- Branch: `main`
- Latest published commit before this promotion: `40822e5 Add bowling ball illustration`
- Local homepage promotion is awaiting commit.
- `data/portfolio.json` remains the source of truth for The File.
- `js/editorial.js` renders both ordinary job jackets and expanded Case Files.
- `css/editorial.css` contains the desktop split layout and stacked mobile
  treatment for expanded Case Files.
