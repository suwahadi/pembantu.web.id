# Pembantu.web.id — Brief Teknis Terstruktur (Laravel 12 + Livewire, PHP 8.3, MySQL)

Dokumen ini adalah **prompt teknis lengkap** untuk agent AI/engineer yang akan membangun aplikasi **Pembantu.web.id** (marketplace multi-jasa tenaga kerja) dengan fokus pada:
- Arsitektur **service-first** (logic bisnis di service layer, bukan di Livewire/Controller).
- Aman dari **race condition** (khususnya pembayaran, escrow, refund, payout).
- Transaksi transparan: kontrak kerja, event timeline, dispute, audit trail.
- Payment gateway **Midtrans Core API** (bukan Snap).
- **Refund manual** oleh admin via transfer bank (bukan via Midtrans).
- Bahasa UI dan label sistem: **Bahasa Indonesia**.
- Mata uang: **IDR saja** (tanpa desimal).
- Format tanggal UI: **“Senin, 17 Feb 2026”** (contoh format; gunakan locale Indonesia).

---

## 1) Ringkasan Produk & Model Bisnis

### 1.1. Peran (Role)
- **Admin Platform**: memverifikasi agency/worker, memproses dispute, menjalankan refund manual, memonitor payout, audit.
- **Agency**: memposting tenaga kerja (worker), mengelola profil, harga, area layanan; menerima payout.
- **Pengunjung (Visitor)**: mencari worker, membuat kontrak dan order, membayar, membuka dispute, menerima refund manual bila diperlukan.

### 1.2. Kategori Jasa
Multi jasa minimal:
- ART / PRT
- Babysitter
- Perawat Lansia
- Tukang Kebun
- Sopir

### 1.3. Alur Tingkat Tinggi (Escrow)
1) Agency memposting worker + harga + area layanan.  
2) Pengunjung memilih worker → membuat kontrak kerja → membuat order.  
3) Pengunjung membayar via Midtrans Core API → dana **di-hold** sebagai escrow di sistem.  
4) Pekerjaan berjalan → selesai.  
5) Setelah window dispute lewat (misal 24 jam) → dana escrow **release** → payout ke agency.  
6) Jika dispute: admin memutuskan refund/release/parsial:  
   - Keputusan memindahkan escrow menjadi **payable** (kewajiban platform).  
   - Refund/payout dilakukan **manual** via bank transfer oleh admin.

---

## 2) Prinsip Arsitektur (Wajib)

### 2.1. Service-First
- Semua mutasi status, perhitungan harga, escrow, ledger, dispute, payout/refund **harus** melalui **Service Layer**.
- Livewire/Controller hanya:
  - Validasi input (atau FormRequest).
  - Memanggil service.
  - Menampilkan hasil.

### 2.2. Bebas Race Condition
- Semua operasi kritis menggunakan:
  - **DB Transaction** (`DB::transaction`)
  - **Row Lock** (`SELECT ... FOR UPDATE` / Eloquent `lockForUpdate()`)
  - **Idempotency** melalui unique key (terutama ledger entry).

### 2.3. Single Source of Truth untuk Uang
- Pergerakan dana tercatat di `wallet_ledgers` dengan `entry_key` **unik**.
- Status escrow (`escrow_holds`) mengikuti keputusan bisnis, bukan sekadar status pembayaran.

### 2.4. Auditability & Transparansi
- Setiap aksi penting: simpan `order_events` (timeline) dan `audit_logs` (before/after, actor).
- Dispute harus menyimpan keputusan dan evidence.

---

## 3) Status & State Machine (Wajib Konsisten)

### 3.1. Order Status (contoh minimal yang disarankan)
- `pending_payment` — order dibuat, menunggu pembayaran
- `paid_escrow` — pembayaran settled, escrow hold dibuat
- `in_progress` — pekerjaan dimulai
- `completed` — pekerjaan selesai
- `disputed` — dispute aktif (open/investigating)
- `refund_pending` — keputusan refund dibuat, menunggu transfer manual
- `payout_pending` — keputusan release dibuat, menunggu transfer payout
- `refunded` — refund selesai (transfer manual selesai)
- `partially_refunded` — refund parsial selesai
- `released` — dana dilepas untuk agency (payout dilakukan/atau payable tercatat)
- `cancelled` — order dibatalkan (misal expire sebelum settlement)

### 3.2. Payment Status (Midtrans)
- `initiated`, `pending`, `settlement`, `expire`, `cancel`, `deny`, `chargeback`

### 3.3. Escrow Hold Status
- `hold`, `released`, `refunded`, `partial_refunded`, `partial_released` (opsional)

