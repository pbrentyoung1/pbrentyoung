# SEO Phase 1 Implementation

Implementation date: 2026-08-13

This document records the first technical SEO implementation sprint following
`SEO_PHASE_0_AUDIT.md`. It defines the measurement contract, sitemap date
source, parameter indexing policy, and article entity markup that the site now
expects.

## Measurement Contract

The shared tracking layer lives in `js/analytics.js` and is loaded by the site
footer. It sends only known page, content, resource, and placement identifiers.
It does not read or send email addresses, form values, Community Snapshot
addresses, search phrases, or other user-provided text.

| Event | Trigger | Important parameters | Key event policy |
| --- | --- | --- | --- |
| `subscribe_cta_view` | A subscription link becomes meaningfully visible | `page_path`, `link_location` | Diagnostic |
| `subscribe_open` | The subscription dialog opens | `page_path`, `link_location` | Diagnostic |
| `subscribe_submit` | The subscription form is submitted | `page_path`, `link_location` | Diagnostic |
| `subscribe_success` | Brevo displays its success state | `page_path`, `link_location` | Mark as a GA4 key event after production verification |
| `subscribe_error` | Brevo displays its error state | `page_path`, `link_location` | Diagnostic |
| `related_content_click` | A reader follows a related article | `page_path`, `content_source`, `destination_path`, `link_location` | Diagnostic |
| `resource_download` | A known document link or generated Community Snapshot CSV is downloaded | `page_path`, `resource_name`, `resource_path`, `link_location` | Mark as a GA4 key event after production verification |
| `contact_click` | A reader follows an email contact link | `page_path`, `link_location` | Diagnostic until contact quality can be evaluated |

Subscription success and errors are observed from Brevo's existing visible
form states. Production verification must use a controlled test address and
confirm one event per interaction before either key event is enabled in GA4.

## Sitemap Modification Dates

Deployment filesystem timestamps are no longer used.

- Static-page dates live in the `sitemap_lastmod` map in
  `inc/blog-config.php` and must be updated only when the public page changes
  materially.
- Article modification dates come from an optional `updated: YYYY-MM-DD`
  frontmatter field.
- An article without `updated` uses its durable publication `date`.
- The blog index uses the newest article modification date.
- The Community Snapshot is now included in the sitemap.

The generated sitemap contains 22 canonical URLs at this implementation point.

## Blog Parameter Indexing Policy

The clean `/blog` page remains `index, follow` with a self-referencing
canonical.

Topic, tag, search, and pagination variants use `noindex, follow` and point
their canonical to `/blog`. These pages remain useful navigation for readers
and crawlers, but they are not independent search landing pages because they
do not yet contain unique editorial introductions or distinct search jobs.

If a topic later becomes a substantive authority hub, it should receive a
stable path, unique copy, intentional metadata, and explicit Brand and Content
approval before becoming indexable.

## Article Entity Markup

The homepage defines the stable Person identifier
`https://brentyoung.org/#person`. Blog collections and articles refer to that
identifier.

Every article now emits an `@graph` containing:

- The stable Brent Young `Person`
- `BlogPosting` with `datePublished` and `dateModified`
- `BreadcrumbList` for Home, Blog, and the current article

When an article has an `updated` date different from its publication date, the
updated date also appears visibly in the article byline.

## Verification

The implementation passed PHP syntax checks, JavaScript syntax checks,
Community Snapshot calculation tests, structured-data JSON parsing, sitemap
date and URL assertions, whitespace validation, and local browser checks for
the subscription dialog, article hooks, and filtered archive metadata.

Production follow-up still requires deployment, controlled event verification
in GA4 DebugView or Realtime, key-event configuration for confirmed success and
download events, and resubmission or recrawl monitoring for the revised
sitemap.
