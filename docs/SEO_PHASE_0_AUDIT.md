# SEO Phase 0 Audit

Audit date: 2026-08-13

This document establishes the pre-strategy SEO baseline for `brentyoung.org`.
It records observed evidence, separates migration problems from site problems,
and provides the ranked backlog that should guide Phase 1.

No production code, Search Console setting, Analytics setting, form, or
deployment was changed during this audit.

## Post-Audit Actions: 2026-08-13

After Brent approved execution of the Phase 0 account cleanup:

- The redundant `https://www.brentyoung.org/sitemap.xml` submission was
  removed. Search Console now lists only the successful canonical apex sitemap,
  with 21 discovered pages.
- The stale GA4 Search Console link for `pbrentyoung.com` and the retired
  `website` stream was removed.
- The canonical `brentyoung.org` Domain property was linked to the GA4
  `Pbrentyoung` property and its `New Site` stream, ID `15348969115`.
- The `New Site` stream URL was updated from
  `https://www.brentyoung.org` to `https://brentyoung.org`. Measurement ID
  `G-F1TD8KH28H` was unchanged, and the stream remained active.
- The Change of Address submission was attempted twice. Ownership validation
  passed for both properties, but Google's required homepage redirect check
  returned `Couldn't fetch the page http://pbrentyoung.com/`, so Google did not
  accept the move. Independent checks immediately confirmed that all four old
  homepage protocol and host variants return a direct `301` to
  `https://brentyoung.org/`, including requests using a Googlebot user agent.
  This remains an external Google validation or cache issue, not an observed
  redirect failure.

## Planning Priority Update: 2026-08-15

Brent confirmed that the former domain had little meaningful traffic. The
unaccepted Change of Address is therefore a low-priority monitoring item, not a
gate for deployment, publishing, measurement work, or keyword planning.

Keep the verified path-preserving redirects active. Retry Google's validator
occasionally, but do not change the redirects or spend significant project time
on the validator unless new evidence shows a real traffic, indexing, or redirect
problem.

## Executive Finding

The public site is crawlable and technically coherent, but Google's migration
and discovery state is incomplete.

The canonical Search Console property reports:

- 3 indexed pages
- 17 pages marked `Discovered - currently not indexed`
- 2 expected HTTP redirect URLs
- 2 clicks and 8 impressions in the available reporting period

The former `pbrentyoung.com` property still reports 42 indexed pages. Google
has not accepted the domain move because its Change of Address validator could
not fetch the old HTTP homepage, despite the working public redirect.

This is the most important Phase 0 conclusion. The site does not currently show
evidence of a broad crawlability failure:

- The canonical sitemap succeeds and reports 21 discovered pages.
- All 21 canonical sitemap URLs returned `200` during the audit.
- All 67 unique internal page links found across those pages returned `200`.
- Canonical redirects are direct and preserve paths and query strings.
- A live URL Inspection test for the Evangelistic Marketing framework returned
  `URL is available to Google` and `Page can be indexed`.
- Search Console reports valid robots files, no manual actions, and no security
  issues.

The first recovery work should therefore strengthen and complete the migration
signals, then monitor discovery. It should not begin with speculative changes
to otherwise healthy page rendering.

## Audit Scope

The audit covered:

- Repository and deployment configuration
- Public status codes, redirects, robots, and sitemap
- Page titles, descriptions, canonicals, H1s, and structured data
- Internal page links
- Search Console indexation, performance, settings, links, and live inspection
- Google Analytics stream health and current traffic
- Newsletter placement and tracking implementation
- PageSpeed Insights and automated accessibility signals
- Social-preview source assets
- Published-content authority clusters

Search Console and Analytics evidence was read through Brent's authenticated
Google session. The audit did not export account data or mutate external
settings.

## Domain Migration and Indexation

### Search Console properties

The account includes Domain properties for both `brentyoung.org` and
`pbrentyoung.com`. Brent is a verified owner of the canonical property.

The `brentyoung.org` property was added on 2026-07-29. Its Page Indexing report
was last updated on 2026-08-06.

| State | Pages | Notes |
| --- | ---: | --- |
| Indexed | 3 | Homepage and two essays |
| Discovered, currently not indexed | 17 | Blog index, 13 essays, glossary, and two framework/tool pages |
| Page with redirect | 2 | Expected HTTP homepage variants |

The indexed URLs were:

- `https://brentyoung.org/`
- `https://brentyoung.org/blog/why-your-church-communications-feel-chaotic`
- `https://brentyoung.org/blog/your-church-isnt-for-everyone`

The 17 discovered but unindexed URLs all showed `Last crawled: N/A` in the
report. The newest essay, `the-church-the-toy-and-the-cardboard-box`, was not
present in the 2026-08-06 index report even though it is present in the sitemap
read on 2026-08-13.

