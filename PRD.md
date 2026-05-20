# PRD — Maldives Local Services Marketplace (working title)

**Status:** Draft v0.4
**Owner:** @moosashifaz
**Last updated:** 2026-05-20

> This document captures the v0.1 product thinking. Sections marked **[DECIDE]** are open
> questions that block scope. Sections marked **[ASSUMPTION]** are working assumptions
> we should confirm but won't block on.

---

## 1. Summary

A two-sided marketplace connecting tourists *already in the Maldives* with local
service providers on inhabited islands — laundry, souvenir shops, local food,
training/classes, experiences, retail, wellness, and other day-to-day services
consumed *during* a stay. The platform handles discovery, booking, payment, and trust,
with [BML Swipe](https://github.com/BML-Digital/swipe-merchants-dev) as the domestic
settlement rail for paying providers.

**Explicitly NOT a part of this product:** accommodation / room booking, arrival
transfers (airport → island), and inter-island transport. Those are large,
well-served categories with entrenched players (Booking.com, Airbnb, resort
transfer desks). The tourist arrives and is housed via someone else; this app
covers everything they spend on *while they are here*. See §18.

The product exists because today this in-stay services market runs on word of mouth,
paper signs at the jetty, and cash — which is inconvenient for tourists and
invisibility-as-a-business-model for providers.

## 2. Problem statement

### 2.1 What's broken for tourists

- **No discovery layer for non-resort services.** A guesthouse tourist on Maafushi has
  no equivalent of Yelp/Google Maps that's actually populated for local offerings.
- **Cash dependency off-resort.** Local operators are largely cash-only or have flaky POS.
  ATMs are scarce outside Malé.
- **Pay-upfront-in-cash deposits** for excursions booked through guesthouses,
  with no escrow and no clean refund path on cancellations (weather, illness, no-show).
- **Price opacity.** Verbal pricing on dhonis and at stalls. TGST often not itemized.
- **Quality / legitimacy uncertainty.** Tourists hand cash to strangers with no review
  signal or operator verification.
- **Language gap.** Provider signage and verbal communication is often in Dhivehi.

### 2.2 What's broken for providers

- **Distribution.** Reach is limited to whoever walks past the shop or whichever
  guesthouse owner refers them. Marketing budget is effectively zero.
- **Payment friction.** Cash creates safety, reconciliation, and tax-reporting pain.
  Card terminals are expensive and unreliable.
- **No record-keeping.** No structured receipts, no TGST audit trail, no demand history
  to plan supply (food prep, laundry capacity, class scheduling).
- **No off-season smoothing.** Demand is bursty; without a forward-booking system,
  capacity is wasted in low weeks and rationed in peak weeks.

## 3. Target users

### 3.1 Tourist (demand side)

| Segment | Description | Priority |
|---|---|---|
| Guesthouse tourist | Mid-market, on inhabited islands, price-sensitive, mobile-first | **P0** |
| Liveaboard / day-tripper | Dive groups, island-hoppers, surfers | P1 |
| Resort tourist | Premium, often confined to resort island | P2 (resort policies may block off-island engagement) |
| Domestic / resident | Maldivian staycationers and expats | P1 (these are the users for whom Swipe-as-wallet *just works*) |

### 3.2 Provider (supply side)

| Segment | Examples | Priority |
|---|---|---|
| Daily services | Laundry, tailoring, bike/snorkel rental | **P0** |
| F&B | Cafés, home-cooked meals, catering for boat trips | **P0** |
| Retail | Souvenir shops, local crafts | P1 |
| Experiences | Dive lessons, cooking classes, fishing trips, Dhivehi/yoga/free-dive training | P1 |
| Wellness | Massage, spa | P2 |
| Photography | Beach / honeymoon photographer, drone, videographer | P2 |

*(Accommodation, arrival transfers, and inter-island transport are deliberately
excluded from supply — see §1 and §18.)*

## 4. Value proposition

**For tourists:** one app to discover, book, and pay for everything you need
*during* your stay outside the resort — meals, laundry, souvenirs, classes,
excursions, spa, photography — with reviews, prices in your currency, digital
receipts, a refund path that doesn't depend on convincing a stranger, **and a
built-in financial advisor that turns USD/MVR confusion, mystery fees, and "is
this a fair price?" anxiety into transparent, in-context guidance (see §10).**

We do not book your room, your airport transfer, or your inter-island boat — by
design. An **AI concierge** (see §11) sits across the whole app, ready to answer
"where, when, how much, is this OK?" in plain language.

**For providers:** a free storefront, a booking calendar, instant digital payments to
their BML account, and a customer base they couldn't otherwise reach.

## 5. Core user journeys

### 5.1 Tourist: discover → book → consume

1. Open app; on first launch, capture trip dates and which island they are staying
   on (we don't book the room, so we don't get this for free — the app asks).
2. Browse by category (Eat, Do, Buy, Wash, Spa, Capture) or search.
3. View listing: photos, price (toggle USD/MVR), reviews, cancellation policy, host info.
4. Pick date/time (or "now" for instant services like laundry).
5. Check out — pay via card (international) or Swipe (domestic). Platform holds funds.
6. Receive booking confirmation + QR-coded voucher.
7. Provider scans voucher at delivery → platform releases funds.
8. Tourist leaves a review.

### 5.2 Provider: sign up → list → fulfill → get paid

1. Sign up with phone + national ID + business registration (or informal-operator path).
2. Verify identity (NID + selfie); link BML account for payouts.
3. Create listing(s): category, photos, pricing, availability, cancellation policy.
4. Receive booking notification (SMS + in-app).
5. Confirm booking → service is locked.
6. Scan tourist's voucher QR on delivery to mark complete.
7. Weekly payout lands in BML account via Swipe.

## 6. Service categories — MVP cut

**[DECIDE]** Which categories ship in v1? My recommendation:

- **In v1:** Eat (cafés + home-cooked meals), Wash (laundry), Buy
  (souvenirs/retail with pickup, *no delivery v1*), Do (a curated set of
  excursions/classes hand-onboarded).
- **v2:** Wellness/spa, equipment rental, photography.

Rationale: laundry + food cover the daily-need surface for a guesthouse tourist;
one of these will be opened almost every day of their stay, which is what we need
for habit formation. Souvenirs and excursions are higher-AOV but lower-frequency.
Accommodation, arrival transfers, and inter-island transport are deliberately
excluded (see §1 and §18).

## 7. Functional requirements

### 7.1 Tourist app (P0)

- Phone-number auth + optional email; passport scan for KYC-light **[DECIDE: needed for v1?]**
- Browse by category, search, map view scoped to current/selected island
- Listing detail: photo carousel, description, price in USD/MVR toggle, TGST shown
  separately, host profile, reviews, cancellation policy
- Booking calendar with availability
- Cart-less single-listing checkout (no multi-listing cart in v1)
- Payment: card (Visa/Mastercard via [DECIDE: Stripe / local PSP]) + Swipe for residents
- In-app chat with provider (post-booking only — no contact details leaked pre-payment)
- Booking history + digital receipts (TGST itemized)
- Reviews + ratings
- Multi-language: **English** v1; Dhivehi v2; Chinese/Russian v3 based on arrival data
- Financial Advisor surfaces — price-in-both-currencies, TGST itemization, trip budget
  tracker, end-of-trip spend summary (see §10 for full scope)

### 7.2 Provider app / web portal (P0)

- Sign-up with NID + business reg
- KYC: NID front/back, selfie, business registration doc, BML account linking
- Listing manager: create/edit/disable, photos, pricing rules, availability calendar
- Booking inbox: confirm, decline (with reason), reschedule
- Voucher scanner (camera-based QR scan to mark fulfilled)
- Earnings dashboard: pending, in escrow, paid out, fees, TGST collected
- Payout history
- In-app chat
- Dhivehi UI from day 1 (the provider side must speak Dhivehi)

### 7.3 Admin / ops (P0)

- Provider verification queue
- Dispute resolution console (booking-level: refund / split / release)
- Listing moderation (photos, prices, category fit)
- Reporting: TGST collected, payouts due, GMV by island/category
- Manual payout override

### 7.4 Notifications

- Booking created / confirmed / declined → SMS + push
- Cancellation / refund → SMS + push + email
- Payout settled → in-app + email
- New review → push

## 8. Non-functional requirements

- **Connectivity tolerance.** Provider voucher scan must work offline and sync on
  reconnect (boats / remote islands lose signal). Use signed QR vouchers that the
  provider app can validate offline against a cached public key.
- **Performance.** Listing browse < 2s on a 3G connection from an outer atoll.
- **Localization.** Currency, date, language; price always shown in *both* USD and MVR.
- **Accessibility.** WCAG AA on the tourist web/app; provider app must be usable on
  low-end Android (we should set a target floor — e.g. Android 9, 2GB RAM).
- **Data residency.** **[DECIDE]** Maldives has no strict data-residency law today, but
  TGST + financial records likely need to be retained for MIRA audits (7 years).

## 9. Payment & financial model

### 9.1 Rails

- **Tourist → Platform:** card processor (Visa/Mastercard) + Swipe (for Swipe-enabled
  payers). **[DECIDE: which card PSP]** — Stripe doesn't operate in Maldives directly;
  candidates are 2Checkout, Adyen via reseller, or local acquirers like BML/MIB.
- **Platform → Provider:** Swipe payouts (`POST /api/v1/payouts`) to the provider's
  linked BML account, weekly or on-demand.

### 9.2 Money flow

```
Tourist pays → Platform wallet (holds in escrow until fulfillment)
                  ↓
              Voucher scanned + cooling-off window (e.g. 24h)
                  ↓
              Release: provider gets net amount, platform retains commission + TGST
                  ↓
              Weekly batched payout via Swipe → provider's BML account
```

### 9.3 Commission

**[DECIDE]** Default proposal: 12% across categories, with 8% for high-frequency
low-AOV (laundry, food under ~MVR 200), and 15% for experiences/excursions.

### 9.4 TGST

- 16% TGST applies to all tourism goods/services consumed by tourists.
- Platform must itemize TGST on every receipt.
- Open question: **[DECIDE]** does the platform collect TGST and remit centrally, or
  does each provider remit their own? Centralized remittance is simpler for providers
  but requires the platform to register as a TGST collection agent with MIRA.

### 9.5 Refunds

- Three cancellation tiers per listing: Flexible / Moderate / Strict.
- Refunds processed back to original payment method.
- **Gap:** Swipe spec has no refund endpoint as of v1.2.0 — refunds to Swipe payers must
  be handled either by reversing from platform float or by an out-of-band BML process.
  This needs confirmation with BML before launch.

## 10. Financial Advisor for Tourists

A standout feature distinguishing this app from a generic booking marketplace: a built-in
financial guidance layer that helps tourists spend confidently. This directly attacks
problems #3 (MVR/USD confusion), #4 (card surcharges), #5 (mystery FX charges),
#8 (price opacity), #9 (TGST transparency), #11 (bill splitting), and #12 (tipping)
identified in §2.1.

### 10.1 Purpose

Tourists arrive without a mental model for what things should cost, which currency to
pay in, how to split bills, or how tax and tipping work locally. They consistently
overpay or under-budget. The Financial Advisor turns the app into a financial co-pilot
for the length of the trip — informational, not prescriptive, and never a replacement
for licensed advice.

### 10.2 Capabilities

| Capability | What it does |
|---|---|
| **USD ↔ MVR live conversion** | Every price shown in both currencies, with the source rate cited (e.g. Central Bank of Maldives reference rate, daily). |
| **Smart pay recommendation** | At checkout: "Pay in MVR to save 4% (your card adds a 3% FX fee + 1% surcharge)." |
| **Trip budget tracker** | Set a total trip budget; the app deducts platform bookings and accepts manual cash entries; forecasts overrun by departure date. |
| **Category price benchmarks** | "Laundry on Maafushi typically runs MVR 50–80 per kg — this listing is in range." Sourced from anonymized platform transaction data once enough exists. |
| **TGST itemization** | Every receipt clearly separates base price, TGST (16%), platform fee, and total — with an in-app explainer of what TGST is. |
| **Tipping guidance** | Per-service-type suggested ranges; flag whether a service charge is already included. |
| **Cash-out helper** | "You hold ~MVR 1,200 in cash and depart in 2 days — here are MVR-friendly spend options nearby." Reduces stranded local-currency cash. |
| **Bill split** | After a group booking, split the bill across multiple payers each on their own payment method. |
| **End-of-trip spend report** | Total spent, savings vs. benchmark, downloadable PDF/CSV — useful for business travelers' expense reports. |
| **Tax refund eligibility** | If/where Maldives offers a tourist tax refund on departure, surface eligible purchases. **[DECIDE: confirm existence and rules with MIRA / Customs.]** |

### 10.3 Surfaces (where it shows up)

- **Onboarding** — "Trip dates? Approximate budget? Preferred currency?" — sets up tracking.
- **Listing detail** — "Price feels typical / above market / below market" indicator.
- **Checkout** — smart-pay recommendation; FX rate and fee breakdown above the Pay button.
- **Receipt** — TGST, platform fee, and FX rate transparently broken out.
- **Home tab** — budget remaining, daily burn rate, runway-to-departure.
- **Trip summary screen** — end-of-trip report on the day of departure.
- **Settings** — currency preference, rate source, advisor on/off.
- **AI agent chat** (see §11) — the same advisor data is reachable conversationally
  ("Is MVR 80 fair for laundry?", "How much have I spent this trip?").

### 10.4 Non-goals

- **Not investment, tax, or legal advice.** All output carries a clear disclaimer and a
  link to MIRA / Customs for authoritative answers.
- **Not a wallet.** The budget tracker reflects spending; it does not hold or move funds
  outside the platform's escrow.
- **Not personalized financial profiling.** No credit-style scoring, no third-party data
  enrichment on the tourist beyond what they enter or transact.

### 10.5 MVP cut

**v1:**
- USD/MVR price toggle with cited source rate
- TGST itemization on every receipt
- Manual + automatic trip budget tracker
- End-of-trip spend summary

**v2:**
- Smart-pay recommendation at checkout (requires PSP fee data)
- Tipping guidance
- Cash-out helper

**v3:**
- Category price benchmarks (need transaction volume first)
- AI-assistant chat ("How much should a 30-min boat transfer cost?")
- Tax refund eligibility surfacing

### 10.6 Data & accuracy

- **FX rates** pulled daily from a transparent, citable source (Central Bank of Maldives
  reference rate preferred over commercial-bank rates).
- **Benchmarks** are anonymized aggregates only; minimum sample size (e.g. ≥20
  transactions per month for a category × island pair) before a benchmark is shown.
- **Stale data** — rates older than 24 hours are shown with a "last updated" timestamp
  and a refresh action.
- **Disclaimer** — every advisor surface carries a short "Informational only — confirm
  with [authority] for binding figures."

## 11. AI Agent — tourist concierge

A conversational AI agent that acts as a 24/7 concierge for tourists — answering
questions, recommending listings, helping book, troubleshooting issues, and translating.
The agent is the connective tissue across the rest of the app: it reaches into the
Financial Advisor (§10), the listing catalogue, the booking system, and the dispute
flow, and exposes them through a chat interface.

### 11.1 Why a chatbot, not just better menus

- Tourists ask questions in natural language ("Can I swim here in a bikini?",
  "Where can I get laundry done by 6 today?"), not in app navigation paths.
- The questions span categories — financial, cultural, logistical, booking,
  dispute — in a way a single menu cannot represent cleanly.
- Many tourists are first-time visitors with very little local context. A
  conversational interface lets them ask "obvious" questions without
  embarrassment or wading through documentation.
- The agent also captures intent we would otherwise miss — what tourists *try* to
  ask for that we don't currently offer is itself a roadmap signal.

### 11.2 Capabilities

| Capability | Example query |
|---|---|
| **Catalogue search** | "Find me a vegetarian dinner under MVR 200 within walking distance." |
| **Personalized recommendation** | "I have 4 hours this afternoon and I've never snorkeled — what should I do?" |
| **Financial questions** | "Is MVR 80 a fair price for laundry here?" — pulls from §10 benchmarks. |
| **Cultural & etiquette** | "Can I wear a bikini on this beach?", "What should I wear visiting the mosque?", "Are restaurants open during Ramadan daytime?" |
| **Logistical info (read-only)** | "Where's the nearest pharmacy?", "What time is the last ferry?" (purely informational; transfers and transport are not bookable per §18.1.) |
| **Booking assistance** | "Book me a sunset cruise for two tomorrow at 5pm." → confirms slot, pricing, cancellation policy, then triggers checkout. |
| **Post-booking support** | "My snorkel trip got cancelled, what now?" |
| **Dispute filing** | "The laundry came back damaged — what do I do?" → walks them through evidence upload + opens a dispute. |
| **Translation** | Real-time English ↔ Dhivehi for chats with providers, menus, signage. |
| **Emergency triage** | "I cut my foot on coral, what should I do?" → first-aid info, nearest clinic, with a prominent "call emergency services" CTA when warranted. |

### 11.3 Surfaces

- **Floating chat button** persistent across the app.
- **Dedicated "Ask" tab** in the bottom navigation.
- **Voice input** for hands-free use on the beach / on a boat (v3).
- **Proactive nudges** via push: "Heavy rain forecast tomorrow — reschedule your boat trip?", "Your laundry is ready for pickup." (v2)
- **In-receipt help** ("Was something wrong with this?") to lower the floor for dispute initiation.

### 11.4 Non-goals

- **Not the only booking interface.** Visual browse + tap remains the primary path; the agent is an alternative, not a replacement.
- **Not human support.** Escalates to a human concierge for emergencies, disputes above a value threshold, or repeated low-confidence answers.
- **Not authoritative on medical, legal, or financial matters.** Every such answer carries a disclaimer and a link to a real authority (MIRA, Ministry of Health, embassy).
- **Not a provider tool in v1.** A provider-side AI assistant (listing copywriting, response drafting) is a v4 effort.
- **Not for prohibited topics** (alcohol, drugs, adult content — per §18.1). The agent refuses politely and explains why.

### 11.5 Architecture (proposal — **[DECIDE]**)

- **Model:** Claude (Haiku as default for cheap routing, Sonnet for most queries, Opus for complex multi-step reasoning). **[DECIDE]** — alternatives include GPT-4-class via OpenAI, or self-hosted open-weight for cost / data-residency reasons.
- **Retrieval-augmented:** real-time platform data (listings, prices, availability, opening hours, weather) injected into context per query. Static knowledge base for cultural / legal / etiquette content.
- **Tool use:** a defined set of tools the agent can invoke — `search_listings`, `check_availability`, `start_booking`, `get_benchmark_price`, `file_dispute`, `translate`, `escalate_to_human`.
- **Memory:** per-trip context (island, dates, budget, prior conversations, completed bookings). Auto-purged 30 days after trip end.
- **Streaming:** responses stream token-by-token to mask latency on poor connections.
- **Caching:** aggressive prompt caching of system prompts + listing catalogue to control cost.
- **Guardrails:** refusals for prohibited categories; explicit "I don't know — here's how to find out" for low-confidence queries; never invents prices, hours, or contact details.

### 11.6 MVP cut

**v1:**
- Text chat in English only.
- Read-only Q&A: catalogue search, financial questions, cultural/etiquette info, logistical lookups.
- Tools: `search_listings`, `get_benchmark_price` only.
- "Talk to a human" CTA always visible.

**v2:**
- Booking assistance (tool: `start_booking`).
- Post-booking support and dispute filing (tools: `file_dispute`, `escalate_to_human`).
- Translation (English ↔ Dhivehi inside provider chat).
- Proactive nudges.

**v3:**
- Voice input.
- Dhivehi as a *conversation language* (not just translation).
- Additional languages — Chinese, Russian, German — informed by arrival data.

**v4:**
- Provider-side assistant: listing copywriting, message drafting, dispute response prep.

### 11.7 Safety, privacy, and accuracy

- **Conversation log retention:** encrypted at rest, retained 30 days post-trip, then purged unless attached to an open dispute.
- **PII redaction** before any conversation is used for model fine-tuning or evaluation.
- **No medical / legal / financial *advice*** — only *information* with disclaimers and links to authorities.
- **Hallucination guardrails:** the agent must not invent listings, prices, opening hours, or contact details. If retrieval returns nothing, it says so.
- **AI disclosure:** every conversation surface clearly labelled "AI assistant — confirm important details with a person or the official source."
- **Refusal categories:** prohibited Maldives categories (§18.1), instructions to evade Maldivian law, anything that could endanger the tourist.
- **Audit log:** every booking or dispute action taken by the agent is logged and reviewable by ops.

### 11.8 Cost

- LLM cost per conversation depends on context size and model choice.
- **[DECIDE based on traffic projections]** — target ≤ ~$0.05 per active tourist per day at Sonnet pricing with prompt caching and a ~2k-token retrieval window. Per-user daily budget cap to prevent abuse.
- Haiku-as-default for simple queries; escalate to Sonnet / Opus only when reasoning depth is required.
- Static knowledge base + per-island catalogue aggressively prompt-cached.

## 12. Trust & safety

- **Provider verification.** NID + business reg + selfie liveness check. Tiered
  trust badge: Verified / Verified+ (with on-site visit).
- **Listing quality.** Photo moderation, no-stock-photo rule, price floor/ceiling checks.
- **Reviews.** Double-blind: both sides review, neither sees the other until both submit
  or 14 days pass.
- **Disputes.** In-app evidence upload (photos, chat transcripts). Admin adjudicates;
  funds in escrow are the lever.
- **Insurance.** **[DECIDE]** for water-based experiences (diving, fishing, boat trips),
  do we require provider-side insurance evidence at onboarding? Liability exposure is
  real if we don't.

## 13. Compliance & regulatory

- **MIRA** — Maldives Inland Revenue Authority. TGST registration, monthly returns.
- **Ministry of Tourism** — tourism operators may need licensing; investigate whether the
  platform itself needs a tourism-services license.
- **Ministry of Economic Development** — business registration.
- **Data protection** — Maldives passed the Personal Data Protection Act in recent years;
  confirm current status and obligations. Must confirm to GDPR as many European tourists will use it. **[DECIDE]**
- **AML/KYC** — provider onboarding KYC; tourist KYC likely not required for low-value
  transactions, but card-processor rules may impose limits.

## 14. Tech stack (initial proposal — open to change)

- **Mobile:** React Native (one codebase, iOS + Android, hot fixes) — or native if
  performance demands it. **[DECIDE]**
- **Backend:** [DECIDE — Go / Node / Python]. Go aligns with the Swipe SDK ergonomics
  but team familiarity matters more.
- **DB:** Postgres + Redis.
- **Payments:** Swipe (BML) + a card PSP **[DECIDE]**.
- **Maps:** Mapbox or Google Maps (Maldives island-level granularity is patchy on both).
- **Hosting:** Cloud provider with low-latency to South Asia (AWS Mumbai or Singapore).
- **Observability:** OpenTelemetry → managed backend.

## 15. MVP scope (what ships in v1)

**In:**
- Tourist app (iOS + Android, English only)
- Provider app (Android first — provider side skews Android; Dhivehi + English)
- Categories: Eat, Wash, Buy (pickup), Do (curated only)
- Card + Swipe payment, escrow, weekly payouts
- Booking voucher with offline QR validation
- Reviews, in-app chat, basic disputes
- Financial Advisor v1: USD/MVR toggle, TGST itemization, budget tracker, end-of-trip
  spend summary (see §10.5)
- AI Agent v1: English text chat, read-only Q&A (catalogue search, financial, cultural,
  logistical), `search_listings` + `get_benchmark_price` tools, "talk to a human" CTA
  (see §11.6)
- Admin console: verification + dispute + payouts
- 1–2 pilot islands **[DECIDE: which]** — recommend Maafushi (highest guesthouse density)
  and possibly Thulusdhoo or Ukulhas

**Out of v1:**
- Wellness / spa
- Photography
- Delivery (souvenirs are pickup only)
- Multi-listing cart
- Provider-set custom cancellation rules (use platform tiers only)
- Loyalty / referrals
- Resort-side integration
- Multi-language beyond English + Dhivehi

**Permanently out of the product (not just v1) — see §18:**
- Accommodation / room booking
- Arrival transfers (airport → island, seaplane, domestic flight)
- Inter-island transport (dhoni, speedboat, taxi)

## 16. Success metrics (first 6 months post-launch)

| Metric | Target |
|---|---|
| Verified providers on pilot island | 50+ per island |
| Weekly active tourists | 200+ during high season |
| Bookings completed | 500+ in first 90 days |
| Repeat booking rate | 30% within a single trip |
| GMV | **[DECIDE: target]** |
| NPS — tourist | > 40 |
| NPS — provider | > 30 |
| Dispute rate | < 2% of bookings |
| Time-to-payout (booking → provider funds available) | ≤ 10 days |

## 17. Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| **No accommodation funnel** — without booking the room, we don't naturally know the tourist's arrival, dates, or island | **High** | Onboarding-quiz capture; partner with guesthouses as *referrers* (QR codes in rooms, affiliate commission on first booking); list the app on Booking.com / Airbnb guest-info packs |
| Provider supply cold-start | High | Hand-onboard the first 50 per island; offer free verification + photography |
| Card PSP refuses Maldives or charges extreme fees | Medium | Validate at least two PSPs before commitment; Swipe-only as fallback for domestic |
| TGST collection compliance | Medium | Engage tax counsel before launch |
| Refund flow on Swipe payments | Medium | Confirm BML's process; design platform-side float to absorb |
| Connectivity at point of service | High | Offline voucher validation built in from day 1 |
| Tourist trust without brand recognition | High | Partner with established guesthouses for QR-code in-room distribution; visible verification badges; in-app concierge for first-trip nerves |
| Regulatory shift on tourism brokering | Low–Medium | Government affairs from week 1 |
| **LLM hallucination on safety / legal / medical queries** | Medium | Strict refusal categories; retrieval-grounded answers only; visible AI-assistant disclosure; human escalation always one tap away (§11.7) |
| **LLM cost blowout** at scale | Medium | Per-tourist daily budget cap; Haiku-as-default with Sonnet/Opus escalation; aggressive prompt caching of static knowledge + catalogue (§11.8) |

## 18. Out of scope

### 18.1 Permanently out — not just v1

These are **deliberately excluded from the product**, not deferred. The user (and team)
have decided this app does not enter these spaces, in order to focus on the in-stay
services layer and avoid head-on competition with entrenched players.

- **Accommodation / room booking** (guesthouses, homestays, resort rooms, vacation
  rentals). Booking.com, Airbnb, and direct-booked guesthouse websites cover this
  surface; we will not compete on it.
- **Arrival transfers** — airport pickup, seaplane, domestic-flight + ferry combos
  from MLE to the destination island. Resort/guesthouse transfer desks and platforms
  like Atoll Transfer cover this.
- **Inter-island transport** — dhoni transfers, speedboats, taxis between islands.
- **Long-term rentals** (different regulatory regime entirely).
- **Resort-internal experiences** (the resort already controls these).
- **B2B** procurement (e.g. supplying ingredients to restaurants — different product).
- **Influencer / affiliate marketplaces**.
- **White-label provider websites**.
- **Prohibited categories** (Maldives-specific legal/cultural): alcohol, nightlife,
  pork, recreational drugs, tattoo / piercing, adult services, jet-ski rentals in
  protected zones, and any wildlife-interaction listing that violates conservation
  law (turtle riding, manta touching, unlicensed shark feeding).

### 18.2 Deferred to v2+ (not v1, but on the roadmap)

- Wellness / spa
- Photography (beach / honeymoon photographers, drone, videography)
- Souvenir *delivery* (v1 is pickup-only)
- Multi-listing cart
- Provider-set custom cancellation policies
- Loyalty / referral programs
- Multi-language beyond English + Dhivehi

## 19. Open decisions blocking detailed planning

1. **Pilot island(s)** — Maafushi alone or Maafushi + one other?
2. **Card PSP** — which acquirer/processor?
3. **TGST collection model** — central (platform remits) or per-provider?
4. **Provider KYC strictness** — informal-operator path or licensed-only?
5. **Commission rates** — flat or by category?
6. **Tech stack** — RN vs native; backend language; team skills?
7. **Insurance requirement** for water-based experiences?
8. **Brand / product name** — current repo is `midnightDevs`; the product needs its own name.
9. **Domestic-only or international-tourist focus from day 1?** — this changes the
   payment-rail priority (Swipe-first vs card-first).
10. **Tourist tax refund** — does Maldives offer a TGST refund on departure for
    non-resident tourists? If yes, what's the eligibility / process? Confirm with MIRA
    and Customs before scoping the Financial Advisor v3 work.
11. **FX rate source** — Central Bank of Maldives reference rate vs. commercial-bank
    rate vs. a market aggregator? Affects perceived fairness of the advisor.
12. **Tourist acquisition channel** — with accommodation/transfers/transport
    excluded, the app has no built-in "first touch." How do tourists discover us?
    Candidates: in-room QR codes via partnered guesthouses (affiliate model),
    jetty signage on pilot islands, Maldives Tourism Promotion Board partnership,
    Booking.com / Airbnb host-info-pack inclusion. Needs a deliberate go-to-market
    plan before launch.
13. **AI model choice** — Claude (Haiku / Sonnet / Opus mix) vs GPT-4-class vs
    self-hosted open-weight. Trade-off is cost, latency, data residency, and how
    well the model handles Dhivehi + Maldives-specific cultural context.
14. **AI knowledge-base ownership** — who curates the static cultural / legal /
    etiquette / first-aid knowledge base the agent answers from? Internal content
    team, an external partner (e.g. MTPB), or a hybrid?
15. **AI scope on transport/accommodation queries** — the agent can *answer*
    questions like "where's the ferry?" or "good guesthouses on this island?" even
    though we don't *book* those. Should it? If yes, do we link out to
    Booking.com / Atoll Transfer, or stay neutral?

---

*Next step: walk through the [DECIDE] items in order. Once 1, 4, 5, and 9 are settled,
we can start v1 epic-level planning.*