### 3.4. Dispute Status
- `open`, `investigating`, `resolved`, `rejected`

### 3.5. Refund Status (manual bank transfer)
- `queued`, `processing`, `paid`, `failed`, `cancelled`

### 3.6. Payout Status (manual/terjadwal bank transfer)
- `queued`, `processing`, `paid`, `failed`

> Semua status disimpan sebagai string dan didefinisikan dalam konstanta (class) agar mudah refactor.

---

## 4) Modul Utama & Tanggung Jawab

### 4.1. Catalog Worker (Read-heavy)
- Filter berdasarkan kategori, lokasi, tarif, skill, pengalaman, rating, availability.
- Halaman profil worker menampilkan:
  - Dokumen (status verifikasi)
  - Skill
  - Harga (skema harian/mingguan/bulanan/per jam opsional)
  - Area layanan

### 4.2. Contract & Order
- Kontrak dibuat dari template kategori.
- Kontrak harus bisa di-sign oleh pengunjung & agency.
- Setelah kontrak final → buat order.

### 4.3. Payment (Midtrans Core API)
- Charge server-to-server.
- Callback/notification harus idempoten.
- Settlement → create escrow hold → update order.

### 4.4. Escrow + Ledger
- Escrow hold dibuat saat payment settlement.
- Release/refund (termasuk parsial) menghasilkan ledger entries idempoten.

### 4.5. Dispute Center
- Pengunjung dapat membuka dispute pada window yang ditentukan.
- Admin memutuskan:
  - refund penuh
  - release penuh
  - parsial (refund + release)

### 4.6. Refund Manual (Bank Transfer)
- Refund tidak melalui Midtrans.
- Admin melakukan transfer bank manual dan upload bukti transfer.
- Sistem mencatat ledger “payable → cash_bank” saat admin menandai paid.

### 4.7. Payout ke Agency
- Setelah release: buat record payout `queued`.
- Admin transfer manual / atau integrasi disbursement di masa depan.
- Mirip refund flow (upload bukti, mark paid).

---

## 5) Aturan Data Rekening Bank (Wajib)

- Pengunjung dan agency **wajib** memiliki rekening bank untuk proses refund/payout.
- Rekening disimpan pada `bank_accounts`.
- Primary rekening disimpan pada:
  - `users.primary_bank_account_id`
  - `agencies.primary_bank_account_id`
- Validasi minimal:
  - `account_no`: string 8–30, numeric-like
  - `account_name`: 3–120 karakter
- Verifikasi rekening:
  - `verified_status`: `unverified/verified/rejected`
  - Untuk tahap awal: verifikasi manual oleh admin.

---

## 6) Struktur Folder (Plain TXT Hierarki)

Berikut struktur yang disarankan agar service-first dan terorganisir.

