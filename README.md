# AfterArrival

> 🏆 **2nd Place — Co.Lab 26 Hackathon · Track 2: Fintech with BML Payment APIs**
> *Maldives AI Lab × BML × MINDCo · Hotel Jen, Malé · May 20–21, 2026*

---

> **Maldives, after you arrive.**
> An AI-powered marketplace that connects tourists on Maldivian islands with verified local providers for food, laundry, souvenirs, and experiences — paid digitally via **BML Swipe**.

[![2nd Place — Co.Lab 26](https://img.shields.io/badge/Co.Lab%2026-2nd%20Place%20%F0%9F%A5%88-E8A84A)](https://colab.mv)
[![Track 2 — Fintech with BML Payment APIs](https://img.shields.io/badge/Track%202-Fintech%20with%20BML-5999CF)](https://colab.mv)
[![License: MIT](https://img.shields.io/badge/License-MIT-54B289)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20)](https://laravel.com)
[![Powered by Claude](https://img.shields.io/badge/Powered%20by-Claude%20Sonnet%204.6-543AAD)](https://anthropic.com)

---

## The team

**AfterCode** — Co.Lab 26 Hackathon (Maldives AI Lab / BML / MINDCo)

- **Moosa Shifaz** — [@moosashifaz](https://github.com/moosashifaz)
- **Mohamed Adhuham** — [@adhuham](https://github.com/adhuham)

**Track:** Track 2 — Fintech with BML Payment APIs

---

## The problem

Maldives is a USD 4B tourism economy. Resorts and OTAs (Booking.com, Airbnb) have the accommodation side. Atoll Transfer and resort transfer desks have the arrival logistics. **But everything tourists spend on *during* their stay on inhabited Maldivian islands — meals, laundry, cooking classes, snorkel trips, souvenirs — runs on cash, paper signs at the jetty, and word-of-mouth.**

Tourists overpay because they can't read Dhivehi price boards. They hand cash to strangers without recourse. Local providers — guesthouse cafés, traditional artisans, dive operators, home cooks — are invisible to the very tourists walking past their door.

**AfterArrival** is the digital commerce layer for that gap.

## What it does

| For tourists | For local providers |
|---|---|
| Discover verified local services on your island | Free digital storefront with photos, prices, calendar |
| AI concierge that knows real listings, real prices, real local culture | Instant SMS + in-app booking notifications |
| AI trip planner that builds multi-day itineraries within your budget | Earnings dashboard with TGST itemized |
| Dual-currency pricing (MVR + USD) with TGST shown separately | Weekly Swipe payouts to BML account |
| One-tap BML Swipe payments with escrow until QR voucher is scanned | Voucher scanner for service fulfillment |

## Live demo

The application is a mobile-first PWA. **Try it locally** by following the install steps below — judges can sign in with:

| Role | Login |
|---|---|
| Tourist | `tourist@afterarrival.test` |
| Provider | `provider1@afterarrival.test` |
| Admin | `admin@afterarrival.test` |

Authentication is **email OTP** (no password). When `MAIL_MAILER=log`, the OTP code is written to `storage/logs/laravel.log` — grep for `OTP` in the latest log lines to retrieve it.

The seeded island is **Maafushi** with 17 listings across the four MVP categories.

## Install & run

### Requirements

- PHP **8.3+** (`brew install php` on macOS)
- Composer **2.x**
- Node.js **20+** and npm
- (Optional) The BML Swipe CLI binary for local payment mocking — `brew tap BML-Digital/tap && brew install swipe`

### Setup

```sh
# 1. Clone the repo
git clone https://github.com/moosashifaz/midnightDevs.git
cd midnightDevs/app

# 2. Install dependencies
composer install
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Fill in .env (see "Environment variables" below)
#    Required at minimum: ANTHROPIC_API_KEY

# 5. Database (SQLite is the default; no setup required beyond the migrate)
php artisan migrate --seed

# 6. Build assets
npm run build

# 7. Run the server
php artisan serve
# → http://127.0.0.1:8000
```

### Environment variables

The full template is in [`app/.env.example`](app/.env.example). The variables you'll need:

| Variable | Purpose | Required? |
|---|---|---|
| `ANTHROPIC_API_KEY` | Anthropic Messages API key for the AI concierge + planner | **Yes** |
| `ANTHROPIC_MODEL` | Defaults to `claude-sonnet-4-6` | No |
| `ANTHROPIC_MOCK` | `true` falls back to canned responses for offline demos | No |
| `SWIPE_BASE_URL` | BML Swipe API base URL — use organizer-issued sandbox endpoint | Yes (Track 2) |
| `SWIPE_CLIENT_ID` / `SWIPE_CLIENT_SECRET` | OAuth2 client credentials issued by BML | Yes (Track 2) |
| `SWIPE_MOCK` | `true` uses deterministic responses without hitting the network | No |
| `SWIPE_BIN` | Path to the local Swipe CLI binary (e.g. `/opt/homebrew/bin/swipe`) | Only if using CLI mock |
| `HOME` | Home directory the Swipe CLI uses for `~/.swipe/` state | Only if using CLI mock |
| `PHP_CLI_SERVER_WORKERS` | Set to **8** when using `php artisan serve` — single worker blocks during AI calls | Recommended |

### Running with the BML Swipe CLI mock (local payment loop)

```sh
# In a separate terminal
swipe mock start --port 8080 --webhook-url http://127.0.0.1:8000/webhooks/swipe

# Configure Laravel to talk to the local mock
# (in .env)
SWIPE_BASE_URL=http://127.0.0.1:8080
SWIPE_MOCK=false
```

This gives you the full **charge → escrow → voucher scan → payout** loop without needing public webhook URLs or live sandbox credentials.

## Services and APIs used

| Service | What it powers | Docs |
|---|---|---|
| **BML Swipe Merchants API** | Payment charges, payouts, escrow accounting, webhook events. Track 2 mandatory rail. | [github.com/BML-Digital/swipe-merchants-dev](https://github.com/BML-Digital/swipe-merchants-dev) |
| **Anthropic Messages API** | AI Concierge (`claude-sonnet-4-6`) — grounded chat over real listings. AI Planner — structured-JSON multi-day itineraries with slug-allowlist hydration. | [docs.anthropic.com](https://docs.anthropic.com) |
| **Central Bank of Maldives reference rate** *(planned)* | USD ↔ MVR conversion source for the Financial Advisor budget tracker. | – |

## AI tools used

Per Rule 02 (Tools) — all AI assistance is disclosed:

**Build-time (development):**
- **Claude Code** (Anthropic CLI, Sonnet 4.6 / Opus 4.7 1M context) — scaffolding, refactoring, code review, documentation
- **Cursor** — in-editor AI completions
- **Higgsfield AI** — cinematic intro video generation

**Runtime (in the product):**
- **Anthropic Messages API** (`claude-sonnet-4-6`) — powers both the AI Concierge (`app/Services/AIAgentService.php`) and the AI Planner (`app/Services/AIPlannerService.php`). System prompts inject real listings by slug, so the agent cannot hallucinate inventory.

Every team member can explain any part of the codebase (Rule 02 compliance).

## Open-source components and licenses

| Component | Version | License | Used for |
|---|---|---|---|
| [Laravel Framework](https://laravel.com) | ^13.8 | MIT | Backend monolith |
| [Laravel Breeze](https://github.com/laravel/breeze) | ^2.4 | MIT | Auth scaffolding (extended for OTP) |
| [Livewire](https://livewire.laravel.com) | ^3.x | MIT | Server-driven reactivity (chat, planner, OTP) |
| [Livewire Volt](https://livewire.laravel.com/docs/volt) | ^1.x | MIT | Single-file components |
| [Tailwind CSS](https://tailwindcss.com) | ^3.1 | MIT | Utility-first styling |
| [@tailwindcss/forms](https://github.com/tailwindlabs/tailwindcss-forms) | ^0.5 | MIT | Form element resets |
| [@tailwindcss/typography](https://github.com/tailwindlabs/tailwindcss-typography) | ^0.5 | MIT | Markdown rendering in chat bubbles |
| [Alpine.js](https://alpinejs.dev) | (via Livewire) | MIT | Tiny client-side interactivity |
| [Vite](https://vite.dev) | ^8.0 | MIT | Asset bundler |
| [Pest](https://pestphp.com) | ^4.x | MIT | Test framework |
| [league/commonmark](https://commonmark.thephpleague.com) | ^2.8 | BSD-3-Clause | Markdown parsing for chat |
| [Lottie Web](https://airbnb.io/lottie) | (latest) | Apache-2.0 | Planner generating animation |
| [Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans) | – | OFL-1.1 | Typography |
| [Lucide icons](https://lucide.dev) | (paths inlined) | ISC | SVG icon set |

## Repository layout

```
midnightDevs/
├── app/                       # Laravel application
│   ├── app/                   # Eloquent models, Livewire components, services
│   │   ├── Http/Controllers/  # MarketplaceController, OrderController, ProviderDashboardController
│   │   ├── Livewire/          # AIChat, PlanBuilder, OtpAuth
│   │   ├── Models/            # Island, Listing, Order, Payment, Provider, Review, OtpCode
│   │   └── Services/          # AIAgentService, AIPlannerService, SwipeService, SwipeCliService, OtpService, SamplePlanService
│   ├── database/              # Migrations + seeders (Maafushi pilot data)
│   ├── resources/views/       # Blade + Livewire views, Dhivehi-themed design system
│   ├── public/                # PWA manifest, service worker, hero video + images
│   ├── tests/                 # Pest feature tests
│   └── routes/                # web.php (marketplace + planner + OTP)
├── README.md                  # This file
└── LICENSE                    # MIT
```

## Architecture in one paragraph

A Laravel 13 monolith + SQLite (or Postgres in production) backs three roles: tourists, providers, and admins, distinguished by a `role` column on `users`. The marketplace surface is server-rendered Blade + Livewire components with Tailwind. The **AI Concierge** is a Livewire chat component that calls the Anthropic Messages API with a dynamic system prompt built from the signed-in tourist's trip context + a fresh snapshot of all active listings on their current island; Claude can only recommend by slug, and recommendations are hydrated against the listing table so hallucinations are structurally prevented. The **AI Planner** uses the same pattern but enforces a strict JSON schema (`days[].items[].slug`) and validates each item against budget + listing availability before render. The **BML Swipe integration** wraps `POST /api/v1/payments` and `POST /api/v1/payouts` with an internal escrow ledger; status flips are driven by webhook delivery from Swipe (or the local Swipe CLI mock for offline demos).

> *Product strategy, PRD, and detailed architecture diagrams are maintained in a private team repo for ongoing product work — this public repo carries the implementation that won 2nd at Co.Lab 26.*

## Compliance notes

- **Rule 5.3 — Newly written:** the entire `app/` directory was written during the hackathon period (May 20–21, 2026). Commit history is publicly visible from May 20 onward. Pre-existing knowledge of Laravel idioms and open-source libraries (listed above) is not pre-existing code.
- **Rule 03 — No secrets:** `app/.env` is gitignored; only `app/.env.example` with placeholder values is committed. Confirmed via `git ls-files | grep -E '\\.env\\b'`.
- **Rule 05 — Track 2 Sandbox:** BML Swipe integration uses organizer-issued sandbox credentials only. No production credentials, no real customer data, no real money. Local development uses the [`swipe mock start`](https://github.com/BML-Digital/swipe-merchants-dev) CLI binary provided by BML for offline development.

## Acknowledgments

- **Bank of Maldives** for the Swipe Merchants API and the [open-source CLI](https://github.com/BML-Digital/swipe-merchants-dev) that makes local development possible.
- **Maldives AI Lab** (NADCC) **/ MINDCo** for organizing Co.Lab 26.
- The Maldives' local-island operators, café owners, dhoni captains, and artisans whose work this platform tries to make discoverable.

---

*Built in 48 hours · May 20–21, 2026 · Hotel Jen, Malé*
