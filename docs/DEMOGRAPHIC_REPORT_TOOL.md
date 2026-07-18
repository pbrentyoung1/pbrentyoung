# Community Demographic Report

## Build status

A working, no-index proof is available locally at `/community-snapshot`.

The proof currently:

- Matches a complete campus address with the Census Geocoder, with the ArcGIS World Geocoding Service as a non-stored fallback when Census has no address record.
- Builds a fixed fifteen-mile straight-line study area.
- Uses ACS 2024 block-group boundaries for the current report and ACS 2019 boundaries for the earlier non-overlapping period.
- Calculates partial-boundary weights instead of counting every geography touched by the circle in full.
- Uses weighted Census tracts for language, poverty, and one-year mobility because those tables are not published at the block-group level.
- Compares the local estimate with the campus county and the United States.
- Creates the correct ARDA county report link.
- Keeps the Census API key on the server.
- Does not cache the submitted address, its matched result, or its coordinates.
- Caches reusable county-level Census data for thirty days to reduce repeat API work. Cached files contain public aggregate data, not the submitted address.
- Provides a print-friendly browser view.
- Builds and replaces reports in place, leaving the browser on a clean GET page. A second address replaces the first report, and refreshing returns to the blank tool.
- Downloads the displayed report as a CSV spreadsheet for Excel, Numbers, or Google Sheets without making another data request or storing the submitted address.
- Allows up to two minutes for a cold multi-service report request instead of relying on PHP's common thirty-second default.

The page remains `noindex, nofollow` until the data, language, and visual design have received an editorial review.

### Primary proof result

For 12400 Walden Road, Montgomery, Texas 77356, the current proof finds 184 ACS 2024 block groups intersecting the study area. Fifty-three cross the boundary and receive partial weight. The local estimate is approximately 308,700 people and 115,100 households.

### Method caveat discovered during the build

Partial geography weights are based on the share of land area inside the circle. This is more careful than counting a touched geography in full, but it still assumes residents are distributed evenly inside each block group or tract. Water, undeveloped land, and concentrated housing can make that assumption imperfect. The public report must describe local figures as estimates, not exact counts.

### Before public release

- Review every measure and label against the Census table definitions.
- Add margins of error or a clear uncertainty treatment.
- Decide whether the confirmation step should appear before the full report is generated.
- Add lightweight abuse protection without collecting user profiles or saved addresses.
- Configure `CENSUS_API_KEY` in the production environment.
- Complete the art-direction pass and choose which measures deserve charts rather than tables.

### Address fallback finding

The Census Geocoder does not contain every valid campus address. Pacific Christian Center at 3435 Santa Maria Way, Santa Maria, California 93455 is one confirmed example. The address is listed by the church and the California Department of Education but returns no Census match.

The tool now tries Census first. When Census returns no address, it makes one non-stored request to the ArcGIS World Geocoding Service and accepts only a high-confidence address-level result. Those coordinates are then sent back to Census to identify the state and county. The report discloses when the fallback supplied the location. Neither geocoder result is cached.

## Working idea

Create a simple interactive tool that helps a church understand the community surrounding a campus. A user enters a church address and receives a clear demographic snapshot of the people living within a fifteen-mile radius.

The report is a starting point for listening and persona research. It should not try to become a complete community-needs assessment or compete with specialized research tools.

> Demographics show us where to listen. Interviews help us understand the people who live there.

## Scope decision: simple first, then point the way

The first release stays intentionally small. This tool has one job: give a church an honest first portrait of its community and teach it what to ask next. It is not a research platform, and it should not try to become one.

Deeper reporting already exists and is done well by others. Rather than rebuilding it, the report ends with the Go Deeper directory that sends people to the right instrument for their next question. Curation is part of the hospitality.

## Why this belongs here

The tool puts the ideas in *A Persona Is More Than a Demographic* into practice:

- Communication is stewardship.
- A persona is more than a demographic.
- Data can give us a place to begin, but it cannot replace listening.
- Research should help a church prepare a place, not reduce people to prospects.

## Primary user

A church communicator, pastor, ministry leader, or consultant who needs an accessible picture of the community surrounding a church campus.