```
app
├─ Domain
│  ├─ Shared
│  │  ├─ Statuses
│  │  │  ├─ OrderStatus.php
│  │  │  ├─ PaymentStatus.php
│  │  │  ├─ EscrowStatus.php
│  │  │  ├─ DisputeStatus.php
│  │  │  ├─ RefundStatus.php
│  │  │  └─ PayoutStatus.php
│  │  ├─ ValueObjects
│  │  │  ├─ MoneyIdr.php
│  │  │  └─ DateId.php
│  │  └─ Support
│  │     ├─ Idempotency.php
│  │     └─ Locking.php
│  │
│  ├─ Bank
│  │  ├─ Services
│  │  │  └─ BankAccountService.php
│  │  └─ Policies
│  │     └─ BankAccountPolicy.php
│  │
│  ├─ Worker
│  │  ├─ Services
│  │  │  └─ WorkerCatalogService.php
│  │  └─ DTO
│  │     └─ WorkerSearchDTO.php
│  │
│  ├─ Contract
│  │  ├─ Services
│  │  │  └─ ContractService.php
│  │  └─ DTO
│  │     └─ ContractDraftDTO.php
│  │
│  ├─ Order
│  │  ├─ Services
│  │  │  └─ OrderService.php
│  │  └─ DTO
│  │     └─ CreateOrderDTO.php
│  │
│  ├─ Payment
│  │  ├─ Services
│  │  │  ├─ MidtransCoreService.php
│  │  │  └─ PaymentService.php
│  │  └─ Support
│  │     ├─ MidtransSignature.php
│  │     └─ MidtransMapper.php
│  │
│  ├─ Ledger
│  │  └─ Services
│  │     └─ LedgerService.php
│  │
│  ├─ Escrow
│  │  └─ Services
│  │     └─ EscrowService.php
│  │
│  ├─ Dispute
│  │  └─ Services
│  │     └─ DisputeService.php
│  │
│  ├─ Refund
│  │  ├─ Services
│  │  │  └─ RefundService.php
│  │  └─ Policies
│  │     └─ RefundPolicy.php
│  │
│  ├─ Payout
│  │  └─ Services
│  │     └─ PayoutService.php
│  │
│  ├─ Audit
│  │  └─ Services
│  │     └─ AuditLogService.php
│  │
│  └─ Event
│     └─ Services
│        └─ OrderEventService.php
│
├─ Http
│  ├─ Controllers
│  │  └─ Payment
│  │     └─ MidtransNotificationController.php
│  ├─ Requests
│  │  ├─ BankAccountRequest.php
│  │  ├─ ContractRequest.php
│  │  └─ DisputeRequest.php
│  └─ Middleware
│     └─ VerifyMidtransSignature.php
│
├─ Livewire
│  ├─ Visitor
│  │  ├─ WorkerSearch.php
│  │  ├─ CheckoutWizard.php
│  │  ├─ BankAccountForm.php
│  │  ├─ OrderDetail.php
│  │  └─ DisputeForm.php
│  ├─ Agency
│  │  ├─ WorkerManage.php
│  │  ├─ PricingManage.php
│  │  ├─ BankAccountForm.php
│  │  └─ OrdersBoard.php
│  └─ Admin
│     ├─ AgencyVerification.php
│     ├─ WorkerVerification.php
│     ├─ DisputeQueue.php
│     ├─ RefundQueue.php
│     └─ PayoutQueue.php
│
├─ Jobs
│  ├─ ReleaseEscrowJob.php
│  ├─ QueuePayoutJob.php
│  └─ SendNotificationJob.php
│
├─ Policies
│  ├─ OrderPolicy.php
│  └─ DisputePolicy.php
│
└─ Providers
   └─ DomainServiceProvider.php

resources
├─ views
│  ├─ livewire
│  │  ├─ visitor
│  │  ├─ agency
│  │  └─ admin
│  └─ layouts
└─ lang
   └─ id
      └─ messages.php

database
├─ migrations
└─ seeders
   ├─ RolesSeeder.php
   ├─ ServiceCategoriesSeeder.php
   ├─ ServiceSkillsSeeder.php
   └─ LocationsSeeder.php
```

---

## 7) Struktur Database — Migration Final (Urutan & Ringkasan)

### 7.1. Master & Auth
1. `create_users_table`
2. `create_roles_table`
3. `create_user_roles_table`
4. `create_locations_table`
5. `create_service_categories_table`
6. `create_service_skills_table`
7. `create_agencies_table`

### 7.2. Rekening Bank
8. `add_primary_bank_account_id_to_users_table` (nullable)
9. `add_primary_bank_account_id_to_agencies_table` (nullable)
10. `create_bank_accounts_table`
11. `add_fk_primary_bank_account_id_users_agencies` (FK -> bank_accounts)

### 7.3. Worker Catalog
12. `create_workers_table`
13. `create_worker_documents_table`
14. `create_worker_skills_table`
15. `create_worker_service_pricing_table`
16. `create_worker_service_areas_table`

### 7.4. Transaksi (Kontrak & Order)
17. `create_orders_table`
18. `create_contracts_table` (FK unique order_id)
19. `add_contract_id_to_orders_table` (FK unique contract_id) *(opsional bila tidak langsung di orders)*

### 7.5. Pembayaran & Escrow
20. `create_payments_table` (unique order_id, unique midtrans_order_id)
21. `create_escrow_holds_table` (unique order_id)
22. `create_wallet_ledgers_table` (unique entry_key)
23. `create_payouts_table` (unique order_id)

### 7.6. Dispute, Refund, Review, Audit
24. `create_disputes_table`
25. `create_dispute_evidences_table`
26. `create_refunds_table`
27. `create_reviews_table`
28. `create_audit_logs_table`
29. `create_order_events_table` *(opsional tapi sangat disarankan)*

---

## 8) Field Penting per Tabel (Rangkuman)

### 8.1. orders
- `code` UNIQUE
- `visitor_user_id`, `agency_id`, `worker_id`, `category_id`
- `status`
- `subtotal_idr`, `platform_fee_idr`, `total_idr`
- `contract_id` UNIQUE nullable (1:1)

Index wajib:
- `INDEX(visitor_user_id, created_at)`
- `INDEX(worker_id, status)`
- `INDEX(agency_id, status)`
- `INDEX(status)`

