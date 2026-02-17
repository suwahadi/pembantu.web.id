# IMPLEMENTATION GUIDE - Pembantu.web.id (Step 7-14 Complete)

**Status Project:** ✅ Step 7-14 Complete (Database + Service Layer + Payment + Dispute Resolution)  
**Last Updated:** February 17, 2026  
**Laravel Version:** 12.51.0 | **PHP:** 8.3.30 | **MySQL:** 8.0.30

---

## 📋 Daftar Isi

1. [Arsitektur Umum](#arsitektur-umum)
2. [Database Schema (31 Migrations)](#database-schema-31-migrations)
3. [Service Layer Architecture](#service-layer-architecture)
4. [Order Workflow Lengkap](#order-workflow-lengkap)
5. [Ledger Accounting System](#ledger-accounting-system)
6. [Midtrans Payment Integration](#midtrans-payment-integration)
7. [Dispute Resolution Flow](#dispute-resolution-flow)
8. [Auto-Release Scheduler](#auto-release-scheduler)
9. [Folder Structure & Models](#folder-structure--models)
10. [Setup & Installation](#setup--installation)
11. [Key Principles & Best Practices](#key-principles--best-practices)
12. [Admin Operations](#admin-operations)

---

## Arsitektur Umum

Proyek menggunakan **Service-First Architecture** dengan Domain-Driven Design:

```
HTTP Request (Controller/Livewire)
    ↓ (thin layer - validation only)
Service Layer (semua business logic)
    ├─ PaymentService
    ├─ LedgerService
    ├─ EscrowService
    ├─ OrderService
    ├─ DisputeService
    ├─ RefundService
    ├─ PayoutService
    ├─ ContractService
    ├─ OrderEventService
    ├─ AuditLogService
    └─ BankAccountService (11 services total)
    ↓
Database
    ├─ 25 Tables (31 migrations total)
    ├─ Eloquent Models
    ├─ Foreign Keys + Constraints
    └─ Wallet Ledger (single source of truth untuk uang)
```

**Prinsip Kunci:**
- ✅ Transaction + Row Locking (prevent race condition)
- ✅ Idempotency via unique entry_key (safe retry)
- ✅ Double-entry accounting (money always balanced)
- ✅ Complete audit trail (setiap action tercatat)

---

## Database Schema (31 Migrations)

### Batch 1: Original Schema (25 Migrations)
**Status:** ✅ Applied

| # | Migration | Tujuan | Key Fields |
|---|---|---|---|
| 1-7 | Auth & Master | Users, roles, locations, categories, agencies | email (UNIQUE), phone (UNIQUE), type codes |
| 8-12 | Worker Catalog | Workers, documents, skills, pricing, areas | worker_id FK, unique indexes |
| 13-24 | Orders & Transactions | Complete flow | order_id (UNIQUE), status, amounts |
| 25 | Audit & Timeline | complete transaction tracking | entry tracking |

**Total Tables:** 25

### Batch 2: Step 7 Enhancements (6 Migrations)
**Status:** ✅ Applied

| # | Migration | Enhancement | New Fields |
|---|---|---|---|
| 26 | enhance_users_table | User status tracking | `status` (active/inactive/suspended) - indexed |
| 27 | add_code_to_service_categories_table | System identifiers | `code` (UNIQUE: e.g., ART_PRT, BABYSITTER) |
| 28 | enhance_agencies_table | Geographic + cache | `location_id` FK, `rating_avg`, `orders_completed_count` |
| 29 | enhance_workers_table | Profile complete | `gender`, `birth_date`, `photo_path`, `availability_status` |
| 30 | enhance_contracts_table | Contract details | `work_days`, `work_hours`, `location_id` FK, `scope_of_work`, financial fields |
| 31 | add_currency_to_orders_table | Multi-currency prep | `currency` (char 3, default IDR) |

**Total Migrations:** 31 ✅ (25 + 6 enhancements)

### Key Design Patterns

**Wallet Ledgers Table:**
```sql
CREATE TABLE wallet_ledgers (
    id BIGINT PRIMARY KEY,
    entry_key VARCHAR(255) UNIQUE NOT NULL,  -- CRITICAL: prevents duplicate posts
    debit_account VARCHAR(100),
    credit_account VARCHAR(100),
    amount_idr BIGINT,
    ref_type VARCHAR(50),
    ref_id BIGINT,
    note TEXT,
    created_at TIMESTAMP
);

-- Idempotency: INSERT dengan entry_key duplicate → graceful fail
-- Digunakan untuk setiap money movement
```

---

## Service Layer Architecture

### 1. LedgerService - Accounting & Idempotency

**File:** `app/Domain/Ledger/Services/LedgerService.php`

```php
Method: record(
    string $entryKey,        // UNIQUE - prevents duplicates
    string $debitAccount,
    string $creditAccount,
    int $amountIdr,
    string $refType,
    int $refId,
    ?string $note = null
): void

// Idempotency:
// 1st call dengan entry_key = INSERT berhasil
// 2nd call dengan entry_key sama = Duplicate constraint → catch & return
// Result: IDEMPOTENT! Safe untuk retry logic

Entry Key Format:
  ORDER:123:ESCROW_HOLD
  ORDER:123:AUTO_RELEASE
  REFUND:456:PAID
  PAYOUT:789:PAID
  ORDER:123:REFUND_DECISION:dispute_999
```

**Account Types:**
```
Assets:
  - cash_bank (uang kita di bank)
  - escrow_hold (dana menunggu)

Liabilities:
  - customer_X_refundable (hutang untuk refund)
  - agency_Y_payable (hutang untuk payout)
```

---

### 2. PaymentService - Midtrans Integration

**File:** `app/Domain/Payment/Services/PaymentService.php`

```php
Methods:
  - initiatePayment() → Create Payment (status: initiated)
  - handlePaymentSettlement() → Process settlement
  - handlePaymentFailure() → Handle expire/cancel/deny
  - getPaymentStatus() → Query current status

Settlement Flow:
  1. Extract Midtrans callback payload
  2. VERIFY SIGNATURE (SHA512 - CRITICAL!)
  3. Map status code (100=settlement, others=pending/failed)
  4. Lock order + payment row (prevent race)
  5. Create EscrowHold
  6. Create LedgerEntry (idempotent via entry_key)
  7. Update order status → paid_escrow
  8. Record OrderEvent + AuditLog
```

---

### 3. EscrowService - Escrow Hold Management

**File:** `app/Domain/Escrow/Services/EscrowService.php`

```php
Methods:
  - createHold(orderId, amount) → Create hold setelah payment
  - releaseHold(orderId, reason) → Full release ke agency_payable
  - refundFull(orderId) → Full refund ke customer_refundable
  - applyResolution(orderId, refundAmount, releaseAmount) → Partial

Ledger Entries:
  HOLD: customer_X → escrow_hold (via Payment settlement)
  RELEASE: escrow_hold → agency_Y_payable
  REFUND: escrow_hold → customer_X_refundable
```

---

### 4. OrderService - Order Lifecycle

**File:** `app/Domain/Order/Services/OrderService.php`

```php
Methods:
  - createFromContract(contractId) → Create order dari contract signed
  - updateStatus(orderId, newStatus) → Change status dengan validation
  - markInProgress() → Guard: must be paid_escrow
  - markCompleted() → Guard: must be in_progress
  - cancel() → Cancel jika still pending_payment

Status Machine (VALIDATED):
  pending_payment
    ↓
  paid_escrow (after payment settlement)
    ↓
  in_progress (agency starts work)
    ↓
  completed (agency finishes)
    ├─→ disputed (within 24h window)
    └─→ payout_pending (auto-release after 24h)
```

---

### 5. DisputeService - Dispute Resolution

**File:** `app/Domain/Dispute/Services/DisputeService.php`

```php
Methods:
  - openDispute(orderId, reason, evidenceFiles[])
    → Create dispute (status: open)
    → Update order status → disputed
    
  - resolveWithFullRefund(disputeId, adminNotes)
    → EscrowService::refundFull()
    → RefundService::queueRefund()
    → Order status → refund_pending
    
  - resolveWithFullRelease(disputeId, adminNotes)
    → EscrowService::releaseHold()
    → PayoutService::queuePayout()
    → Order status → payout_pending
    
  - resolveWithPartial(disputeId, refundAmount, notes)
    → Apply both transitions (refund + release split)
```

---

### 6. RefundService - Manual Refund Processing

**File:** `app/Domain/Refund/Services/RefundService.php`

```php
Methods:
  - queueRefund(orderId, amount) → Create refund (status: queued)
  - markProcessing(refundId) → Change to processing
  - markPaid(refundId, proofPhoto, transferDate)
    → LedgerEntry: customer_X_refundable → cash_bank
    → Update refund status → paid
    → Update order status → refunded

Used by:
  - DisputeService::resolveWithFullRefund()
  - Admin manual refunds
```

---

### 7. PayoutService - Agency Payout Management

**File:** `app/Domain/Payout/Services/PayoutService.php`

```php
Methods:
  - queuePayout(orderId, amount, bankAccountId) → Create payout (status: queued)
  - markProcessing(payoutId)
  - markPaid(payoutId, proofPhoto, transferDate)
    → LedgerEntry: agency_Y_payable → cash_bank
    → Update payout status → paid
    → Update order status → released

Used by:
  - EscrowService::releaseHold() (auto or manual)
  - ReleaseEscrowJob (auto-release)
```

---

### 8. ReleaseEscrowJob - Auto-Release Scheduler

**File:** `app/Jobs/ReleaseEscrowJob.php`
**Scheduled:** Every 15 minutes

```php
Logic:
  1. Find all orders dengan:
     - status = 'completed'
     - updated_at > ORDER_DISPUTE_WINDOW_HOURS (default 24h)
     
  2. Untuk setiap order:
     ├─ Lock order row
     ├─ Check: no active dispute exists
     ├─ Lock escrow hold
     ├─ Call EscrowService::releaseHold()
     │   → LedgerEntry: escrow_hold → agency_Y_payable
     ├─ Call PayoutService::queuePayout()
     │   → Create payout (status: queued)
     ├─ Update order status → payout_pending
     └─ Record OrderEvent (ESCROW_AUTO_RELEASED)

Configuration (.env):
  ORDER_DISPUTE_WINDOW_HOURS=24
```

---

### 9. OrderEventService - Timeline Recording

**File:** `app/Domain/Event/Services/OrderEventService.php`

```php
Methods:
  - recordStatusChange(orderId, newStatus, payload)
  - record(orderId, type, message, payload)
  - getTimeline(orderId) → All events ordered by created_at

Events di-record:
  - ORDER_CREATED
  - PAYMENT_INITIATED
  - PAYMENT_SETTLED
  - PAYMENT_FAILED
  - ESCROW_HELD
  - WORK_STARTED
  - WORK_COMPLETED
  - DISPUTE_OPENED
  - DISPUTE_RESOLVED
  - REFUND_QUEUED
  - REFUND_PAID
  - PAYOUT_QUEUED
  - PAYOUT_PAID
  - ESCROW_AUTO_RELEASED

Complete timeline traceable!
```

---

### 10. AuditLogService - Audit Trail

**File:** `app/Domain/Audit/Services/AuditLogService.php`

```php
Methods:
  - record(action, actor, before, after, metadata)
  
Records setiap:
  - action: payment_settled, order_completed, dispute_resolved, refund_paid, payout_paid
  - actor_type: admin, agency, visitor, system
  - actor_user_id: siapa yang trigger
  - before/after data (JSON)
  - metadata (context-specific info)
```

---

## Order Workflow Lengkap

### Phase 1: Creation → Payment (2-5 menit)

```
Visitor pilih Worker → CheckoutWizard
    ↓
ContractService::create()
    ├─ Create Contract (status: draft)
    └─ Create Order (status: pending_payment)
    ↓
Contract Review & Signing
    ├─ ContractService::signByVisitor()
    ├─ ContractService::signByAgency()
    └─ Contract status → active, signed_date set
    ↓
Payment Initiation
    ├─ PaymentService::initiatePayment()
    └─ Create Payment (status: initiated)
         midtrans_order_id: PBW-20260217-XXXXX
         midtrans_snap_token: generated
    ↓
Midtrans Charge (Visitor redirect)
    └─ Midtrans Snap UI
```

### Phase 2: Settlement → Escrow (immediate saat callback)

```
Midtrans Webhook Callback
    ↓
MidtransNotificationController::handleNotification()
    ├─ Extract payload (order_id, status_code, gross_amount, signature_key)
    ├─ VERIFY SIGNATURE (CRITICAL!)
    │   serverKey = config('midtrans.server_key')
    │   expected = hash_sha512(orderId + statusCode + grossAmount + serverKey)
    │   compare dengan payload['signature_key']
    │   If invalid → return 401 (REJECT)
    ├─ Map status code (100 = settlement)
    ├─ DB::transaction → lock both order & payment
    └─ Call PaymentService::handlePaymentSettlement()
        ├─ Create EscrowHold (amount: order.total_idr, status: hold)
        ├─ LedgerService::record()
        │   entry_key: ORDER:123:ESCROW_HOLD
        │   debit: customer_X
        │   credit: escrow_hold
        │   amount_idr: 500000
        ├─ Update Order status → paid_escrow
        ├─ OrderEventService::record(PAYMENT_SETTLED)
        └─ AuditLogService::record()
    ↓
Order Ready for Work
```

### Phase 3: In Progress → Completion (2-30 hari)

```
Agency Starts Work
    ├─ OrderService::markInProgress()
    ├─ Update Order status → in_progress
    ├─ OrderEventService::record(WORK_STARTED)
    └─ AuditLogService::record()

...work happens...

Agency Completes Work
    ├─ OrderService::markCompleted()
    ├─ Update Order status → completed
    ├─ OrderEventService::record(WORK_COMPLETED)
    └─ AuditLogService::record()
```

### Phase 4: Dispute Window (24 hours after completed)

```
Case A: NO DISPUTE → Auto-Release
    ├─ ReleaseEscrowJob runs every 15min
    ├─ Check: completed > 24h + NO active dispute
    ├─ EscrowService::releaseHold()
    │   └─ LedgerEntry: escrow_hold → agency_Y_payable
    ├─ PayoutService::queuePayout()
    │   └─ Create payout (status: queued)
    ├─ Update Order status → payout_pending
    └─ OrderEventService::record(ESCROW_AUTO_RELEASED)

Case B: YES DISPUTE → Admin Resolve
    ├─ DisputeService::openDispute()
    │   └─ Create dispute (status: open)
    ├─ Update Order status → disputed
    ├─ Admin review evidence (via admin panel)
    ├─ Admin make decision:
    │   ├─ OPTION 1: Full Refund
    │   │   ├─ EscrowService::refundFull()
    │   │   │   └─ LedgerEntry: escrow_hold → customer_X_refundable
    │   │   ├─ RefundService::queueRefund()
    │   │   └─ Order status → refund_pending
    │   │
    │   ├─ OPTION 2: Full Release
    │   │   ├─ EscrowService::releaseHold()
    │   │   │   └─ LedgerEntry: escrow_hold → agency_Y_payable
    │   │   ├─ PayoutService::queuePayout()
    │   │   └─ Order status → payout_pending
    │   │
    │   └─ OPTION 3: Partial (e.g., 50% refund, 50% payout)
    │       ├─ EscrowService::applyResolution(refundAmount, releaseAmount)
    │       ├─ Queue BOTH refund + payout
    │       └─ Order status → partially_refunded
    └─ OrderEventService::record(DISPUTE_RESOLVED)
```

### Phase 5: Manual Transfers (admin action)

```
Refund Queue (Admin Panel):
    ├─ Admin view pending refunds
    ├─ Click "Mark Processing"
    │   └─ RefundService::markProcessing()
    ├─ Admin do bank transfer manually (externally)
    ├─ Admin click "Mark Paid"
    │   └─ RefundService::markPaid()
    │       ├─ LedgerEntry: customer_X_refundable → cash_bank
    │       ├─ Update refund status → paid
    │       └─ Update order status → refunded
    ├─ Upload proof screenshot
    └─ OrderEventService::record(REFUND_PAID)
         User dapat notif + uang di platform

Payout Queue: (sama tapi to agency)
    ├─ Admin view pending payouts
    ├─ Mark Processing → admin transfer (externally)
    ├─ Mark Paid → uploads proof
    └─ PayoutService::markPaid()
        ├─ LedgerEntry: agency_Y_payable → cash_bank
        ├─ Update payout status → paid
        └─ Update order status → released
```

---

## Ledger Accounting System

### Prinsip: Double-Entry untuk Setiap Rupiah

```
SETIAP transaksi HARUS balanced:
  Debit + Credit = 0

Example: Payment Settlement 500.000 IDR
  ┌─────────────────────────────────┐
  │ Debit:  customer_123      (+500k)│
  │ Credit: escrow_hold       (-500k)│
  ├─────────────────────────────────┤
  │ Balance: 0 ✅                    │
  └─────────────────────────────────┘
```

### Account Types

```
ASSETS:
  cash_bank
    = Uang kita di bank
    Increases when: admin marks refund/payout as paid
    
  escrow_hold
    = Dana yang ditahan (pending dispute window)
    Increases when: payment settled
    Decreases when: released to agency OR refunded to customer

LIABILITIES:
  customer_X_refundable
    = Berapa kita hutang ke customer (belum ditransfer)
    Increases when: dispute decision = full refund
    Decreases when: admin marks paid
    
  agency_Y_payable
    = Berapa kita hutang ke agency (belum ditransfer)
    Increases when: escrow released OR partial release
    Decreases when: admin marks paid
```

### Idempotency Mechanism

```php
LedgerService::record(
    entryKey: $entryKey,  // MUST BE UNIQUE
    debitAccount: 'customer_123',
    creditAccount: 'escrow_hold',
    amountIdr: 500000,
    ...
);

// entry_key harus UNIQUE di wallet_ledgers table
// 
// Jika create 2x dengan entry_key sama:
//   1st call  → INSERT successful
//   2nd call  → QueryException(Duplicate)
//            → Catch & return silently
//
// Result: IDEMPOTENT! Safe untuk retry logic

// Entry Key Format: TYPE:ID:ACTION
// Examples:
//   ORDER:123:ESCROW_HOLD
//   ORDER:123:AUTO_RELEASE
//   REFUND:456:PAID
//   PAYOUT:789:PAID
//   ORDER:123:REFUND_DECISION:dispute_999
```

---

## Midtrans Payment Integration

### Controller dengan Signature Verification

**File:** `app/Http/Controllers/Payment/MidtransNotificationController.php`

```php
Route: POST /api/payment/midtrans/notification

Method: handleNotification(Request $request)
  
  1. Extract payload
     $orderId = (string) $payload['order_id']
     $statusCode = (string) $payload['status_code']
     $grossAmount = (string) $payload['gross_amount']
     $signatureKey = (string) $payload['signature_key']
     
  2. VERIFY SIGNATURE (CRITICAL!)
     $signature = new MidtransSignature(config('midtrans.server_key'))
     
     if (!$signature->verify($orderId, $statusCode, $grossAmount, $signatureKey)) {
         return response()->json(['error' => 'Invalid signature'], 401);
     }
     
     Algorithm: SHA512(orderId + statusCode + grossAmount + serverKey)
     Compare dengan signature_key using hash_equals() [timing-safe]
     
  3. Map status code
     100         → settlement (settled ✅)
     201-408     → pending (menunggu)
     203         → expired (kadaluarsa)
     204         → denied (ditolak)
     401-419     → cancelled (dibatalkan)
     412-413     → chargeback (dispute)
     
  4. Process based on status
     if SETTLEMENT:
       → PaymentService::handlePaymentSettlement()
          ├─ Create EscrowHold
          ├─ Create LedgerEntry (idempotent)
          ├─ Update order status → paid_escrow
          └─ Record events
          
     if FAILED:
       → PaymentService::handlePaymentFailure()
          ├─ Update payment status
          └─ Update order status → cancelled
          
  5. Return 200 OK
```

### Support Classes

**MidtransSignature:** `app/Domain/Payment/Support/MidtransSignature.php`
```php
verify(orderId, statusCode, grossAmount, signatureKey): bool
generate(orderId, statusCode, grossAmount): string
```

**MidtransMapper:** `app/Domain/Payment/Support/MidtransMapper.php`
```php
mapStatus(transactionStatus): string
isSettled(transactionStatus): bool
isFailed(transactionStatus): bool
```

### Configuration

```php
config/midtrans.php
  MIDTRANS_SERVER_KEY=xxx (from Midtrans dashboard - KEEP SAFE!)
  MIDTRANS_CLIENT_KEY=xxx (from Midtrans dashboard - public OK)
  MIDTRANS_PRODUCTION=false (sandbox for testing)
```

### Security Notes

✅ **Signature verification implemented** (SHA512 timing-safe)
✅ **Server key in .env** (never hardcoded)
✅ **Duplicate prevention** (entry_key idempotency)
✅ **Row locking** (prevent race conditions)
✅ **Transaction wrapper** (atomic success/fail)

---

## Dispute Resolution Flow

### Timeline & Constraints

```
0h:
  Order completed

0-24h:
  [DISPUTE WINDOW OPEN ]
  Customer dapat complaint

24h+:
  if NO dispute opened:
    ReleaseEscrowJob auto-release
    
  if YES dispute opened:
    Admin review evidence
    Admin make decision
    Apply resolution immediately
```

### DisputeService Methods

**Open Dispute:**
```php
DisputeService::openDispute(
    orderId: 123,
    reason: 'Hasil kerja tidak sesuai',
    evidenceFiles: [photo1, photo2, ...]
)

Checks:
  ✓ Order status = completed
  ✓ Completed < 24h ago
  ✓ No active dispute exists
  
Actions:
  1. Lock order + escrow row
  2. Create Dispute (status: open)
  3. Create DisputeEvidence (files)
  4. Update order status → disputed
  5. Record events
```

**Resolve with Full Refund:**
```php
DisputeService::resolveWithFullRefund(disputeId, adminNotes)

Actions:
  1. Lock escrow
  2. EscrowService::refundFull()
     └─ LedgerEntry: escrow_hold → customer_X_refundable
  3. RefundService::queueRefund()
  4. Update order status → refund_pending
  5. Record OrderEvent (REFUND_QUEUED)
```

**Resolve with Full Release:**
```php
DisputeService::resolveWithFullRelease(disputeId, adminNotes)

Actions:
  1. Lock escrow
  2. EscrowService::releaseHold()
     └─ LedgerEntry: escrow_hold → agency_Y_payable
  3. PayoutService::queuePayout()
  4. Update order status → payout_pending
  5. Record OrderEvent (PAYOUT_QUEUED)
```

**Resolve with Partial:**
```php
DisputeService::resolveWithPartial(
    disputeId,
    refundAmount: 250000,  // Refund 50%
    notes
)

Actions:
  1. Lock escrow
  2. EscrowService::applyResolution(250000, 250000)
     ├─ LedgerEntry: escrow_hold → customer_X_refundable (250k)
     └─ LedgerEntry: escrow_hold → agency_Y_payable (250k)
  3. Queue BOTH refund + payout
  4. Update order status → partially_refunded
```

---

## Auto-Release Scheduler

### ReleaseEscrowJob Configuration

**File:** `app/Jobs/ReleaseEscrowJob.php`  
**Scheduled:** Every 15 minutes (via Console Kernel)

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->job(new \App\Jobs\ReleaseEscrowJob)
        ->everyFifteenMinutes()
        ->name('release-escrow-auto')
        ->withoutOverlapping();
}

Logic:
  1. Find orders cu:
     ├─ status = 'completed'
     └─ updated_at > NOW - ORDER_DISPUTE_WINDOW_HOURS (24h default)
     
  2. Process setiap order (max 100 per run):
     ├─ Lock order
     ├─ Check: status still 'completed'
     ├─ Check: no active dispute (open/investigating)
     ├─ If valid:
     │   ├─ Lock escrow hold
     │   ├─ Check: escrow status = 'hold'
     │   ├─ EscrowService::releaseHold()
     │   ├─ PayoutService::queuePayout()
     │   ├─ Update order → payout_pending
     │   └─ Record OrderEvent
     └─ Auto-commit transaction

Configuration (.env):
  ORDER_DISPUTE_WINDOW_HOURS=24

Benefits:
  ✅ No manual action needed
  ✅ Automatic after dispute window
  ✅ Safe from race conditions
  ✅ Idempotent (safe to re-run)
```

---

## Folder Structure & Models

### Domain Services Organization

```
app/Domain/
├── Shared/
│   ├── Statuses/
│   │   ├── OrderStatus.php
│   │   ├── PaymentStatus.php
│   │   ├── EscrowStatus.php
│   │   ├── DisputeStatus.php
│   │   ├── RefundStatus.php
│   │   └── PayoutStatus.php
│   └── Support/
│       ├── Idempotency.php
│       └── Locking.php
│
├── Payment/Services/PaymentService.php
├── Ledger/Services/LedgerService.php
├── Escrow/Services/EscrowService.php
├── Order/Services/OrderService.php
├── Dispute/Services/DisputeService.php
├── Refund/Services/RefundService.php
├── Payout/Services/PayoutService.php
├── Contract/Services/ContractService.php
├── Event/Services/OrderEventService.php
├── Audit/Services/AuditLogService.php
└── Bank/Services/BankAccountService.php
```

### Model Relationships

```
User → hasMany roles, orders_as_visitor, bank_accounts
     → hasOne primary_bank_account

Agency → belongsTo user(owner), location
       → hasMany orders, workers, bank_accounts

Order → belongsTo visitor(User), agency, contract
      → hasOne payment, escrow_hold, refund, payout
      → hasMany disputes, order_events

Payment → belongsTo order
EscrowHold → belongsTo order
Dispute → belongsTo order
Contract → belongsTo order
Refund → belongsTo order, bank_account
Payout → belongsTo order, agency, bank_account

WalletLedger → tracks all money movements
AuditLog → tracks all mutations
OrderEvent → complete timeline
```

---

## Setup & Installation

### Prerequisites

- PHP 8.3+
- MySQL 8.0+
- Composer
- Laravel 12.51.0
- Livewire 3.7.10

### Installation Steps

```bash
# 1. Clone & install
git clone <repo>
composer install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate  # Runs all 31 migrations

# 4. Seed master data
php artisan db:seed

# 5. Midtrans configuration (.env)
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_PRODUCTION=false

# 6. Dispute window configuration (.env)
ORDER_DISPUTE_WINDOW_HOURS=24

# 7. Setup scheduler (crontab)
* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1

# 8. Run application
php artisan serve
```

### Verification

```bash
# Check migrations
php artisan migrate:status
# Output: All 31 migrations should show [Ran] ✅

# Test services with tinker
php artisan tinker
> resolve('\App\Domain\Ledger\Services\LedgerService')->getAccountBalance('cash_bank')
```

---

## Key Principles & Best Practices

### 1. Transaction + Row Locking

Every critical mutation:

```php
DB::transaction(function () {
    $order = Order::lockForUpdate()->find($orderId);
    $escrow = EscrowHold::lockForUpdate()->where('order_id', $orderId)->first();
    
    // Safe to mutate both
    // Row lock ensures sequential (no concurrent mutation)
});

Benefits:
  ✅ Prevents race condition
  ✅ Atomic: all or nothing
  ✅ Guaranteed consistency
```

### 2. Idempotency via entry_key

```php
// entry_key = UNIQUE on wallet_ledgers table
// 
// Duplicate insert:
//   1st call  → success
//   2nd call  → QueryException → catch & return silently
// 
// Result: SAFE TO RETRY

LedgerService::record(
    entryKey: 'ORDER:123:ESCROW_HOLD',  // Must be unique
    ...
);
```

### 3. Service-First Architecture

```php
// Controller: thin layer only
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $order = $this->orderService->create($validated);
    return redirect()->route('orders.show', $order);
}

// All logic in Service
// Service can be called from Controller, Livewire, Job, API, Console
```

### 4. Double-Entry Accounting Always Balanced

```php
// Every transaction: Debit + Credit
LedgerService::record(
    debitAccount: 'customer_123',      // source
    creditAccount: 'escrow_hold',      // destination
    amountIdr: 500000,
    ...
);

// Balance formula: sum(credit) - sum(debit) per account
// Should always equal: planned_balance

// Benefits:
//   ✅ No money disappears
//   ✅ Automatic audit trail
//   ✅ Fraud impossible
```

### 5. Ledger as Single Source of Truth

```
Money balance NEVER stored on Order/Payment records!

Instead query ledger:
  SELECT SUM(amount_idr) FROM wallet_ledgers
  WHERE credit_account = 'customer_123'

Why:
  ✅ One source of truth
  ✅ Prevents inconsistency
  ✅ Easy audit/reconcile
```

### 6. Entry Key Format for Traceability

```
[TYPE]:[ID]:[ACTION]

Examples:
  ORDER:123:ESCROW_HOLD       = payment settlement
  ORDER:123:AUTO_RELEASE      = auto-release after 24h
  REFUND:456:PAID             = manual refund mark paid
  PAYOUT:789:PAID             = manual payout mark paid
  ORDER:123:REFUND_DECISION:999 = dispute resolution

Benefits:
  ✅ Easy to trace action
  ✅ Prevents duplicates
  ✅ Audit-friendly
```

### 7. Event Recording for Complete Timeline

```php
// Every important event recorded
OrderEventService::record(
    orderId: 123,
    type: 'PAYMENT_SETTLED',
    message: 'Pembayaran diterima',
    payload: ['amount_idr' => 500000, 'midtrans_order_id' => 'PBW-123']
);

// Timeline completely traceable from creation to completion
```

### 8. Signature Verification for Payment Callbacks

```php
// CRITICAL: Verify every Midtrans callback

$signature = new MidtransSignature(config('midtrans.server_key'));

if (!$signature->verify($orderId, $statusCode, $grossAmount, $signatureKey)) {
    return response()->json(['error' => 'Invalid'], 401);
}

// Only THEN process callback

Benefits:
  ✅ Prevents malicious callbacks
  ✅ Prevents man-in-the-middle
  ✅ Uses strong SHA512 crypto
```

### 9. Status State Machine Validation

```
Valid transitions only (enforced in OrderService):

pending_payment → paid_escrow ✅
paid_escrow → in_progress ✅
in_progress → completed ✅
completed → disputed ✅
completed → payout_pending ✅
disputed → refund_pending ✅
disputed → payout_pending ✅
refund_pending → refunded ✅
payout_pending → released ✅

Any other transition → throw InvalidStatusTransition

Benefit:
  ✅ Impossible to reach invalid states
```

---

## Admin Operations

### Refund Queue (Manual)

**Location:** Admin Panel / RefundQueue Livewire Component

```
Admin views pending refunds:

Step 1: Mark Processing
  ├─ RefundService::markProcessing(refundId)
  └─ Update refund status → processing

Step 2: Manual Bank Transfer
  ├─ Admin sends money via bank transfer (externally)
  └─ Saves transfer screenshot/proof

Step 3: Mark Paid
  ├─ RefundService::markPaid(refundId, proofPhoto, transferDate)
  ├─ LedgerEntry: customer_X_refundable → cash_bank
  ├─ Update refund status → paid
  ├─ Update order status → refunded
  └─ OrderEventService::record(REFUND_PAID)

Result:
  ✅ Money credited to customer wallet on Pembantu
  ✅ Audit trail recorded
  ✅ Notification sent to customer
```

### Payout Queue (Manual)

**Location:** Admin Panel / PayoutQueue Livewire Component

```
Same flow but to Agency bank account:

Step 1: Mark Processing
  └─ PayoutService::markProcessing(payoutId)

Step 2: Manual Bank Transfer
  └─ Admin transfers to agency bank account

Step 3: Mark Paid
  └─ PayoutService::markPaid(payoutId, proofPhoto, transferDate)
     ├─ LedgerEntry: agency_Y_payable → cash_bank
     ├─ Update payout status → paid
     ├─ Update order status → released
     └─ OrderEventService::record(PAYOUT_PAID)

Result:
  ✅ Money transferred to agency
  ✅ Complete order journey finished
```

### Dispute Queue (Admin)

**Location:** Admin Panel / DisputeQueue Livewire Component

```
Admin views open disputes:

Step 1: Review Dispute
  ├─ View order details
  ├─ View customer reason
  ├─ View evidence files (photos, etc)
  └─ View timeline

Step 2: Make Decision
  ├─ Option A: Full Refund
  │   └─ DisputeService::resolveWithFullRefund(disputeId, notes)
  │
  ├─ Option B: Full Release (favor agency)
  │   └─ DisputeService::resolveWithFullRelease(disputeId, notes)
  │
  └─ Option C: Partial (split)
      └─ DisputeService::resolveWithPartial(disputeId, refundAmount, notes)

Step 3: System Processing
  ├─ Escrow released/refunded (auto via service)
  ├─ Refund/Payout queued (admin transfer manually later)
  └─ Order status updated automatically

Result:
  ✅ Dispute resolved
  ✅ Queued for admin manual transfer
```

---

## Order Status State Machine (Complete)

```
                    ┌─ pending_payment
                    │        │
                    │        │ [payment settlement]
                    │        ↓
                    │    paid_escrow
                    │        │
                    │        │ [admin action]
                    │        ↓
                    │    in_progress
                    │        │
                    │        │ [agency completes]
                    │        ↓
                    │    completed
                    │     ↙  ↓  ↖
                    │    /   │   \
        [dispute] /      │    [no dispute] \
                 /        │    [24h pass]    \
                /         ↓                   \
            disputed    [auto-release]    payout_pending
             ↙  ↖      ReleaseEscrowJob        │
            /     \                            │
        [admin]  [admin]                  [admin marks paid]
        /           \                          ↓
      refund_      payout_                  released
      pending      pending         [END - order complete]
        │            │
        │            │ [admin marks paid]
        │            ↓
        │          released
        │
        │ [admin marks paid]
        ↓
      refunded
    [END - refunded]
```

---

## Monitoring & Verification

### Check Ledger Balance

```php
$balance = LedgerService::getAccountBalance('customer_123');
// Returns: sum(all credits) - sum(all debits)
// Should match amount we owe to/owe from customer
```

### Check Order Status

```php
$order = Order::find($orderId);
// Must be in valid state per state machine
// Check order->status is one of: pending_payment, paid_escrow, in_progress, etc
```

### View Complete Timeline

```php
$events = OrderEvent::where('order_id', $orderId)
    ->orderBy('created_at', 'asc')
    ->get();
// Complete timeline from creation to current state
```

### Audit Trail

```php
$auditLogs = AuditLog::where('model_type', 'Order')
    ->where('model_id', $orderId)
    ->orderBy('created_at', 'asc')
    ->get();
// Complete mutation history with before/after values
```

---

## Next Steps (Implementation Roadmap)

✅ **Step 7-14:** Complete
- Database schema (31 migrations)
- Service layer (11 services)
- Payment integration (Midtrans)
- Dispute resolution
- Auto-release scheduler

🔄 **Step 15-20:** Frontend & UI
- Livewire components (CheckoutWizard, DisputeQueue, etc)
- Blade views/templates
- Form validation
- Admin dashboard

🔄 **Step 21-25:** Features & Polish
- Email notifications
- SMS notifications
- Chat system
- Reviews & ratings
- Worker calendars

---

**Status:** ✅ Backend infrastructure COMPLETE. Ready for UI/Livewire implementation!

*Last Updated: February 17, 2026*
