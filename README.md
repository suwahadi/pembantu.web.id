# Pembantu.web.id - Marketplace Multi-Jasa Tenaga Kerja

## 📋 Deskripsi Proyek

Pembantu.web.id adalah platform marketplace untuk menghubungkan calon pengguna (visitor) dengan tenaga kerja profesional (worker) melalui agency partner. Platform ini mengimplementasikan escrow system yang aman untuk transaksi, dengan payment gateway Midtrans dan dispute resolution yang transparan.

## 🎯 Fitur Utama

- ✅ **Multi-Role System**: Admin, Agency, Visitor
- ✅ **Worker Catalog**: Search, filter by category, location, rating
- ✅ **Contract & Order System**: Digital contracts dengan signed timestamps
- ✅ **Midtrans Payment Integration**: Core API dengan callback handling
- ✅ **Escrow System**: Hold dana sampai order selesai
- ✅ **Dispute Resolution**: Admin adjudicate dengan refund/release decisions
- ✅ **Manual Refund & Payout**: Admin transfer bank dengan proof uploads
- ✅ **Audit Trail**: Complete history of all actions
- ✅ **Order Timeline**: Event log untuk transparency

## 🏗️ Tech Stack

- **Backend**: Laravel 12 + Livewire + PHP 8.3
- **Database**: MySQL 5.7+
- **Payment**: Midtrans Core API
- **Frontend**: Livewire components (Blade)
- **Language**: Bahasa Indonesia (UI & system labels)
- **Currency**: IDR (Rupiah, no decimals)

## 📁 Project Structure

```
app/
├── Domain/                    # Business logic (Service layer)
│   ├── Shared/              # Constants, Value Objects
│   ├── Payment/             # Midtrans integration
│   ├── Order/               # Order lifecycle
│   ├── Escrow/              # Escrow management
│   ├── Dispute/             # Dispute resolution
│   ├── Refund/              # Refund processing
│   ├── Payout/              # Agency payouts
│   ├── Contract/            # Contract management
│   ├── Bank/                # Bank account management
│   ├── Worker/              # Worker catalog
│   ├── Ledger/              # Wallet accounting
│   ├── Event/               # Order events/timeline
│   └── Audit/               # Audit logging
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   ├── Middleware/
│   └── ...
├── Livewire/                # UI Components
│   ├── Visitor/
│   ├── Agency/
│   └── Admin/
├── Models/                  # Eloquent models
├── Policies/                # Authorization
└── Jobs/                    # Queued jobs

database/
├── migrations/              # 25 migrations
└── seeders/                 # Initial data

resources/
├── views/
│   ├── livewire/           # Component views
│   └── layouts/            # Layout templates
└── lang/id/                # Indonesian translations

config/
├── midtrans.php            # Payment config
└── ...
```

## 🔄 Order Flow

```
1. SEARCH & SELECT WORKER
   Visitor browse workers → filter by category/location/rating

2. CREATE CONTRACT
   Specify job scope, location, dates
   Visitor sign → Agency sign → Contract ready

3. CREATE ORDER
   Generate order from signed contract
   Status: pending_payment

4. PAYMENT (Midtrans)
   Visitor pay via Midtrans Core API
   On settlement:
   - Create escrow hold (status: hold)
   - Order status → paid_escrow
   - Ledger: customer → escrow_hold

5. WORK IN PROGRESS
   Order status → in_progress
   Work happens...

6. COMPLETE
   Order status → completed
   24h dispute window starts

7. DISPUTE WINDOW (NO DISPUTE)
   After 24h, if no dispute:
   - EscrowService::releaseHold()
   - Order status → payout_pending
   - Ledger: escrow_hold → agency_payable

8. PAYOUT (Admin manual transfer)
   Admin mark payout as paid
   - Order status → released
   - Ledger: agency_payable → cash_bank

OR if DISPUTE:

7B. OPEN DISPUTE (within window)
   Visitor open dispute with complaint
   Order status → disputed

8B. ADMIN RESOLVE
   Admin review evidence
   Decision:
   - Full refund: refundFull() → refund_pending → refunded
   - Full release: releaseHold() → payout_pending → released
   - Partial: refundPartial() → both ledger entries

9B. MANUAL REFUND
   Admin transfer refund to customer
   Mark paid with proof → order status: refunded
```

## 💾 Database Schema (25 Migrations)

**Authentication & Master**:
- users, roles, user_roles
- locations, service_categories, service_skills

