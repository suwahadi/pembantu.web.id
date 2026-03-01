<?php

namespace Database\Seeders;

use App\Domain\Shared\Statuses\DisputeStatus;
use App\Domain\Shared\Statuses\OrderStatus;
use App\Domain\Shared\Statuses\PaymentStatus;
use App\Models\Agency;
use App\Models\BankAccount;
use App\Models\Contract;
use App\Models\Dispute;
use App\Models\DisputeEvidence;
use App\Models\Location;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\Payout;
use App\Models\Refund;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestingScenarioSeeder extends Seeder
{
    public function run(): void
    {
        // Data referensi
        $visitor1 = User::whereEmail('anwar@example.com')->first();
        $visitor2 = User::whereEmail('siti@example.com')->first();
        $agency1User = User::whereEmail('agency1@example.com')->first();
        $agency2User = User::whereEmail('agency2@example.com')->first();
        $admin = User::whereEmail('admin@pembantu.web.id')->first();

        $agencyProfile1 = $agency1User ? Agency::where('user_id', $agency1User->id)->first() : null;
        $agencyProfile2 = $agency2User ? Agency::where('user_id', $agency2User->id)->first() : null;
        $worker1 = Worker::first();
        $worker2 = Worker::skip(1)->first();

        $visitor1Bank = $visitor1 ? BankAccount::whereOwnerType(User::class)->whereOwnerId($visitor1->id)->first() : null;
        $visitor2Bank = $visitor2 ? BankAccount::whereOwnerType(User::class)->whereOwnerId($visitor2->id)->first() : null;
        $agency1Bank = $agencyProfile1 ? BankAccount::whereOwnerType(Agency::class)->whereOwnerId($agencyProfile1->id)->first() : null;
        $agency2Bank = $agencyProfile2 ? BankAccount::whereOwnerType(Agency::class)->whereOwnerId($agencyProfile2->id)->first() : null;

        $categoryArt = ServiceCategory::whereName('ART / PRT')->first();
        $categoryBaby = ServiceCategory::whereName('Babysitter')->first();
        $locationJakarta = Location::where('city', 'Jakarta Pusat')->first();

        $required = [
            'visitor1' => $visitor1,
            'visitor2' => $visitor2,
            'agency1User' => $agency1User,
            'agency2User' => $agency2User,
            'agencyProfile1' => $agencyProfile1,
            'agencyProfile2' => $agencyProfile2,
            'worker1' => $worker1,
            'worker2' => $worker2,
            'visitor1Bank' => $visitor1Bank,
            'visitor2Bank' => $visitor2Bank,
            'agency1Bank' => $agency1Bank,
            'agency2Bank' => $agency2Bank,
            'categoryArt' => $categoryArt,
            'categoryBaby' => $categoryBaby,
            'locationJakarta' => $locationJakarta,
            'admin' => $admin,
        ];

        foreach ($required as $key => $model) {
            if (!$model) {
                $this->command->error("Required model {$key} not found. Please run basic seeders first.");
                return;
            }
        }

        // =====================================================
        // SKENARIO 1: Order selesai tanpa dispute
        // =====================================================
        $order1 = Order::firstOrCreate(
            ['code' => 'ORD-2401-001'],
            [
                'visitor_user_id' => $visitor1->id,
                'agency_id' => $agencyProfile1->id,
                'worker_id' => $worker1->id,
                'category_id' => $categoryArt->id,
                'status' => OrderStatus::COMPLETED,
                'subtotal_idr' => 7_500_000,
                'platform_fee_idr' => 0,
                'total_idr' => 7_500_000,
                'start_date' => now()->subMonths(2)->startOfMonth(),
                'end_date' => now()->subMonths(1)->endOfMonth(),
                'notes' => 'Pekerjaan rutin ART full-time',
                'completed_at' => now()->subMonths(1),
            ]
        );
        $this->command->info(
            $order1->wasRecentlyCreated
                ? 'Created order ORD-2401-001 (ID ' . $order1->id . ')'
                : 'ORD-2401-001 already exists, reusing order ID ' . $order1->id
        );

        Contract::firstOrCreate(
            ['order_id' => $order1->id],
            [
                'contract_no' => 'CTR-2401-001',
                'start_date' => now()->subMonths(2)->startOfMonth(),
                'end_date' => now()->subMonths(1)->endOfMonth(),
                'job_scope' => 'ART full-time, 6 hari/minggu, 8 jam/hari',
                'location_address' => 'Jakarta Pusat',
                'terms_conditions' => 'Benefit: mess + makan. Diperbolehkan membawa anak kecil jika ada.',
                'visitor_signed' => true,
                'visitor_signed_at' => now()->subMonths(2)->addDay(),
                'agency_signed' => true,
                'agency_signed_at' => now()->subMonths(2)->addDay(),
                'metadata' => json_encode(['special_note' => 'Diperbolehkan membawa anak kecil jika ada']),
            ]
        );

        $payment1 = Payment::firstOrCreate(
            ['order_id' => $order1->id],
            [
                'midtrans_order_id' => 'MID-ORD-' . $order1->id,
                'transaction_id' => 'TXN-' . Str::random(12),
                'status' => PaymentStatus::SETTLEMENT,
                'amount_idr' => 7_500_000,
                'payment_method' => 'credit_card',
                'request_payload' => json_encode([
                    'transaction_details' => [
                        'order_id' => $order1->code,
                        'gross_amount' => 7_500_000,
                    ],
                    'customer_details' => [
                        'email' => $visitor1->email,
                        'first_name' => $visitor1->name,
                    ],
                ]),
                'last_callback_payload' => json_encode([
                    'transaction_status' => 'settlement',
                    'transaction_id' => 'TXN-' . Str::random(12),
                    'gross_amount' => '7500000.00',
                    'payment_type' => 'credit_card',
                    'status_message' => 'Success, transaction found',
                ]),
                'settled_at' => now()->subMonths(2)->addDays(1),
            ]
        );

        PaymentAttempt::firstOrCreate(
            ['midtrans_order_id' => $payment1->midtrans_order_id],
            [
                'order_id' => $order1->id,
                'transaction_id' => $payment1->transaction_id,
                'amount_idr' => 7_500_000,
                'status' => PaymentStatus::PENDING,
                'raw_payload' => json_encode(['status' => 'pending', 'redirect_url' => 'https://app.midtrans.com/snap/v2/vtweb/...']),
                'callback_received_at' => now()->subMonths(2),
                'processed_at' => now()->subMonths(2),
            ]
        );

        PaymentAttempt::firstOrCreate(
            ['midtrans_order_id' => $payment1->midtrans_order_id . '-settlement'],
            [
                'order_id' => $order1->id,
                'transaction_id' => 'TXN-' . Str::random(12) . '-SETTLE',
                'amount_idr' => 7_500_000,
                'status' => PaymentStatus::SETTLEMENT,
                'raw_payload' => $payment1->last_callback_payload,
                'callback_received_at' => now()->subMonths(2)->addDay(),
                'processed_at' => now()->subMonths(2)->addDay(),
            ]
        );

        Payout::firstOrCreate(
            ['order_id' => $order1->id],
            [
                'agency_id' => $agencyProfile1->id,
                'bank_account_id' => $agency1Bank->id,
                'amount_idr' => 6_500_000,
                'status' => 'paid',
                'paid_at' => now()->subMonths(1)->addDays(2),
                'notes' => 'Full payout for completed order',
            ]
        );

        // =====================================================
        // SKENARIO 2: Order berjalan dengan dispute & refund parsial
        // =====================================================
        $order2 = Order::firstOrCreate(
            ['code' => 'ORD-2402-005'],
            [
                'visitor_user_id' => $visitor1->id,
                'agency_id' => $agencyProfile1->id,
                'worker_id' => $worker1->id,
                'category_id' => $categoryBaby->id,
                'status' => OrderStatus::DISPUTED,
                'subtotal_idr' => 9_000_000,
                'platform_fee_idr' => 0,
                'total_idr' => 9_000_000,
                'start_date' => now()->subMonth()->startOfMonth()->addDays(14),
                'end_date' => now()->addMonths(3)->startOfMonth()->addDays(14),
                'notes' => 'Baby sitter untuk bayi 6 bulan',
            ]
        );
        $this->command->info(
            $order2->wasRecentlyCreated
                ? 'Created order ORD-2402-005 (ID ' . $order2->id . ')'
                : 'ORD-2402-005 already exists, reusing order ID ' . $order2->id
        );

        Contract::firstOrCreate(
            ['order_id' => $order2->id],
            [
                'contract_no' => 'CTR-2402-005',
                'start_date' => now()->subMonth()->startOfMonth()->addDays(14),
                'end_date' => now()->addMonths(3)->startOfMonth()->addDays(14),
                'job_scope' => 'Baby sitter, 5 hari/minggu, 7 jam/hari. Butuh skill merawat bayi.',
                'location_address' => 'Jakarta Pusat',
                'terms_conditions' => 'Benefit: mess + makan. Khusus bayi 6 bulan.',
                'visitor_signed' => true,
                'visitor_signed_at' => now()->subMonth()->addDay(),
                'agency_signed' => true,
                'agency_signed_at' => now()->subMonth()->addDay(),
                'metadata' => json_encode(['special_note' => 'butuh skill merawat bayi']),
            ]
        );

        $payment2 = Payment::firstOrCreate(
            ['order_id' => $order2->id],
            [
                'midtrans_order_id' => 'MID-ORD-' . $order2->id,
                'transaction_id' => 'TXN-' . Str::random(12),
                'status' => PaymentStatus::SETTLEMENT,
                'amount_idr' => 9_000_000,
                'payment_method' => 'credit_card',
                'request_payload' => json_encode([
                    'transaction_details' => [
                        'order_id' => $order2->code,
                        'gross_amount' => 9_000_000,
                    ],
                    'customer_details' => [
                        'email' => $visitor1->email,
                        'first_name' => $visitor1->name,
                    ],
                ]),
                'last_callback_payload' => json_encode([
                    'transaction_status' => 'settlement',
                    'transaction_id' => 'TXN-' . Str::random(12),
                    'gross_amount' => '9000000.00',
                    'payment_type' => 'credit_card',
                    'status_message' => 'Success, transaction found',
                ]),
                'settled_at' => now()->subWeeks(3),
            ]
        );

        PaymentAttempt::firstOrCreate(
            ['midtrans_order_id' => $payment2->midtrans_order_id],
            [
                'order_id' => $order2->id,
                'transaction_id' => $payment2->transaction_id,
                'amount_idr' => 9_000_000,
                'status' => PaymentStatus::PENDING,
                'raw_payload' => json_encode(['status' => 'pending']),
                'callback_received_at' => now()->subWeeks(4),
                'processed_at' => now()->subWeeks(4),
            ]
        );

        PaymentAttempt::firstOrCreate(
            ['midtrans_order_id' => $payment2->midtrans_order_id . '-settlement'],
            [
                'order_id' => $order2->id,
                'transaction_id' => 'TXN-' . Str::random(12) . '-SETTLE',
                'amount_idr' => 9_000_000,
                'status' => PaymentStatus::SETTLEMENT,
                'raw_payload' => $payment2->last_callback_payload,
                'callback_received_at' => now()->subWeeks(3),
                'processed_at' => now()->subWeeks(3),
            ]
        );

        if (!Dispute::where('order_id', $order2->id)->exists()) {
            try {
                $dispute2 = Dispute::create([
                    'order_id' => $order2->id,
                    'opened_by_user_id' => $visitor1->id,
                    'status' => DisputeStatus::RESOLVED,
                    'complaint' => 'Kualitas pekerjaan buruk. Pekerjaan tidak sesuai dengan yang diharapkan.',
                    'decision' => 'partial_refund',
                    'refund_amount_idr' => 3_000_000,
                    'release_amount_idr' => 6_000_000,
                    'resolution_note' => 'Refund parsial diberikan, sisanya dibayarkan ke agency.',
                    'resolved_by_user_id' => $admin->id,
                    'resolved_at' => now()->subWeeks(2),
                ]);

                DisputeEvidence::firstOrCreate(
                    [
                        'dispute_id' => $dispute2->id,
                        'file_path' => 'evidence/dispute_' . $dispute2->id . '_chat.png',
                    ],
                    [
                        'submitted_by_type' => User::class,
                        'submitted_by_id' => $visitor1->id,
                        'description' => 'Screenshot chat dengan agency',
                        'created_at' => now()->subWeeks(3),
                    ]
                );

                $this->command->info('Dispute created for order ' . $order2->id);
            } catch (\Exception $e) {
                $this->command->error('Error creating dispute for order 2: ' . $e->getMessage());
            }
        } else {
            $this->command->info('Dispute already exists for order ' . $order2->id);
        }

        if (!Refund::where('order_id', $order2->id)->exists()) {
            try {
                Refund::create([
                    'order_id' => $order2->id,
                    'payee_type' => User::class,
                    'payee_id' => $visitor1->id,
                    'bank_account_id' => $visitor1Bank->id,
                    'amount_idr' => 3_000_000,
                    'status' => 'paid',
                    'reason' => 'Dispute resolved - partial refund',
                    'paid_at' => now()->subWeeks(2)->addDays(2),
                    'notes' => 'Partial refund due to dispute resolution',
                ]);
                $this->command->info('Refund created for order ' . $order2->id);
            } catch (\Exception $e) {
                $this->command->error('Error creating refund for order 2: ' . $e->getMessage());
            }
        } else {
            $this->command->info('Refund already exists for order ' . $order2->id);
        }

        Payout::firstOrCreate(
            ['order_id' => $order2->id],
            [
                'agency_id' => $agencyProfile1->id,
                'bank_account_id' => $agency1Bank->id,
                'amount_idr' => 6_000_000,
                'status' => 'paid',
                'paid_at' => now()->subWeeks(2)->addDays(3),
                'notes' => 'Payout setelah dispute (partial)',
            ]
        );

        // =====================================================
        // SKENARIO 3: Payment gagal → Refund full, tanpa payout
        // =====================================================
        $order3 = Order::firstOrCreate(
            ['code' => 'ORD-2403-010'],
            [
                'visitor_user_id' => $visitor2->id,
                'agency_id' => $agencyProfile2->id,
                'worker_id' => $worker2->id,
                'category_id' => $categoryArt->id,
                'status' => 'canceled',
                'subtotal_idr' => 6_000_000,
                'platform_fee_idr' => 0,
                'total_idr' => 6_000_000,
                'start_date' => now()->subWeeks(6),
                'end_date' => now()->subWeeks(2),
                'notes' => 'Order dibatalkan karena gagal pembayaran',
            ]
        );
        $this->command->info(
            $order3->wasRecentlyCreated
                ? 'Created order ORD-2403-010 (ID ' . $order3->id . ')'
                : 'ORD-2403-010 already exists, reusing order ID ' . $order3->id
        );

        $payment3 = Payment::firstOrCreate(
            ['order_id' => $order3->id],
            [
                'midtrans_order_id' => 'MID-ORD-' . $order3->id,
                'transaction_id' => 'TXN-' . Str::random(12),
                'status' => PaymentStatus::DENY,
                'amount_idr' => 6_000_000,
                'payment_method' => 'credit_card',
                'request_payload' => json_encode([
                    'transaction_details' => [
                        'order_id' => $order3->code,
                        'gross_amount' => 6_000_000,
                    ],
                    'customer_details' => [
                        'email' => $visitor2->email,
                        'first_name' => $visitor2->name,
                    ],
                ]),
                'last_callback_payload' => json_encode([
                    'transaction_status' => 'deny',
                    'transaction_id' => 'TXN-' . Str::random(12),
                    'gross_amount' => '6000000.00',
                    'payment_type' => 'credit_card',
                    'status_message' => 'Transaction denied',
                    'fraud_status' => 'accept',
                ]),
                'settled_at' => null,
            ]
        );

        PaymentAttempt::firstOrCreate(
            ['midtrans_order_id' => $payment3->midtrans_order_id],
            [
                'order_id' => $order3->id,
                'transaction_id' => $payment3->transaction_id,
                'amount_idr' => 6_000_000,
                'status' => PaymentStatus::PENDING,
                'raw_payload' => json_encode(['status' => 'pending', 'redirect_url' => 'https://app.midtrans.com/snap/v2/vtweb/...']),
                'callback_received_at' => now()->subWeeks(6),
                'processed_at' => now()->subWeeks(6),
            ]
        );

        PaymentAttempt::firstOrCreate(
            ['midtrans_order_id' => $payment3->midtrans_order_id . '-deny'],
            [
                'order_id' => $order3->id,
                'transaction_id' => 'TXN-' . Str::random(12) . '-DENY',
                'amount_idr' => 6_000_000,
                'status' => PaymentStatus::DENY,
                'raw_payload' => $payment3->last_callback_payload,
                'callback_received_at' => now()->subWeeks(6)->addMinutes(30),
                'processed_at' => now()->subWeeks(6)->addMinutes(30),
            ]
        );

        if (!Refund::where('order_id', $order3->id)->exists()) {
            try {
                Refund::create([
                    'order_id' => $order3->id,
                    'payee_type' => User::class,
                    'payee_id' => $visitor2->id,
                    'bank_account_id' => $visitor2Bank->id,
                    'amount_idr' => 6_000_000,
                    'status' => 'paid',
                    'reason' => 'Payment denied - auto refund',
                    'paid_at' => now()->subWeeks(6)->addHour(),
                    'notes' => 'System auto-refund due to payment denial',
                ]);
                $this->command->info('Refund created for order ' . $order3->id);
            } catch (\Exception $e) {
                $this->command->error('Error creating refund for order 3: ' . $e->getMessage());
            }
        } else {
            $this->command->info('Refund already exists for order ' . $order3->id);
        }

        $this->createDummyDisputesForAdmin();

        $this->createDummyPayoutsForAdmin();

        $this->createDummyRefundsForAdmin();

        $this->command->info('✓ Testing scenarios seeded: Orders, Contracts, Payments, Disputes, Refunds, Payouts');
    }

    private function createDummyDisputesForAdmin()
    {
        $orders = Order::whereHas('contract')->limit(3)->get();
        
        foreach ($orders as $index => $order) {
            $dispute = Dispute::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'opened_by_user_id' => $order->visitor_user_id,
                    'complaint' => 'Dummy complaint for testing admin panel #' . ($index + 1),
                    'status' => $index % 2 == 0 ? 'open' : 'investigating',
                    'decision' => null,
                    'refund_amount_idr' => 0,
                    'release_amount_idr' => 0,
                    'resolution_note' => null,
                    'resolved_by_user_id' => null,
                    'resolved_at' => null,
                ]
            );

            if ($dispute->wasRecentlyCreated) {
                DisputeEvidence::firstOrCreate(
                    ['dispute_id' => $dispute->id],
                    [
                        'submitted_by_type' => 'App\Models\User',
                        'submitted_by_id' => $order->visitor_user_id,
                        'file_path' => 'dummy/evidence_' . ($index + 1) . '.pdf',
                        'description' => 'Dummy evidence document for dispute #' . ($index + 1),
                    ]
                );
            }
        }

        $this->command->info('✓ Dummy disputes created for admin panel testing');
    }

    private function createDummyPayoutsForAdmin()
    {
        // Get some agencies to create payouts for
        $agencies = \App\Models\Agency::limit(3)->get();
        
        // Get some orders that don't have payouts yet
        $orders = Order::whereDoesntHave('payout')->whereHas('contract')->limit(3)->get();
        
        foreach ($agencies as $index => $agency) {
            // Skip if no more orders available
            if (!isset($orders[$index])) {
                continue;
            }
            
            $order = $orders[$index];
            
            // Get or create a bank account for the agency
            $bankAccount = BankAccount::firstOrCreate(
                ['owner_type' => 'App\Models\Agency', 'owner_id' => $agency->id],
                [
                    'bank_code' => 'BCA',
                    'bank_name' => 'Bank Agency ' . ($index + 1),
                    'account_no' => '987654321' . ($index + 1),
                    'account_name' => $agency->name,
                    'verified_status' => 'verified',
                    'is_primary' => true,
                ]
            );

            Payout::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'agency_id' => $agency->id,
                    'bank_account_id' => $bankAccount->id,
                    'amount_idr' => [2500000, 2750000, 3000000][$index],
                    'status' => 'queued',
                    'proof_file_path' => null,
                    'paid_at' => null,
                    'notes' => 'Dummy queued payout for testing admin panel #' . ($index + 1),
                ]
            );
        }

        $this->command->info('✓ Dummy payouts created for admin panel testing');
    }

    private function createDummyRefundsForAdmin()
    {
        // Get some existing orders to create refunds for
        $orders = Order::whereHas('contract')->limit(3)->get();
        
        foreach ($orders as $index => $order) {
            // Get or create a bank account for the visitor
            $bankAccount = BankAccount::firstOrCreate(
                ['owner_type' => 'App\Models\User', 'owner_id' => $order->visitor_user_id],
                [
                    'bank_code' => 'BCA',
                    'bank_name' => 'Bank Dummy ' . ($index + 1),
                    'account_no' => '123456789' . ($index + 1),
                    'account_name' => $order->visitor->name ?? 'User ' . $order->visitor_user_id,
                    'verified_status' => 'verified',
                    'is_primary' => true,
                ]
            );

            Refund::firstOrCreate(
                ['order_id' => $order->id, 'status' => 'queued'],
                [
                    'payee_type' => 'App\Models\User',
                    'payee_id' => $order->visitor_user_id,
                    'bank_account_id' => $bankAccount->id,
                    'amount_idr' => [2500000, 2750000, 3000000][$index],                    'status' => 'queued',
                    'reason' => 'Dummy refund for testing admin panel #' . ($index + 1),
                    'proof_file_path' => null,
                    'paid_at' => null,
                    'transfer_ref' => null,
                    'notes' => 'Dummy queued refund for testing admin panel #' . ($index + 1),
                    'verified_by_admin_user_id' => null,
                    'verified_at' => null,
                    'admin_note' => null,
                ]
            );
        }

        $this->command->info('✓ Dummy refunds created for admin panel testing');
    }
}
