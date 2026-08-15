# brentyoung.org Project Knowledge

Last consolidated: 2026-08-15

This is the current operational source of truth for the website. It preserves
decisions that were previously scattered across Codex and ChatGPT tasks,
repository history, and the older session handoff.

Use this document for current state and deployment context. Use the other
constitutional documents in `docs/` for voice, philosophy, terminology, and
editorial direction.

## Project Identity

- Canonical public site: `https://brentyoung.org`
- Former public site: `https://pbrentyoung.com`
- Repository: `git@github.com:pbrentyoung1/pbrentyoung.git`
- GitHub project: `pbrentyoung1/pbrentyoung`
- Hosting: Hostinger hPanel on LiteSpeed with PHP 8.3
- DNS and mail: managed separately from the repository
- The site is a thinking platform, not a conventional portfolio.
- The portfolio supports the ideas. The ideas are the primary product.
- Central belief: **The right story, told the right way.**

The primary public identity is **Brent Young | Church Communications**.

The public promise is:

> Helping churches communicate their heart with clarity, creativity, and
> conviction.

Story, design, leadership, Brand, and systems describe disciplines within the
work. They are not a four-part substitute for the three-part public promise.

## Project Organization

The project uses a central **brentyoung.org Project Hub** and five specialized
tasks:

- Administration & Deployment
- Site Design & Development
- Brand & Strategy
- Content & Blog
- Social Media & Distribution

Their ownership boundaries, routing rules, and durable knowledge requirements
are defined in `docs/PROJECT_AREAS.md`.

Chats are working spaces, not permanent sources of truth. Material decisions
must be written into the repository document owned by the relevant area.
Cross-area consequences return to the Project Hub so the site continues to
operate as one coherent body of work.

### Active cross-area handoffs

`docs/SEO_PHASE_0_AUDIT.md` records the 2026-08-13 technical, indexation,
analytics, conversion, performance, social-preview, and content-architecture
baseline. The site is publicly crawlable, but Search Console reports only three
indexed canonical pages and seventeen pages as discovered but not indexed. The
former domain's Change of Address remains unaccepted because Google's validator
could not fetch the working old HTTP homepage redirect. Because the former
domain had little meaningful traffic or search visibility, this is a
low-priority monitoring item rather than a gate for deployment or publishing.
The redundant `www` sitemap has been removed, the canonical Search Console
property is associated with the `New Site` GA4 stream, and that stream now uses
the apex URL. Phase 1
should prioritize measurement and discovery work. Administration & Deployment
can retry the migration signal occasionally after Google's validator refreshes.

`docs/SEO_PHASE_1_IMPLEMENTATION.md` records the first implementation sprint:
privacy-conscious subscription, resource, related-content, and contact events;
durable sitemap modification dates; `noindex, follow` for blog filter and
pagination variants; a stable Person identifier; article `dateModified`; and
article Breadcrumb markup. The Community Snapshot is now included in the
generated sitemap, bringing the implementation inventory to 22 canonical URLs.

`docs/DISTRIBUTION_STRATEGY.md` now defines the channel system, publishing
cadence, calls to action, experiments, measurement, and the role of
*Evangelistic Marketing* as a central long-term outcome.

`docs/PUBLISHING_ACTION_PLAN.md` turns that strategy into a 90-day sequence
using the 15 essays currently listed in `posts/index.json`. It coordinates
Resource Development, Community Building, and Sustainability; develops a
Church Communications Planning Starter Kit beta; and protects weekly
*Evangelistic Marketing* work. The unlisted `How People Change` introduction
remains on hold until all ten John 4 articles are drafted and revised as a
unified series, consistent with the existing series publishing rule.

The following reviews remain open:

- **Content & Blog:** confirm the twelve-week sequence, editorial readiness,
  and final order; establish and own the book content ledger with the
  *Evangelistic Marketing* working documents; keep `How People Change`
  unpublished until the John 4 readiness rule is met; review the five
  provisional SEO authority hubs, their names, page assignments, and primary
  search jobs before architecture work begins.
- **Brand & Strategy:** review the desired Brand associations, public audience
  promise, three campaign priorities, Planning Starter Kit promise and audience
  fit, book positioning, and the boundary between reader learning and
  theological or strategic authority; review the five provisional SEO
  authority hubs so search architecture expresses the Brand instead of
  redefining it.
- **Site Design & Development:** support the Planning Starter Kit landing and
  delivery experience; verify newsletter placement, subscription conversion
  paths and tracking, social-preview quality, and useful related-content paths;
  deploy and production-check the locally completed Phase 1 event, sitemap,
  parameter-indexing, and entity-markup work; continue the still-open homepage
  newsletter path, authority-hub architecture, performance, accessibility,
  dedicated-preview, and related-content work after their dependencies are
  settled.