The user should not need experience with Census data or formal audience research. The report should translate public data into plain language without overstating what it proves.

## Primary test campus

Use this public campus address as the primary development and visual-quality test:

**12400 Walden Road, Montgomery, Texas 77356**

Census Geocoder verification:

- Matched address: 12400 WALDEN RD, MONTGOMERY, TX 77356
- Coordinates: 30.395599709227, -95.612062698701
- County: Montgomery County, Texas
- County FIPS: 48339
- ARDA county report: `https://www.thearda.com/us-religion/census/congregational-membership?c=48339&t=0&y=2020`

Additional quality-assurance locations should eventually include a dense urban campus, a rural campus with large Census block groups, and a campus whose fifteen-mile radius crosses a state boundary.

## Input

The preferred input is the full address of a church campus.

**Field label:** Where is your church located?

**Placeholder:** Street address, city, state, and ZIP

**Privacy note:** We send this address to the U.S. Census Bureau to find its location. If Census cannot match it, we use ArcGIS as a backup. We do not save the address or result.

A complete address gives the report a precise center point. The tool may accept a ZIP code as a fallback, but a ZIP-only report should be labeled as approximate.

**ZIP fallback note:** This report uses the approximate center of [ZIP]. Enter a full campus address for a more accurate report.

The interface should show the matched location and let the user confirm it before generating the report.

## Study area

The first version should use a fifteen-mile straight-line radius around the confirmed campus location.

This is not the same as a fifteen-mile drive or a fifteen-minute trip. The report should state that distinction clearly.

The tool will:

- Convert the address into latitude and longitude.
- Identify Census block groups within the fifteen-mile study area.
- Retrieve current American Community Survey five-year estimates.
- Combine the data into one local snapshot.
- Compare major measures with the campus county and the United States.
- Cache completed reports by location and Census data year.

The methodology should be accurate enough to be trustworthy, but it does not need to become the focus of the experience. A short note can explain that the figures are estimates assembled from Census geographies and may include margins of error.

## Basic demographic report

The first report should remain concise.

### People

- Estimated population
- Age groups
- Race and ethnicity
- Languages spoken at home

### Households

- Total households
- Average household size
- Households with children
- People living alone
- Basic family and household composition

### Daily context

- Household income
- Poverty
- Educational attainment
- Homeowners and renters
- Recent movers
- Internet access

These measures are enough to help a church see the broad contours of its community without turning the page into a Census dashboard.

## Then and now

The report should include a compact trend section showing how the community is changing.

Compare the current American Community Survey five-year estimate with the most recent non-overlapping five-year period. For example, a 2020-2024 estimate can be compared with 2015-2019. Avoid comparing overlapping periods because they share several years of survey responses and can make change appear more certain than it is.

The first trend section should focus on a handful of useful measures:

- Estimated population growth or decline
- Changes in major age groups
- Changes in households with children
- Changes in racial and ethnic composition
- Changes in languages spoken at home
- Changes in homeownership and renting
- Changes in internet access

Changes in percentages should be shown as percentage-point differences. Counts and percentages should include a simple uncertainty note when the margins of error make the apparent change unreliable.

Income trends require inflation adjustment before comparison. The first release should either convert earlier dollar values into current dollars using an authoritative inflation measure or omit the income trend until that calculation is in place.

**Section label:** Then and Now

**Introductory line:** A community is not a snapshot. These changes help show how the people surrounding this campus are shifting over time.

## Comparisons

Each major measure should compare three views:

- The fifteen-mile study area
- The county containing the campus
- The United States

The interface should name the county rather than use the vague label "regional."

For example:

**15-mile radius | Tarrant County | United States**

Comparisons should be descriptive, not judgmental. The report can identify a meaningful difference, but it should not decide what that difference means for the church.

## Data source

The first release should rely primarily on the American Community Survey five-year estimates from the U.S. Census Bureau.

Supporting services:

- U.S. Census Geocoder for primary address matching and coordinates
- ArcGIS World Geocoding Service for a non-stored fallback when Census cannot match a valid address
- U.S. Census TIGERweb for geographic boundaries
- American Community Survey five-year estimates for demographic data