The former `pbrentyoung.com` property reports 42 indexed pages and 10 excluded
pages. That property currently reports very little search activity, but its
larger indexed footprint remains important migration evidence.

### Change of Address

The `pbrentyoung.com` Change of Address submission to the verified
`brentyoung.org` Domain property was attempted twice after the audit. Google's
ownership checks passed, but its required redirect check returned
`Couldn't fetch the page http://pbrentyoung.com/`. No completed move is
recorded.

Public requests to `http://pbrentyoung.com/`, `https://pbrentyoung.com/`, and
both `www` variants returned a direct `301` to `https://brentyoung.org/` during
the same session. The next Administration & Deployment action is to allow
Google's validator time to refresh, then retry without changing the verified
redirect unless new evidence shows a real failure.

### Sitemap state

Two sitemaps were submitted to the canonical Domain property at audit time:

| Sitemap | Last read | Status | Discovered pages |
| --- | --- | --- | ---: |
| `https://brentyoung.org/sitemap.xml` | 2026-08-13 | Success | 21 |
| `https://www.brentyoung.org/sitemap.xml` | 2026-08-04 | Success | 20 |

The `www` sitemap resolved through the canonical host redirect and was
unnecessary. It was removed after the audit. The apex sitemap is now the only
submitted sitemap.

The sitemap's current `lastmod` values are derived from deployment filesystem
timestamps. All public entries showed `2026-08-13` after the latest deployment.
This is not an accurate editorial modification signal. Future implementation
should use explicit content modification dates or another durable source that
survives deployment.

### URL Inspection

The stored Google index state for
`/evangelistic-marketing-framework` reported:

- URL is unknown to Google
- No referring sitemap detected
- No crawl recorded

The live test on the same date reported:

- URL is available to Google
- Page can be indexed
- No availability failure

This supports a discovery and processing diagnosis rather than a page-level
technical block.

## Public Crawl and Redirect Baseline

All 21 URLs in the canonical sitemap returned `200` with the expected HTML
content type.

A page-link crawl found 67 unique internal page destinations:

- 67 returned `200`
- 0 redirected
- 0 returned a client error
- 0 returned another error

The audit also verified:

- `http://pbrentyoung.com/blog/brand-is-the-referee?utm_source=audit`
  redirected in one hop to the matching canonical HTTPS URL and preserved the
  query string.
- `https://www.brentyoung.org/blog` redirected in one hop to the apex canonical
  URL.
- The public robots file allows crawling and names the apex sitemap.

Search Console reports 102 crawl requests for the canonical property over the
last 90 days. It has no Core Web Vitals field dataset yet.

## Search Appearance and Structured Data

Every canonical sitemap page has:

- One H1
- A unique title
- A meta description
- A self-referencing canonical
- A crawl-permitting state

The homepage omits an explicit robots meta tag, which correctly defaults to an
indexable state.

Current structured data coverage is:

| Page type | Current markup |
| --- | --- |
| Homepage | `Person` |
| Blog index | `CollectionPage` |
| Article | `BlogPosting` |
| Glossary | `DefinedTermSet` |
| Evangelistic Marketing framework | None |
| Future Congregation Journey | None |

The next structured-data implementation should establish one stable Person
entity and connect articles to it. It should also add honest page-level markup
where a supported type fits. Structured data should describe the visible page,
not be added simply to increase markup volume.

Article markup currently lacks an explicit `dateModified`. The site also lacks
a dedicated About page and Breadcrumb markup. These are useful entity and
navigational gaps, not the cause of the present indexation problem.

### Filter and search URLs

Blog topic, tag, search, and pagination parameters currently generate
self-referencing canonicals and remain indexable. This creates two decisions
for Phase 1:

- Promote selected topic pages into stable, useful authority hubs with unique
  introductions and intentional metadata.
- Mark arbitrary search results and low-value parameter combinations `noindex,
  follow` and canonicalize them according to the chosen policy.

Do not leave every possible query-string combination eligible for indexing.

## Search Performance Baseline

Search Console contains very little performance data, so current rankings are
not a useful strategy signal yet.

### Canonical property

Available period: 2026-07-28 through 2026-08-11

| Metric | Value |
| --- | ---: |
| Clicks | 2 |
| Impressions | 8 |
| CTR | 25% |
| Average position | 4.4 |

Query rows were suppressed because of the low data volume.

### Former property

Available period: 2026-07-16 through 2026-08-11

| Metric | Value |
| --- | ---: |
| Clicks | 0 |
| Impressions | 11 |
| CTR | 0% |
| Average position | 45.9 |