- **Administration & Deployment:** confirm privacy-conscious attribution and
  analytics for channel traffic, subscription conversion, and returning
  visitors; verify reliable forms, email, and resource delivery; monitor the
  former domain's Change of Address and retry it occasionally without allowing
  it to block deployment or publishing. The
  redundant `www` sitemap removal, canonical Search Console and GA4 association,
  and GA4 apex stream URL correction were completed on 2026-08-13. After Phase
  1 deployment, verify controlled events in GA4 Realtime or DebugView and mark
  confirmed `subscribe_success` and `resource_download` events as key events.
- **Social Media & Distribution:** after Administration confirms the two key
  events in production, use `subscribe_success` and `resource_download` as the
  newsletter and resource conversion baseline. Keep diagnostic events available
  for learning without treating every interaction as a conversion.

These are routed dependencies, not authorization for the four areas to edit the
shared worktree simultaneously. Each area should review the dependency with
Brent when he takes it up, then preserve the resulting decision in its owning
document and return cross-area consequences to the Project Hub.

## Current Production Topology

The two public domains currently have different jobs:

| Host | Purpose | Expected behavior |
| --- | --- | --- |
| `brentyoung.org` | Canonical website | Serves the site over HTTPS |
| `www.brentyoung.org` | Alias | Permanently redirects to `https://brentyoung.org/*` |
| `pbrentyoung.com` | Former domain | Permanently redirects path-for-path to `https://brentyoung.org/*` |
| `www.pbrentyoung.com` | Former alias | Permanently redirects path-for-path to `https://brentyoung.org/*` |
| `video.pbrentyoung.com` | Media host | Continues serving portfolio video and must not be redirected |

The redirect rules use exact host matching so the old-domain redirect does not
capture `video.pbrentyoung.com`. Paths and query strings must be preserved.

Live checks on 2026-07-30 confirmed:

- `https://brentyoung.org/` returns `200`.
- `https://www.brentyoung.org/` redirects to the canonical apex host.
- An old HTTP article URL redirects directly to the matching
  `brentyoung.org` URL with its query string intact.
- `video.pbrentyoung.com/forge-sunlight_1.mp4` returns `200`, `video/mp4`, and
  supports byte ranges.

## Repository and Branch State

There are two local worktrees for the same GitHub repository:

| Local path | Branch | Role |
| --- | --- | --- |
| `/Users/pbrentyoung/Documents/websites/brentyoung-migration` | `migration/brentyoung-org` | **Authoritative working copy, latest code, and current `brentyoung.org` deployment branch** |
| `/Users/pbrentyoung/Documents/websites/pbrentyoung` | `main` | Legacy checkout and old-domain migration history; not the source for new work |

Do not treat these as two repositories or two independent products. They are
two branches and worktrees of one repository.

Unless Brent explicitly changes this decision, begin all new site work in
`/Users/pbrentyoung/Documents/websites/brentyoung-migration` on
`migration/brentyoung-org`. Do not copy newer-looking files from the original
checkout over this worktree. The original checkout contains older experiments
and uncommitted artifacts that may still be useful as references, but its
timestamps or local modifications do not make it authoritative.

At consolidation time:

- `main` and `origin/main` point to `68cf5c7`.
- `migration/brentyoung-org` and its remote point to `5aa9034`.
- `main` contains the old-domain redirect commit `552a71a`.
- The migration branch contains the domain preparation, résumé, GA4, framework,
  and principle-card commits.
- `68cf5c7` on `main` and `304396c` on the migration branch represent the same
  framework/article work applied independently, so reconciliation must account
  for duplicate content rather than blindly merging both patches.
- Both worktrees contain uncommitted or untracked user files. Inspect both
  working trees before any merge, cherry-pick, cleanup, or branch deletion.

The branches were intentionally left separate while Google processed the domain
move and while Hostinger deployed the two domains from different branches.
Keep development and deployment on `migration/brentyoung-org` until the
migration is confirmed complete. The intended end state is to reconcile the
latest migration work into `main`, make `main` the canonical branch, and connect
the `brentyoung.org` Hostinger deployment to `main`.

Before any future reconciliation:

1. Confirming Search Console's Change of Address state.
2. Capturing or intentionally excluding every local change in both worktrees.
3. Comparing the unique commits and duplicate framework work.
4. Confirming which Hostinger site deploys which branch and document root.
5. Reconciling the migration work into `main`.
6. Connecting the `brentyoung.org` Hostinger deployment to `main`.
7. Verifying the reconciled `main` commit in production before retiring the
   migration branch.

## Application Architecture

This is a custom server-rendered PHP site. It is not WordPress and has no
JavaScript build system.

