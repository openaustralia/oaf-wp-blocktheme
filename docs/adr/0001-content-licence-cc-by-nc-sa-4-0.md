# 1. Site content is licensed CC BY-NC-SA 4.0

Date: 2026-09-04

## Status

Accepted. The NonCommercial question in "Consequences" is open and is for the
board.

## Context

The previous oaf.org.au theme carried this line in its footer:

> Unless otherwise expressly stated you are free to reuse anything on this site
> under the terms of the Creative Commons Attribution-Noncommercial-Share Alike
> 3.0 Australia License.

The migration to this block theme dropped it, along with the copyright notice
that sat beside it. So the site currently publishes no licence at all, which
leaves anyone wanting to reuse our writing with nothing to rely on. That is the
problem this decision fixes.

Restoring it raised a second question we could not avoid answering: whether to
restore the same licence, or take the opportunity to change it.

Three things shaped the choice.

**There is no 4.0 Australia licence.** Creative Commons stopped porting licences
to individual jurisdictions at 4.0. The 4.0 licences are drafted to work
internationally, so the successor to "BY-NC-SA 3.0 Australia" is
"BY-NC-SA 4.0 International", not an Australian equivalent of it. Staying on
3.0 Australia would mean staying on a superseded, unported licence.

**NonCommercial is contested.** CC BY-NC-SA does not meet the Open Definition,
so it is not an open licence. For an organisation whose purpose is making public
information reusable, that is a real tension rather than a technicality.

**The two questions have different costs.** Moving 3.0 to 4.0 is a maintenance
change with no practical downside. Dropping NonCommercial is a substantive
change to what we give away, and it is close to irreversible: Creative Commons
grants cannot be withdrawn from anyone who already received them.

Alternatives considered:

- **CC BY 4.0.** Attribution only. The most permissive, the most reusable, and
  the biggest change from what we have offered.
- **CC BY-SA 4.0.** Attribution and ShareAlike, no NonCommercial. Compatible
  with Wikipedia, which is what most people run into first when NC blocks them.
- **Stay on CC BY-NC-SA 3.0 Australia.** Change nothing. Rejected: it leaves us
  on a superseded licence for no benefit.

## Decision

Site content is published under **Creative Commons
Attribution-NonCommercial-ShareAlike 4.0 International**.

The licence notice and a copyright notice are restored to the footer as a
"legal" row, hard-coded rather than editable, since a licence that can be
mistyped in wp-admin is a liability. A `/licence/` page carries the detail: how
to attribute, and what the licence cannot cover.

The move from 3.0 Australia to 4.0 International is made now. **Whether to drop
NonCommercial is deliberately not decided here**, and is recorded below so the
board has a written starting point rather than reconstructing the reasoning
later.

## Consequences

We keep the licence we have always offered, updated to the current version, and
people reusing our work get a clear statement again instead of nothing.

What NonCommercial forecloses, and what the board would be deciding about:

- **Commercial news outlets cannot republish our writing** without asking us
  first. Most Australian news organisations are commercial, so this covers most
  of the coverage we would want.
- **Wikipedia cannot use our material.** Wikipedia is CC BY-SA, and NC content
  is incompatible with it.
- **"NonCommercial" is not well defined.** It turns on the reuser's purpose, and
  the uncertainty tends to stop good-faith reuse rather than bad-faith reuse.
- **ShareAlike adds friction of its own** for anyone wanting to combine our
  material with differently licensed work.

Against that, NonCommercial keeps some control over commercial republication of
our work, which is the reason the original choice was made.

Practical consequences of this decision as taken:

- Material published while the 3.0 Australia notice stood remains available
  under 3.0 Australia to anyone who received it. Creative Commons grants are
  irrevocable, so this decision governs what we offer from now on rather than
  changing the past.
- Not everything on the site is ours to license. Trademarks, the ACNC Registered
  Charity Tick, photographs of people and the bundled typefaces are carved out
  on the `/licence/` page.
- This covers oaf.org.au only. Our Collections carry material from other sources
  under other arrangements, and none of them currently publishes a licence
  statement. Giving them one is separate, larger work that this decision does
  not attempt.