### 8.2. contracts
- `order_id` UNIQUE
- `contract_no` UNIQUE
- periode kerja, alamat, scope, total

### 8.3. payments
- `order_id` UNIQUE
- `midtrans_order_id` UNIQUE
- `transaction_id` UNIQUE nullable
- `status`, payload JSON

### 8.4. escrow_holds
- `order_id` UNIQUE
- `amount_idr`
- `status`
- timestamps (held/released/refunded)

### 8.5. wallet_ledgers
- `entry_key` UNIQUE (idempotency)
- `debit_account`, `credit_account`
- `amount_idr`, `ref_type`, `ref_id`

### 8.6. refunds (manual)
- `order_id`
- `payee_type` (USER/AGENCY), `payee_id`
- `bank_account_id`
- `amount_idr`
- `status`
- `proof_file_path`, `paid_at`

### 8.7. payouts
- mirip refunds, tapi payee = agency

### 8.8. disputes
- `order_id`
- `status`, `decision`, `refund_amount_idr`, `release_amount_idr`

### 8.9. bank_accounts
- `owner_type`, `owner_id`
- `bank_code`, `bank_name`
- `account_no`, `account_name`
- `verified_status`

---

## 9) Aturan Implementasi Service Layer (Checklist Wajib)

### 9.1. Pola umum mutasi
Untuk setiap operasi mutasi:
1) `DB::transaction()`
2) Ambil row kunci pakai `lockForUpdate()`
3) Validasi status saat ini
4) Update status/record
5) Catat ledger (`entry_key` unik) → jika duplikat, treat as success
6) Tulis `audit_logs` + `order_events`
7) Dispatch job via `DB::afterCommit()` jika perlu

### 9.2. Idempotency Key Standar
Contoh pola `entry_key`:
- Payment settlement: `ORDER:{order_id}:PAYMENT_SETTLEMENT:{transaction_id}`
- Escrow hold: `ORDER:{order_id}:ESCROW_HOLD`
- Dispute refund decision: `ORDER:{order_id}:REFUND_DECISION:{dispute_id}`
- Refund paid: `REFUND:{refund_id}:PAID`
- Payout paid: `PAYOUT:{payout_id}:PAID`

### 9.3. Midtrans Callback (Wajib)
- Verifikasi signature sesuai Midtrans.
- Jangan percaya payload tanpa validasi.
- Proses hanya berdasarkan `midtrans_order_id` yang sudah tercatat di `payments`.
- Callback bisa dikirim berulang → harus idempoten.

### 9.4. Refund Manual
- Keputusan dispute memindahkan uang dari escrow → payable (ledger).
- Admin menandai paid → payable berkurang (ledger).
- Status order berubah ke `refunded/partially_refunded` setelah paid.

### 9.5. Payout Manual
- Release memindahkan escrow → agency payable.
- Admin menandai paid → agency payable berkurang.
- Status order bisa `released` saat keputusan release dibuat, atau setelah payout paid (pilih satu dan konsisten; disarankan: `payout_pending` lalu `released` setelah paid).

---

## 10) Konvensi UI (Bahasa Indonesia)
- Semua label, pesan error, notifikasi: Bahasa Indonesia.
- Format uang: `Rp {number_format(..., 0, ',', '.')}`
- Format tanggal: `translatedFormat('l, d F Y')` dengan locale `id`.

---

## 11) Batasan & Hal yang Tidak Dilakukan (Agar Tidak “Halu”)
- Refund tidak menggunakan Midtrans API.
- Tidak ada validasi rekening otomatis via bank API (tahap awal manual).
- Tidak ada fitur chat realtime wajib; boleh Livewire + polling sederhana atau table messages.

---

## 12) Deliverables Minimum (MVP yang aman)
1) Auth + role (Admin/Agency/Visitor)
2) Agency posting worker + pricing + area
3) Visitor search + detail worker
4) Contract draft → sign → order create
5) Midtrans core charge + callback settlement → escrow hold
6) Order lifecycle: start, complete
7) Dispute open + evidence upload
8) Admin resolve dispute (refund/release/partial) + ledger + order events
9) Refund manual queue (admin) + upload bukti transfer + mark paid
10) Payout queue (admin) + mark paid
11) Audit logs + order timeline

---

## 13) Catatan Implementasi Midtrans (Ringkas)
- Gunakan `midtrans_order_id` = `orders.code` (unik).
- Simpan `request_payload` dan `last_callback_payload` untuk audit.
- Status settlement memicu:
  - `escrow_holds` create (unique order_id)
  - ledger entry hold
  - update `orders.status` → `paid_escrow`

---

Selesai.