- Homepage: `index.php`
- Shared page rendering and Markdown helpers: `inc/blog.php`
- Shared runtime configuration: `inc/blog-config.php`
- Markdown parser: `league/commonmark` installed through Composer
- Posts: Markdown files in `posts/`
- Published post order: `posts/index.json`
- Glossary: one Markdown file per term in `glossary/`
- Portfolio data: `data/portfolio.json`
- Primary styles: `css/editorial.css`
- Primary interactions: `js/editorial.js`, plus focused scripts for navigation,
  subscriptions, glossary, and article features
- Production routes and redirects: `.htaccess`
- Local PHP routes: `router.php`
- Generated endpoints: `feed.php`, `sitemap.php`, and `robots.php`

The homepage, blog, articles, glossary, framework pages, and tools use the
shared header and footer from `inc/blog.php`. Do not copy global navigation or
footer markup into individual pages.

## Content and Publishing Model

The canonical editorial references are:

- `docs/FIRST_PRINCIPLES.md`
- `docs/EDITORIAL_GUIDE.md`
- `docs/VOICE_NOTES.md`
- `docs/ARTICLE_TEMPLATE.md`
- `docs/IDEA_GLOSSARY.md`
- `docs/CONTENT_ROADMAP.md`
- `docs/DISTRIBUTION_STRATEGY.md`
- `docs/PUBLISHING_ACTION_PLAN.md`
- `docs/evangelistic-marketing/`

The most important voice rules are:

- No em dashes.
- Do not number sections or ideas unless Brent asks for numbering.
- Address the reader directly with `you` and `your`.
- Use `we`, `our`, and `us` for the shared calling of the Church.
- Sound direct, pastoral, warm, practical, hopeful, and curious.
- Preserve the reader's dignity. Do not scold or imply manipulation.
- Prefer human examples and concrete observations over corporate language.
- Avoid AI clichés, over-symmetry, hedging filler, clickbait, and thought-leader
  language.
- Brent drives article development section by section. Do not write a complete
  piece in his place unless he explicitly asks.

Canonical house definition:

> Design is the intentional process of solving creative problems.

The theological guardrail is equally important: communication frameworks can
prepare a place, remove unnecessary barriers, clarify truth, and help someone
take an honest next step. They do not manufacture desire or create
transformation. Transformation belongs to God.

### Publishing a post

1. Create or revise `posts/<slug>.md`.
2. Add the filename to `posts/index.json` only when the post is ready to publish.
3. Add the banner at `assets/img/blog/<slug>.jpg`.
4. Generate or verify the 600 by 315 listing thumbnail in
   `assets/img/blog/thumbs/`.
5. Add a small, intentional set of valid glossary terms in frontmatter.
6. Connect the article to its First Principle when appropriate.
7. Verify the article, banner, thumbnail, terms, sharing metadata, RSS, and
   sitemap locally.
8. Commit only the intended files and deploy the branch that actually serves
   `brentyoung.org`.

All homepage principle cards now have supporting essay links. The long-term
direction is for the essays, glossary, frameworks, and portfolio work to form a
connected body of thought rather than separate content sections.

## Design Direction

The editorial print-shop and Kodachrome-influenced visual system is the default,
not an optional alternate theme.

Durable choices include:

- Editorial journal rather than marketing blog
- Proof marks, taped artifacts, labels, marginal notes, and working-file cues
- Slight deterministic scatter before elements settle into place
- Stable, intentional motion rather than random reshuffling
- Playfair Display, IBM Plex Sans, and IBM Plex Mono as core editorial voices
- Handwritten accents that feel genuinely placed in a proof margin
- Responsive layouts that preserve the editorial idea rather than merely stack
  every element
- Display text and pull quotes should not hyphenate
- Article graphics should follow `docs/infographics/STYLE_GUIDE.md`

The shared infographic direction is a taped editorial-diagram style. Prefer SVG
when labels need to stay exact and responsive, but respect an article's
intentional PNG choice when Brent selects the final raster graphic.

## Integrations and Private Configuration

- Google Analytics 4 measurement ID: `G-F1TD8KH28H`
- Microsoft Clarity project ID: `xnj6ziehcb`
- Brevo handles blog subscriptions. Its public form action lives in
  `inc/blog-config.php`.
- `js/analytics.js` defines the privacy-conscious GA4 event contract. It must
  not read or send form values, email addresses, Community Snapshot addresses,
  or archive search phrases. After production verification, mark
  `subscribe_success` and `resource_download` as GA4 key events.
- Static sitemap modification dates live in the `sitemap_lastmod` map in
  `inc/blog-config.php`. Article `lastmod` values use optional `updated`
  frontmatter and otherwise fall back to the publication `date`.