Visible queries included `church chaos` and `church for everyone`, each with
one impression.

The properties do not contain a useful sixteen-month history despite that date
range being selected. Baseline comparisons should begin with the evidence
available now and retain future monthly exports.

## Link Baseline

The canonical Search Console property reports 50 external links:

- 43 point to `why-your-church-communications-feel-chaotic`
- 7 point to `your-church-isnt-for-everyone`
- All 50 are attributed to `pbrentyoung.com`

The former property reports no external links. Search Console therefore shows
no independent referring domains at the time of the audit. The current 50-link
count represents migration redirects, not outside authority.

This makes original resources, partnerships, teaching, research, and useful
case studies the appropriate future link-earning strategy.

## Analytics and Conversion Baseline

The GA4 property `Pbrentyoung` is receiving data. Its canonical stream is:

- Stream name: `New Site`
- Stream URL at audit time: `https://www.brentyoung.org`
- Measurement ID: `G-F1TD8KH28H`
- Data collection: active during the preceding 48 hours

The measurement ID matches the site configuration. After the audit, the stream
URL was updated to `https://brentyoung.org`, and Search Console confirmed the
canonical property association with the GA4 `Pbrentyoung` property and
`New Site` stream.

### Seven-day GA4 snapshot

| Metric | Value |
| --- | ---: |
| Active users | 32 |
| New users | 31 |
| Event count | 281 |
| Key events | 0 |

Visible session groups were:

| Channel | Sessions |
| --- | ---: |
| Direct | 27 |
| Organic Social | 12 |
| Organic Search | 3 |

These numbers are small and may include Brent or project testing. They are a
technical baseline, not a performance judgment.

### Newsletter path

The blog index has one newsletter entry point. It opens a working modal with an
email field, submit button, consent-oriented supporting copy, success state,
error state, and RSS alternative. Article pages also include a subscription
entry point.

The homepage has no newsletter entry point.

No custom `gtag` events are implemented for:

- Subscription link view or click
- Modal open
- Form submission
- Subscription success
- Related-content click
- Resource visit or download

GA4 therefore reports zero key events and cannot currently measure organic
subscription conversion. The form itself was not submitted during the audit,
so delivery remains an Administration & Deployment verification task.

## Performance and Accessibility Baseline

PageSpeed Insights returned no real-user field dataset for the homepage.

### Homepage lab results

| Metric | Mobile | Desktop |
| --- | ---: | ---: |
| Performance | 73 | 96 |
| Accessibility | 93 | 93 |
| Best Practices | 100 | 100 |
| SEO | 100 | 100 |
| FCP | 3.3 s | 0.9 s |
| LCP | 4.8 s | 1.2 s |
| Total Blocking Time | 190 ms | 20 ms |
| CLS | 0.006 | 0.009 |
| Speed Index | 3.3 s | 1.0 s |

The mobile LCP does not meet the target of 2.5 seconds. The largest reported
opportunities were:

- Render-blocking requests, estimated mobile savings of 2.59 seconds
- Image delivery, estimated mobile savings of 664 KiB
- Cache lifetimes
- Unused CSS and JavaScript
- Non-composited animations

Desktop image delivery showed an estimated 1,201 KiB opportunity even though
the overall desktop score was strong.

Automated accessibility findings were:

- Insufficient foreground/background contrast
- Links without discernible names in the audited rendering
- Identical links with inconsistent purpose

These need element-level reproduction and manual keyboard and screen-reader
verification before implementation. Automated accessibility scoring is not a
substitute for a manual audit.

The representative article PageSpeed run was throttled by Google's testing
service and did not return a valid report. It remains an open Phase 1 baseline
item.

## Social Preview Baseline

Every article exposes article-specific Open Graph and Twitter metadata, uses
the article banner, reports the actual image type and dimensions, and includes
an image description.

Of the 16 published article banners:

- 2 are 1200 by 630
- 14 are 1024 by 559
- 0 are missing

The 1024 by 559 images are close to, but not the same as, the common 1.91:1
social-preview ratio. Their rendered crops should be checked across LinkedIn,
Facebook, messaging apps, and other priority channels. New source banners
should use a consistent 1200 by 630 master unless the article has a deliberate
reason not to.

Framework and collection pages use the generic site preview. Dedicated
previews would improve recognition for the Evangelistic Marketing framework,
Future Congregation Journey, glossary, and future Planning Starter Kit.

## Provisional Authority Map

This map is an SEO architecture proposal, not a change to Brand positioning or
editorial ownership. Brand & Strategy and Content & Blog should review final
hub names, search language, and page assignments.

### Church Communications Strategy

- The Right Story, Told the Right Way
- The Message Doesn't Change. The Mix Does.
- Make Plans, Not Piles
- The Church, the Toy, and the Cardboard Box

