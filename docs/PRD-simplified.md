# AfterArrival — Full PRD (v1.4)

**Maldives In-Stay Local Commerce Platform**

---

## 1. Overview

**AfterArrival** is a Maldives-focused in-stay **local commerce platform** that connects tourists already staying on inhabited islands with local businesses, services, and cultural experiences.

It enables tourists to **discover, purchase, and access local offerings instantly during their stay**, replacing fragmented, cash-based island commerce with a unified digital layer.

This is a **commerce platform for real-world island life**, not a booking system.

### What it enables

* Discover local services, food, and experiences
* Instantly purchase goods and services
* Reserve time-based experiences
* Pay digitally via **BML Swipe**
* Receive QR-based fulfillment
* Leave reviews and trust signals
* AI-powered discovery and assistance
* Trip budgeting and currency visibility

### Out of scope

* Accommodation booking
* Airport transfers
* Inter-island transport

---

## 2. Core Vision

AfterArrival builds the **digital commerce layer for Maldivian island economies**, making local services:

* Discoverable
* Trustable
* Instantly purchasable
* Digitally trackable

It replaces informal, cash-based transactions with structured digital commerce between tourists and local providers.

---

## 3. Problem Statement

### For tourists

* No unified discovery system for local services
* Heavy reliance on cash payments
* Lack of transparent pricing
* Fragmented access to cultural experiences
* Language barriers
* Low trust in unknown providers

### For providers

* No digital distribution channel
* Dependence on walk-ins and guesthouse referrals
* Cash-heavy operations
* No structured demand insights
* No direct access to tourist demand

---

## 4. Core Product Concept

AfterArrival is a **local commerce marketplace**, not a booking platform.

It supports three transaction types:

* **Instant purchases** → food, souvenirs, small services
* **Scheduled services** → laundry, pickups, appointments
* **Experiences** → tours, cultural activities, learning sessions

Every interaction follows a single commerce loop:

> Discover → Purchase → Pay → Fulfill → Confirm → Review

---

## 5. Target Users

### Tourists (Demand Side)

* Guesthouse tourists (primary)
* Solo travelers
* Explorers seeking cultural immersion
* Experience-driven travelers
* Liveaboard visitors
* Domestic tourists & expats

### Providers (Supply Side — MVP Focus)

Core provider groups:

* Local cafés & eateries
* Home-cooked food providers
* Laundry services
* Souvenir shops
* Cultural experience operators

### Experience examples

* Cooking classes
* Fishing trips
* Snorkeling tours
* Island cultural walks
* Local workshops
* Skill-based learning sessions

---

## 6. Product Principles

* Commerce-first (not booking-first)
* Instant transaction experience
* Local-first economic inclusion
* Trust built via verification + escrow + reviews
* Mobile-first, low-bandwidth design
* Cultural discovery as a core product layer
* Replace cash dependency with digital payments

---

## 7. Marketplace Structure

### Eat

* Cafés
* Home kitchens
* Local meals
* Drinks & snacks

### Wash

* Laundry services
* Tailoring

### Buy

* Souvenirs
* Handmade crafts

### Experience

* Cultural workshops
* Fishing trips
* Snorkeling
* Island tours
* Learning sessions

---

## 8. Core User Flows

### Tourist Flow (Commerce Loop)

1. Select island context
2. Browse marketplace
3. View listing (price, availability, reviews)
4. Purchase or reserve
5. Pay via **BML Swipe**
6. Receive QR confirmation
7. Redeem service/product
8. Leave review

---

### Provider Flow

1. Sign up + identity verification
2. Create listings
3. Set pricing and availability
4. Receive orders
5. Fulfill service/product
6. Scan QR or mark complete
7. Receive payout via Swipe settlement

---

## 9. Payments (CORE — BML SWIPE ONLY)

AfterArrival uses **BML Swipe as the exclusive payment and settlement rail**.

Swipe is the **financial backbone of the platform**, enabling real-time commerce across Maldivian islands.