**Worker Catalog**:
- agencies, workers, worker_documents, worker_skills
- worker_service_pricings, worker_service_areas

**Transactions**:
- orders, contracts, payments
- escrow_holds, wallet_ledgers

**Dispute & Payments**:
- disputes, dispute_evidences
- refunds, payouts, reviews

**Tracking**:
- audit_logs, order_events

**Infrastructure**:
- bank_accounts (polymorphic: users & agencies)

## 🔐 Key Principles

### Service-First Architecture
All business logic in `/app/Domain/*/Services/`. Controllers and Livewire are thin layers that validate input and call services.

### Transaction Safety
- DB::transaction() for all critical operations
- lockForUpdate() for row-level locking
- Prevents race conditions on payment, escrow, refund, payout

### Idempotency
Duplicate operations (e.g., duplicate Midtrans callback) are safe:
- entry_key (UNIQUE) in wallet_ledgers
- On duplicate: return existing entry (success)

### Single Source of Truth
- wallet_ledgers = only record of who-has-what
- Account balance = sum(credits) - sum(debits)
- No balance cache; always calculated

### Transparency
- Every action logged: AuditLog + OrderEvent
- Timeline shows status changes + timestamps
- Admin can see who did what and when

## 🚀 Getting Started

See [IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md) for:
- Setup instructions
- Architecture explanation
- Flow diagrams
- Development guidelines
- Testing procedures

## 📊 API Endpoints (TODO)

- `POST /api/workers/search` - Search workers
- `POST /api/contracts` - Create contract
- `POST /api/orders` - Create order
- `POST /api/payment/initiate` - Start payment
- `POST /api/callback/midtrans` - Midtrans webhook
- `POST /api/disputes/{id}/resolve` - Admin resolve dispute
- `POST /api/refunds/{id}/paid` - Mark refund paid
- `POST /api/payouts/{id}/paid` - Mark payout paid

## 📱 UI Components (TODO)

**Visitor**:
- WorkerSearch (with filters & pagination)
- CheckoutWizard (multi-step: contract → payment)
- OrderDetail (status, events, dispute option)
- DisputeForm (complaint + evidence upload)

**Agency**:
- WorkerManage (CRUD workers)
- PricingManage (pricing tiers)
- OrdersBoard (incoming jobs)
- PayoutHistory (past payouts)

**Admin**:
- AgencyVerification (approve/reject agencies)
- WorkerVerification (approve/reject workers)
- DisputeQueue (open disputes, resolve)
- RefundQueue (pending refunds, mark paid)
- PayoutQueue (pending payouts, mark paid)
- Analytics Dashboard (revenue, stats)

## 🌐 Language & Localization

- **UI**: Bahasa Indonesia
- **Date Format**: "Senin, 17 Feb 2026" (Indonesia locale)
- **Currency**: "Rp 1.000.000" (IDR, no decimals)
- **Status Labels**: Indonesian translations in Status classes
- **Messages**: resources/lang/id/messages.php

## 📝 Configuration

### .env Variables
```
MIDTRANS_SERVER_KEY=xxxxx
MIDTRANS_CLIENT_KEY=xxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_CALLBACK_URL=https://your-domain/callback/midtrans
```

### config/midtrans.php
All Midtrans settings centralized

## ⚙️ Maintenance & Monitoring

### Critical Queues
- ReleaseEscrowJob (automatic after 24h)
- SendNotificationJob (email/SMS on status changes)
- PaymentSettlementJob (handle settlement retries)

### Monitoring Points
- Payment callback handling (is it idempotent?)
- Ledger balance consistency
- Refund/Payout queues (manual transfers)
- Dispute resolution SLA (target 48h)

## 🔍 Testing

See test directory for:
- Unit tests: Service layer logic
- Feature tests: Full order flow
- Integration tests: Midtrans callbacks

## 📖 Documentation Files

- [pembantu_web_id_brief.md](./pembantu_web_id_brief.md) - Complete technical brief
- [IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md) - Development guide
- **README.md** - This file

## Contributors

- Built with service-first architecture
- Designed for scalability & safety
- References from brief specification

## 📞 Support

For issues, refer to:
1. Brief specification
2. Implementation guide
3. Code comments in services
4. Test cases for examples

---

**Status**: ✅ Infrastructure complete, ready for UI/API implementation
**Last Updated**: Feb 17, 2026
# pembantu.web.id