### Church Brand Strategy

- Your Church Already Has a Brand
- Brand Is the Referee
- The Most Expensive Word in Church Communications
- Change vs. Polish
- Stewardship Is What You Invest

### Church Communications Systems

- Why Your Church Communications Feel Chaotic
- Stop Calling Everything a Project
- Every Deliverable Needs One Job
- The Creative Brief Is Ministry

### Audience and Journey

- Your Church Isn't for Everyone
- A Persona Is More Than a Demographic
- Everyone Falls in Love the Same Way
- The Future Congregation Journey

### Evangelistic Marketing

- The Evangelistic Marketing Framework
- The working glossary
- The Right Story, Told the Right Way
- Everyone Falls in Love the Same Way
- Future John 4 series and book work when editorially ready

Several essays can support more than one hub. Each page should still have one
primary search job to avoid internal competition.

## Ranked Phase 1 Backlog

### Migration monitoring actions

- Retry the `pbrentyoung.com` Change of Address occasionally after Google's
  failed homepage fetch has had time to refresh. Both property ownership checks
  already pass, and this is not a deployment or publishing blocker.
- Keep the current direct, path-preserving old-domain redirects active.
- Keep the canonical apex sitemap healthy. The redundant `www` submission was
  removed on 2026-08-13.
- Reinspect representative canonical URLs after the move is submitted.
- Monitor indexed and discovered counts weekly until the canonical sitemap is
  substantially processed.

These are external-state changes and should not be performed silently inside a
design implementation task.

### Critical measurement actions

- Preserve the canonical Search Console and GA4 association completed on
  2026-08-13.
- Preserve the GA4 apex stream URL completed on 2026-08-13.
- Preserve and production-verify the privacy-conscious subscription, resource,
  related-link, and contact events implemented on 2026-08-13.
- Mark the agreed conversion events as GA4 key events.
- Establish internal-traffic filtering and a lightweight monthly baseline.

### High-impact site actions

- Preserve the durable sitemap date model implemented on 2026-08-13 and update
  explicit dates when public content changes materially.
- Preserve the `noindex, follow` policy for topic, tag, search, and pagination
  variants until an approved filter becomes a substantive authority hub.
- Add a dedicated About page and stable Person entity references.
- Extend the article `dateModified`, Breadcrumb, and stable Person markup
  implemented on 2026-08-13 to future page types where the visible content
  supports it.
- Give framework and future resource pages appropriate metadata and dedicated
  social previews.
- Add a deliberate homepage newsletter path without turning the homepage into
  a lead-generation page.
- Production-verify the related-content click tracking implemented on
  2026-08-13.

### Performance and accessibility actions

- Identify and improve the homepage mobile LCP element.
- Reduce render-blocking work and unnecessary CSS and JavaScript.
- Audit homepage image selection, dimensions, formats, and loading behavior.
- Reproduce and fix the reported contrast and link-name failures.
- Run manual keyboard, focus, screen-reader, zoom, and reduced-motion checks.
- Capture valid article, glossary, and framework PageSpeed baselines.

### Content architecture actions

- Confirm the five authority hubs with Brand & Strategy and Content & Blog.
- Assign each published page one primary search job.
- Create query maps from Search Console data as it grows, supplemented by
  intentional search research rather than keyword volume alone.
- Build the Church Communications Planning Starter Kit as the first
  search-and-conversion centerpiece.
- Add useful hub-to-essay, essay-to-framework, glossary, case-study, and
  resource paths.
- Develop independent authority through useful resources, case studies,
  teaching, partnerships, and original research.

## Phase 0 Exit Status

The evidence-gathering portion of Phase 0 is complete.

Completed:

- Migration state identified
- Canonical URL inventory verified
- Crawl and internal-link baseline captured
- Metadata and structured-data baseline captured
- Search Console and GA4 access verified
- Performance and accessibility baseline captured for the homepage
- Newsletter placement and tracking gaps identified
- Social-preview source assets inventoried
- Provisional authority clusters mapped
- Ranked Phase 1 backlog created
- Redundant `www` sitemap submission removed
- Canonical Search Console property linked to the `New Site` GA4 stream
- GA4 stream URL corrected to the apex host

Open dependencies:

- Eventual Google validation and acceptance of the Change of Address, tracked
  as a low-priority monitoring item
- A valid representative-article PageSpeed report
- Manual accessibility verification
- Brand and Content review of authority-hub language and page assignments
- Administration verification of newsletter delivery and privacy-conscious
  analytics behavior

Phase 1 should prioritize measurement integrity and discovery before the site
expands its SEO architecture. The old-domain validator can remain a parallel,
low-priority monitoring item.
