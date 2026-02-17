# IMPLEMENTATION GUIDE - Pembantu.web.id

## Arsitektur & Struktur Proyek

Proyek ini menggunakan **Service-First Architecture** dengan struktur Domain-Driven Design. Semua business logic tersentralisasi di Service Layer.

---

## Struktur Folder Dijelaskan

### `app/Domain/`
Lokasi semua business logic, terpisah per domain:

- **Shared/**: Status constants, Value Objects, Support classes
  - `Statuses/`: OrderStatus, PaymentStatus, EscrowStatus, DisputeStatus, RefundStatus, PayoutStatus
  - `ValueObjects/`: MoneyIdr (untuk handling uang IDR), DateId
  - `Support/`: Idempotency (generate entry keys), Locking (row lock helpers)

- **Payment/**: Midtrans integration
  - `Services/PaymentService.php`: Core payment logic
  - `Support/`: MidtransSignature, MidtransMapper
  
- **Ledger/**: Wallet ledger & accounting
  - `Services/LedgerService.php`: Create entries dengan idempotency key
  
- **Escrow/**: Escrow hold management
  - `Services/EscrowService.php`: Hold, release, refund operations
  
- **Order/**: Order lifecycle
  - `Services/OrderService.php`: Create, update status, cancel
  - `DTO/`: CreateOrderDTO
  
- **Dispute/**: Dispute resolution
  - `Services/DisputeService.php`: Open, resolve, reject disputes
  
- **Refund/**: Manual refund processing
  - `Services/RefundService.php`: Queue, process, mark paid
  
- **Payout/**: Agency payout management
  - `Services/PayoutService.php`: Queue, process payouts
  
- **Contract/**: Contract management
  - `Services/ContractService.php`: Create, sign contracts
  
- **Bank/**: Bank account management
  - `Services/BankAccountService.php`: Create, verify, set primary
  
- **Worker/**: Worker catalog & search
  - `Services/WorkerCatalogService.php`: Search, filter, get details
  - `DTO/WorkerSearchDTO.php`: Search parameters
  
- **Event/**: Order timeline events
  - `Services/OrderEventService.php`: Record status changes & events
  
- **Audit/**: Audit logging
  - `Services/AuditLogService.php`: Log all actions

### `app/Http/`
- **Controllers/Payment/**: MidtransNotificationController untuk handle callback
- **Requests/**: Form validation requests
- **Middleware/**: VerifyMidtransSignature untuk validasi callback

### `app/Livewire/`
UI Components untuk 3 role:
- **Visitor/**: WorkerSearch, CheckoutWizard, OrderDetail, DisputeForm
- **Agency/**: WorkerManage, PricingManage, OrdersBoard
- **Admin/**: AgencyVerification, WorkerVerification, DisputeQueue, RefundQueue, PayoutQueue

### `app/Models/`
Eloquent models untuk semua entities dengan relationships

### `database/`
- **migrations/**: 25 migration files (urutan penting!)
- **seeders/**: RolesSeeder, ServiceCategoriesSeeder, ServiceSkillsSeeder, LocationsSeeder

---

## Flow Utama: Order → Payment → Escrow → Complete → Release/Refund

### 1. CREATE ORDER
```
Visitor pilih worker → Request create Contract 
→ ContractService::create() 
→ Both sign (visitor + agency) 
→ OrderService::createFromContract()
```
**Status**: `pending_payment`

### 2. PAYMENT (Midtrans Core API)
```
Visitor bayar via Midtrans 
→ MidtransNotificationController::handle() 
→ PaymentService::handlePaymentSettlement()
  - Create Payment record (status: settlement)
  - Create EscrowHold (status: hold)
  - Create LedgerEntry (idempotent via entry_key)
  - Order status → paid_escrow
```
**Ledger**: `customer_X → escrow_hold`

### 3. ORDER IN PROGRESS
```
OrderService::updateStatus(in_progress)
→ Create OrderEvent
→ Create AuditLog
```

### 4. COMPLETE ORDER
```
OrderService::complete()
→ Status: completed
→ Create OrderEvent
```

### 5. DISPUTE WINDOW (24 jam setelah completed)
Jika ada dispute:
```
DisputeService::openDispute()
→ Order status: disputed
→ After admin resolves:
  - Full refund: EscrowService::refundFull()
    Ledger: escrow_hold → customer_X_refundable
    RefundService::queueRefund()
  - Full release: EscrowService::releaseHold()
    Ledger: escrow_hold → agency_Y_payable
    PayoutService::queuePayout()
  - Partial: EscrowService::refundPartial()
    Both ledger entries created
```

### 6. MANUAL TRANSFERS (Admin)
```
RefundService::markProcessing() → markPaid()
→ Ledger: customer_X_refundable → cash_bank
→ Order status: refunded

atau

PayoutService::markProcessing() → markPaid()
→ Ledger: agency_Y_payable → cash_bank
→ Order status: released
```

---

## Key Principles

### 1. Service-First
- Semua mutasi logic di Service
- Livewire/Controller hanya:
  ```php
  // Validate
  $validated = $request->validate(...);
  
  // Call service
  $result = $this->orderService->create($data);
  
  // Return
  return redirect()->with('success', 'Done');
  ```

### 2. Transaction & Locking
```php
DB::transaction(function () {
    $model = Model::lockForUpdate()->findOrFail($id);
    $model->update(['status' => 'new']);
    // Ledger, event, audit
});
```

### 3. Idempotency (duplikat call = sukses)
```php
$entryKey = Idempotency::paymentSettlementKey($orderId, $transactionId);
$this->ledgerService->createEntry(
    entryKey: $entryKey, // UNIQUE - jika duplikat, return existing
    // ...
);
```

### 4. Single Source of Truth
- Wallet ledger = satu-satunya sumber uang yang pernah ada
- Account balance = sum(credit) - sum(debit)
- Tidak ada kolom balance di user/agency

### 5. Auditability
- Setiap action: AuditLog (before/after data)
- Setiap status change: OrderEvent (timeline)
- Bayar perhatian: `actor_type`, `actor_user_id` untuk tahu siapa yang action

---

## Setup & Run

### 1. Install Dependencies
```bash
cd /laragon/www/pembantu.web.id
composer install

# Jika belum punya Laravel project, generate key dulu
php artisan key:generate
```

### 2. Environment Setup
```bash
cp .env.example .env
# Edit .env dengan:
DATABASE_CONNECTION=mysql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=3306
DATABASE_NAME=pembantu_web_id
DATABASE_USER=root
DATABASE_PASSWORD=

MIDTRANS_SERVER_KEY=???
MIDTRANS_CLIENT_KEY=???
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_CALLBACK_URL=https://localhost:8000/callback/midtrans
```

### 3. Database
```bash
# Create database di MySQL
mysql -u root -p
> CREATE DATABASE pembantu_web_id CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Run migrations (urutan penting!)
php artisan migrate

# Run seeders
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=ServiceCategoriesSeeder
php artisan db:seed --class=ServiceSkillsSeeder
php artisan db:seed --class=LocationsSeeder
```

### 4. Run Development
```bash
php artisan serve
# Atau dengan Laragon: double-click Pembantu.web.id di list
```

---

## Testing Payment Callback

Untuk test Midtrans callback di development:

1. Setup ngrok atau tunnel
2. Update MIDTRANS_CALLBACK_URL di .env
3. Set Midtrans dashboard webhook URL
4. Trigger payment di app → akan dapat callback

---

## Models & Relationships

```
User (many) ← has many → Role (many)
User → hasOne Agency
User → hasMany BankAccount (morph)
User → hasMany Order (as visitor)

Agency → belongsTo User
Agency → hasMany Worker
Agency → hasMany Order
Agency → hasMany BankAccount (morph)
Agency → hasMany Payout

Worker → belongsTo Agency, Category, Location
Worker → hasMany Order
Worker → hasMany WorkerDocument, Skill, Pricing, ServiceArea

Order → belongsTo Visitor(User), Agency, Worker, Category
Order → hasOne Contract, Payment, EscrowHold, Dispute, Refund, Payout, Review
Order → hasMany OrderEvent

Payment → belongsTo Order
EscrowHold → belongsTo Order
Contract → belongsTo Order

Dispute → belongsTo Order, OpenedBy(User), ResolvedBy(User)
Dispute → hasMany DisputeEvidence

Refund → belongsTo Order, BankAccount
Payout → belongsTo Order, Agency, BankAccount

WalletLedger (accounting)
AuditLog (history)
OrderEvent (timeline)
```

---

## Status State Machine (Validasi)

**Order** traversal yang valid:
- pending_payment → paid_escrow
- paid_escrow → in_progress, disputed, cancelled
- in_progress → completed, disputed, cancelled
- completed → disputed, released, refunded
- disputed → refund_pending, payout_pending, rejected
- refund_pending → refunded
- payout_pending → released, partially_refunded
- refunded, released, cancelled (terminal)

**Payment** statuses dari Midtrans:
- initiated → pending, pending, settlement, expire, cancel, deny, chargeback

**Escrow** statuses:
- hold → released, refunded, partial_refunded, partial_released

---

## Next Steps (TODO)

Implementasi berikutnya:

1. **Livewire Components (UI)**
   - WorkerSearch: search, filter, pagination
   - CheckoutWizard: contract, quantity, price calculation, payment initiation
   - DisputeQueue: admin resolve disputes
   - RefundQueue: admin mark paid
   - PayoutQueue: admin mark paid

2. **API Endpoints (untuk Midtrans & Admin)**
   - POST /api/payment/initiate
   - POST /api/payment/callback
   - POST /api/dispute/{id}/resolve
   - POST /api/refund/{id}/paid
   - POST /api/payout/{id}/paid

3. **Views (Blade templates)**
   - Mapping untuk setiap Livewire component
   - Bootstrap/Tailwind styling
   - Locale Indonesia timezone

4. **Jobs & Scheduling**
   - ReleaseEscrowJob: automatic release setelah 24/48 jam
   - SendNotificationJob: email/SMS notifications

5. **Admin Dashboard**
   - Analytics: orders, revenue, disputes
   - Verification queues (agency, worker, documents)
   - Refund/Payout queues

6. **Additional Features**
   - Review & ratings system
   - Chat messaging
   - Worker availability calendar
   - Agency statistics

---

## Dokumentasi Referensi

- **Brief**: pembantu_web_id_brief.md
- **Status Classes**: app/Domain/Shared/Statuses/
- **Models**: app/Models/
- **Services**: app/Domain/*/Services/
- **Migrations**: database/migrations/

---

Semua files sudah dibuat dan siap untuk development lebih lanjut!