The Census API is free but requires an API key. The key must remain on the server and should never appear in browser code.

## Go deeper

The report should provide a short list of trusted external resources instead of trying to reproduce every kind of community research.

### Religious landscape

Link directly to the appropriate ARDA county membership report using the county FIPS code returned by the Census lookup:

`https://www.thearda.com/us-religion/census/congregational-membership?c={COUNTY_FIPS}&t=0&y=2020`

**Link label:** Explore [County Name]'s Religious Landscape on ARDA

**Context note:** ARDA's county report describes congregations and reported adherents. It does not measure the beliefs or attendance of every resident.

### Additional resources

- **ARDA Community Profile Builder:** Build a fuller radius-based demographic and religious report; the closest existing tool to a complete version of this one.
- **Census data:** Explore detailed social, economic, housing, and demographic tables.
- **Pew Research Center:** Explore broader religious belief and practice.
- **CDC PLACES:** Explore local health estimates.
- **National Center for Education Statistics:** Explore schools and school districts.
- **USDA Food Access Research Atlas:** Explore food access and community-needs indicators.

Commercial platforms such as MissionInsite offer subscription reporting used widely in the church world. The directory can mention that they exist without endorsing them; churches should evaluate the need before paying for certainty the data cannot deliver.

These should be presented as a quiet resource list after the report, not as additional dashboards inside the tool.

## Connection to persona research

The report should not automatically generate a finished persona from demographic data. That would give stereotypes the appearance of research.

After the demographic snapshot, the tool should encourage the next step:

> This data gives you a place to begin. It cannot tell you what your neighbors fear, what they hope for, why they are looking for a church, or what might help them trust you. The next step is to listen.

Provide links to:

- *A Persona Is More Than a Demographic*
- The Future Congregation Journey
- A future interview guide or persona worksheet

## Report experience

The report should feel like an editorial field guide rather than business-intelligence software. It should remain elegant, classic, useful, and consistent with the rest of the site.

Possible structure:

- Campus and study-area summary
- A concise community portrait
- A compact Then and Now trend section
- A small number of clear charts
- Local, county, and national comparisons
- Questions the data suggests asking
- Links for deeper research
- Print-friendly presentation

The report should clearly distinguish facts, estimates, and interpretation.

## Privacy and care

- Do not save the submitted address.
- Do not collect names or household-level information.
- Do not expose the Census API key.
- Do not imply that aggregate data describes every person within the radius.
- Do not infer spiritual condition, family circumstances, trauma, or individual intent.
- Display the Census data year and acknowledge that the figures are estimates.

## First release

The first release includes:

- Full campus address input
- ZIP-code fallback with an accuracy notice
- Address confirmation
- Fixed fifteen-mile radius
- Basic Census demographic snapshot
- Selected demographic trends using non-overlapping ACS periods
- County and national comparisons
- Responsive charts and tables
- Link to the correct ARDA county report
- Links to other trusted research tools
- Persona and interview next steps
- Print-friendly presentation
- Server-side caching

## Outside the first release

- User accounts and saved reports
- Drive-time analysis
- Multiple-campus comparison
- Health, school, food-access, or business data integrations
- A local congregation directory
- AI-generated personas
- AI interpretation of demographic data
- Commercial lifestyle or consumer segmentation
- Automated PDF generation

## Open questions

- Should the radius always be fifteen miles, or should users be able to choose a smaller area?
- Which measures are important enough to earn space in the first report?
- Does the first release need a map, or is a location summary sufficient?
- What charts best support comparison without making the report feel like a dashboard?
- What is the primary call to action after someone reads the report?
- Should the tool live as a standalone resource, inside the persona article, or both?

## Current decisions

- Prefer a full campus address and accept ZIP code as an approximate fallback.
- Use a straight fifteen-mile radius.
- Keep the demographic report basic.
- Use free public Census data.
- Include a small set of responsibly calculated trend measures.
- Compare local measures with the campus county and the United States.
- Link to ARDA and other specialized sites instead of reproducing their tools.
- Treat demographic data as a beginning for listening, not a substitute for persona research.
- Keep the first version free of accounts and saved personal information.