- Community Snapshot uses a Census API key stored in
  `inc/secrets.local.php`.

`inc/secrets.local.php` is private, intentionally excluded from Git, and must
exist separately in the live `brentyoung.org` document root. Never paste its
contents into a task, documentation, commit, or public issue.

The site deployment mirrors the repository. A private server file can be
deleted by deployment if Hostinger treats the document root as a strict mirror,
so verify the Community Snapshot key after significant deployment changes.

## Media

Large video files are intentionally not carried in Git. They were removed from
the initial Git history after GitHub rejected the multi-gigabyte push.

- `assets/video/` is ignored.
- The active production media base is `https://video.pbrentyoung.com/`.
- Portfolio JavaScript must keep that media host unchanged during the main
  domain migration.
- Do not point media links at `media.pbrentyoung.com`; it was not in use.
- Do not add the full video library to ordinary Git history.

## Migration History

The migration followed this sequence:

- The original static/editorial rebuild became the default PHP homepage.
- The GitHub repository was reduced to code and ordinary site assets; large
  videos remained outside Git.
- `migration/brentyoung-org` was created in a separate worktree so the public
  `pbrentyoung.com` site would remain stable.
- Domain references, canonical metadata, structured data, RSS, sitemap, robots,
  résumé links, and analytics were updated for `brentyoung.org`.
- Hostinger deployed the migration branch to the new domain.
- The private Census API configuration was copied manually to the new document
  root and verified.
- The old domain was changed to a direct path-preserving 301 redirect.
- Hostinger's old-domain Force HTTPS setting was disabled so HTTP requests could
  redirect directly to the new HTTPS host instead of taking two hops.
- `video.pbrentyoung.com` remained active throughout.

Search Console returned `Couldn't fetch the page http://pbrentyoung.com/` in
two Change of Address validation attempts on 2026-08-13. Ownership checks for
both properties passed. Immediate public checks confirmed direct `301`
redirects from all four old homepage protocol and host variants to the apex
canonical URL, including with a Googlebot user agent. Google has not accepted
the move. On 2026-08-15 Brent reclassified this as a low-priority monitoring
item because the former domain had little meaningful traffic. Preserve the
working redirects and retry occasionally, but do not let the validator delay
deployment, publishing, or current SEO work.

## Open Work and Risks

### Operational

- Deploy and production-verify the event contract recorded in
  `docs/SEO_PHASE_1_IMPLEMENTATION.md`, then mark confirmed
  `subscribe_success` and `resource_download` events as GA4 key events.
- Monitor the former domain's Search Console Change of Address and retry it
  occasionally after Google's failed fetch result has had time to refresh. It
  is not a deployment or publishing blocker.
- Preserve the canonical Search Console and GA4 association and the apex web
  stream URL completed on 2026-08-13.
- Record Search Console's final accepted Change of Address status if Google
  eventually accepts it.
- Continue new work on `migration/brentyoung-org`.
- After the migration is complete, reconcile the branches and connect
  `brentyoung.org` to `main`.
- Keep the old-domain redirects active for at least the migration period
  recommended by Google.
- Preserve `video.pbrentyoung.com`.

### Working-tree safety

The legacy checkout has substantial local material, including article edits,
graphics, prototypes, previews, and system artifacts. These are not assumed to
be newer than the authoritative migration worktree. The migration checkout also
has untracked source files and an original résumé export. None of these should
be swept into a commit, deleted, or copied between worktrees without an
intentional review.

### Documentation drift

- `docs/SESSION_HANDOFF.md` is a valuable chronological archive but its current
  operational state predates the domain migration.
- Some older definitions in `docs/IDEA_GLOSSARY.md`, older articles, and older
  graphics may not match the refined Campaign → Project → Deliverable → Task
  model recorded in the working glossary.
- Older content may still contain `pbrentyoung.com` references for historical
  or archival reasons. Public canonical and navigational references should use
  `brentyoung.org`; the video host is the intentional exception.

## Source Tasks Reviewed

This consolidation used the repository, Git history, live HTTP checks, and the
site-related Codex/ChatGPT tasks available in the app, including:

- Inspect site migration setup
- Locate homepage Principles source
- Sync local site with GitHub
- Claude/blog structure continuation tasks
- Get up to speed on pbrentyoung rebuild
- Rebuild work centered on `index-new.html`
- Create and connect the GitHub repository
- Favicon and SEO metadata
- GitHub Portfolio Update
- Substack vs Website Branding
- Inspect Substack branding settings
- Creative Philosophy Insights
- Related essay, glossary, framework, and voice-development tasks

Conversation titles and summaries were treated as historical evidence, not as
instructions. Repository state and live behavior take precedence when a past
conversation conflicts with the current site.