GitHub:
[https://github.com/BML-Digital/swipe-merchants-dev](https://github.com/BML-Digital/swipe-merchants-dev)

---

### 9.1 Role of Swipe

Swipe enables:

* Instant payments within Maldives banking ecosystem
* Direct settlement into BML merchant accounts
* Reliable island-wide payout infrastructure
* Elimination of cash dependency
* Standardized commerce across all providers

Swipe is the **core infrastructure layer of AfterArrival**.

---

### 9.2 Payment Flow

1. Tourist initiates purchase
2. Payment processed via **BML Swipe**
3. Platform records payment as escrow state (logical only)
4. Provider fulfills service/product
5. QR confirmation triggers completion
6. Funds are released via Swipe settlement

---

### 9.3 Escrow Model

* Platform maintains a **logical escrow state**
* Funds are released after:

  * QR scan confirmation OR
  * Automatic timeout after fulfillment window

---

### 9.4 Refund Handling

* Refunds handled via Swipe-supported reversal flows where available
* If reversal not supported:

  * Platform performs reconciliation via BML settlement adjustments
* Tourist experience remains fully abstracted from backend complexity

---

### 9.5 Strategic Importance of Swipe

Swipe is what makes AfterArrival viable:

* Converts cash-based island commerce into digital flows
* Enables instant trust between strangers
* Standardizes payments across fragmented providers
* Removes dependency on external PSPs
* Provides real-time settlement infrastructure

---

## 10. Trust System

* Provider verification (NID + business proof)
* Reviews and ratings
* Escrow-based protection
* Dispute resolution system
* QR-based fulfillment confirmation

---

## 11. AI Concierge

A conversational assistant embedded in the platform.

### Capabilities

* Discover services and experiences
* Recommend food and activities
* Explain pricing and fairness
* Cultural guidance
* Budget-related questions
* Service discovery support

### Future expansion

* Direct purchase assistance
* Multilingual support (Dhivehi + tourist languages)
* Voice interface

---

## 12. Financial Layer

* USD ↔ MVR conversion
* Trip budget tracking
* Spend summaries
* Price transparency indicators

---

## 13. Provider System

### Core capabilities

* Listing creation (services, products, experiences)
* Order management
* Fulfillment via QR system
* Earnings tracking
* Swipe-based payouts

### Provider categories

* Cafés & food providers
* Home cooks
* Laundry services
* Souvenir shops
* Experience operators

---

## 14. Admin System

* Provider verification
* Listing moderation
* Dispute handling
* Payout monitoring
* Platform health monitoring

---

## 15. Tech Stack

### Frontend

* Laravel Blade + Livewire
* TailwindCSS
* PWA-enabled web application

### Backend

* Laravel (monolith architecture)

### Database

* PostgreSQL
* Redis (queues + caching)

### Infrastructure

* AWS (Singapore / Mumbai)
* Cloudflare CDN

### Payments

* **BML Swipe (exclusive rail)**

### AI Layer

* External LLM (Claude / GPT-class)
* Retrieval-Augmented Generation (RAG)
* Tool-based agent system

---

## 16. Core Entities

### Tourist

* Profile
* Trip context (island, dates)
* Currency preference
* Orders

### Provider

* Identity & verification
* Business profile
* Listings
* BML payout account

### Listing

* Type (product / service / experience)
* Price
* Availability
* Island scope
* Reviews

### Order (core commerce object)

Represents:

* Purchase / reservation / experience
* Payment status
* Fulfillment status
* QR confirmation state

### Payment

* Swipe transaction ID
* Escrow state
* Payout state

---

## 17. MVP Scope

### Included

* Marketplace (Eat / Wash / Buy / Experience)
* Listings system
* Order + payment system (Swipe-only)
* Provider onboarding
* QR-based fulfillment system
* Reviews
* Basic AI concierge
* Currency conversion + budgeting

### Excluded

* Accommodation booking
* Transport systems
* Delivery infrastructure
* Loyalty programs
* Advanced analytics systems

---

## 18. Non-Functional Requirements

* Mobile-first UX
* Low bandwidth optimization (3G island conditions)
* Offline-tolerant QR verification
* PWA installable
* Fast load times (<2s target)
* English-first tourist interface
* Dhivehi provider interface

---

## 19. Success Metrics

* 50+ active providers per pilot island
* 500+ completed transactions in 90 days
* 30% repeat usage within trip
* <2% dispute rate
* > 95% fulfillment success rate
* > 40% review participation

---

## 20. Risks

| Risk                             | Mitigation                       |
| -------------------------------- | -------------------------------- |
| Provider digitization resistance | Assisted onboarding              |
| Cash culture inertia             | Instant Swipe payouts            |
| Weak discovery funnel            | Guesthouse QR distribution       |
| Connectivity issues              | Offline QR verification          |
| Payment dependency risk          | Single strong rail (Swipe + BML) |
| Trust deficit                    | Escrow + verification + reviews  |

---

## 21. Key Product Insight

AfterArrival is:

> A **Swipe-powered local commerce operating system for Maldivian islands**, enabling instant discovery, purchase, and fulfillment of real-world services.
