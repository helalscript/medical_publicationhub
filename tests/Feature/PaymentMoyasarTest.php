<?php

use App\Models\Course;
use App\Models\Service;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserCourseAccess;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('services.moyasar.key', 'pk_test_publishable_key');
    Config::set('services.moyasar.secret', 'sk_test_secret_key');
    Config::set('services.moyasar.webhook_secret', 'whsec_webhook_secret');
});

test('payment create requires authentication', function () {
    $response = $this->postJson('/api/payments', [
        'payable_type' => 'course',
        'payable_id' => 1,
        'amount' => 100,
        'currency' => 'SAR',
        'description' => 'Test Course Purchase',
        'callback_url' => 'https://example.com/callback',
        'source' => [
            'type' => 'creditcard',
            'name' => 'John Doe',
            'number' => '4111111111111111',
            'cvc' => '123',
            'month' => '12',
            'year' => '2026'
        ]
    ]);

    $response->assertStatus(401);
});

test('payment create validation works', function () {
    $user = User::factory()->create(['role' => 'researcher']);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/payments', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['payable_type', 'payable_id', 'amount', 'currency', 'description', 'callback_url', 'source']);
});

test('payment create succeeds and mocks Moyasar API call', function () {
    $user = User::factory()->create(['role' => 'researcher']);
    $course = Course::factory()->create(['price' => 150.00, 'status' => 'active']);

    Http::fake([
        'https://api.moyasar.com/v1/payments' => Http::response([
            'id' => 'pay_1234567890',
            'status' => 'initiated',
            'amount' => 15000,
            'currency' => 'SAR',
            'description' => 'Test Course Purchase',
            'source' => [
                'type' => 'creditcard',
                'company' => 'visa',
                'name' => 'John Doe',
                'number' => 'XXXX-XXXX-XXXX-1111',
                'transaction_url' => 'https://api.moyasar.com/v1/payments/pay_1234567890/3ds'
            ]
        ], 201)
    ]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/payments', [
            'payable_type' => 'course',
            'payable_id' => $course->id,
            'amount' => 150.00,
            'currency' => 'SAR',
            'description' => 'Test Course Purchase',
            'callback_url' => 'https://example.com/callback',
            'source' => [
                'type' => 'creditcard',
                'name' => 'John Doe',
                'number' => '4111111111111111',
                'cvc' => '123',
                'month' => '12',
                'year' => '2026'
            ]
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.payment.payment_gateway_id', 'pay_1234567890')
        ->assertJsonPath('data.payment.status', 'pending');

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'payable_type' => Course::class,
        'payable_id' => $course->id,
        'amount' => 150.00,
        'payment_gateway_id' => 'pay_1234567890',
        'status' => 'pending',
    ]);
});

test('payment fetch details successfully', function () {
    $user = User::factory()->create(['role' => 'researcher']);
    $payment = Payment::create([
        'user_id' => $user->id,
        'payable_type' => Course::class,
        'payable_id' => 1,
        'amount' => 150.00,
        'payment_gateway' => 'moyasar',
        'payment_gateway_id' => 'pay_1234567890',
        'status' => 'pending'
    ]);

    Http::fake([
        'https://api.moyasar.com/v1/payments/pay_1234567890' => Http::response([
            'id' => 'pay_1234567890',
            'status' => 'paid',
            'amount' => 15000,
            'currency' => 'SAR',
            'description' => 'Test Course Purchase',
            'source' => [
                'type' => 'creditcard',
                'company' => 'visa'
            ],
            'metadata' => [
                'user_id' => $user->id,
                'payable_id' => 1,
                'payable_type' => Course::class
            ]
        ], 200)
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/payments/pay_1234567890');

    $response->assertStatus(200)
        ->assertJsonPath('data.local_payment.payment_gateway_id', 'pay_1234567890')
        ->assertJsonPath('data.moyasar_payment.status', 'paid');
});

test('payment list retrieved successfully', function () {
    $user = User::factory()->create(['role' => 'researcher']);
    Payment::create([
        'user_id' => $user->id,
        'payable_type' => Course::class,
        'payable_id' => 1,
        'amount' => 150.00,
        'payment_gateway' => 'moyasar',
        'payment_gateway_id' => 'pay_1234567890',
        'status' => 'pending'
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/payments');

    $response->assertStatus(200)
        ->assertJsonPath('status', true);
});

test('payment manual verification updates local payment and grants access', function () {
    $user = User::factory()->create(['role' => 'researcher']);
    $course = Course::factory()->create(['price' => 150.00]);
    $payment = Payment::create([
        'user_id' => $user->id,
        'payable_type' => Course::class,
        'payable_id' => $course->id,
        'amount' => 150.00,
        'payment_gateway' => 'moyasar',
        'payment_gateway_id' => 'pay_1234567890',
        'status' => 'pending'
    ]);

    Http::fake([
        'https://api.moyasar.com/v1/payments/pay_1234567890' => Http::response([
            'id' => 'pay_1234567890',
            'status' => 'paid',
            'amount' => 15000,
            'currency' => 'SAR',
            'source' => [
                'type' => 'creditcard'
            ],
            'metadata' => [
                'user_id' => $user->id,
                'payable_id' => $course->id,
                'payable_type' => Course::class
            ]
        ], 200)
    ]);

    $response = $this->actingAs($user, 'api')
        ->postJson("/api/payments/pay_1234567890/verify");

    $response->assertStatus(200)
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'paid',
        'pay_id' => 'pay_1234567890'
    ]);

    $this->assertDatabaseHas('user_course_access', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'status' => 'active'
    ]);
});

test('payment webhook verification handles payment paid event successfully', function () {
    $user = User::factory()->create(['role' => 'researcher']);
    $course = Course::factory()->create(['price' => 150.00]);
    $payment = Payment::create([
        'user_id' => $user->id,
        'payable_type' => Course::class,
        'payable_id' => $course->id,
        'amount' => 150.00,
        'payment_gateway' => 'moyasar',
        'payment_gateway_id' => 'pay_1234567890',
        'status' => 'pending'
    ]);

    Http::fake([
        'https://api.moyasar.com/v1/payments/pay_1234567890' => Http::response([
            'id' => 'pay_1234567890',
            'status' => 'paid',
            'amount' => 15000,
            'currency' => 'SAR',
            'source' => [
                'type' => 'creditcard'
            ],
            'metadata' => [
                'user_id' => $user->id,
                'payable_id' => $course->id,
                'payable_type' => Course::class
            ]
        ], 200)
    ]);

    $webhookPayload = json_encode([
        'id' => 'evt_123456',
        'type' => 'payment.paid',
        'data' => [
            'id' => 'pay_1234567890',
            'status' => 'paid',
            'amount' => 15000,
            'currency' => 'SAR'
        ]
    ]);

    $signature = hash_hmac('sha256', $webhookPayload, 'whsec_webhook_secret');

    $response = $this->withHeaders([
        'x-moyasar-signature' => $signature
    ])->postJson('/api/payments/webhook/moyasar', json_decode($webhookPayload, true));

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success');

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'paid',
    ]);

    $this->assertDatabaseHas('user_course_access', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'status' => 'active'
    ]);
});

test('service payment create succeeds and mocks Moyasar API call', function () {
    $user = User::factory()->create(['role' => 'researcher']);
    $service = Service::factory()->create(['starting_price' => 200.00, 'status' => 'active']);

    Http::fake([
        'https://api.moyasar.com/v1/payments' => Http::response([
            'id' => 'pay_service_123',
            'status' => 'initiated',
            'amount' => 20000,
            'currency' => 'SAR',
            'description' => 'Test Service Purchase',
            'source' => [
                'type' => 'creditcard',
                'company' => 'visa',
                'name' => 'John Doe',
                'number' => 'XXXX-XXXX-XXXX-1111',
                'transaction_url' => 'https://api.moyasar.com/v1/payments/pay_service_123/3ds'
            ]
        ], 201)
    ]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/payments', [
            'payable_type' => 'service',
            'payable_id' => $service->id,
            'amount' => 200.00,
            'currency' => 'SAR',
            'description' => 'Test Service Purchase',
            'callback_url' => 'https://example.com/callback',
            'source' => [
                'type' => 'creditcard',
                'name' => 'John Doe',
                'number' => '4111111111111111',
                'cvc' => '123',
                'month' => '12',
                'year' => '2026'
            ]
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.payment.payment_gateway_id', 'pay_service_123')
        ->assertJsonPath('data.payment.status', 'pending');

    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'payable_type' => Service::class,
        'payable_id' => $service->id,
        'amount' => 200.00,
        'payment_gateway_id' => 'pay_service_123',
        'status' => 'pending',
    ]);
});

test('service payment manual verification updates local payment successfully', function () {
    $user = User::factory()->create(['role' => 'researcher']);
    $service = Service::factory()->create(['starting_price' => 200.00]);
    $payment = Payment::create([
        'user_id' => $user->id,
        'payable_type' => Service::class,
        'payable_id' => $service->id,
        'amount' => 200.00,
        'payment_gateway' => 'moyasar',
        'payment_gateway_id' => 'pay_service_123',
        'status' => 'pending'
    ]);

    Http::fake([
        'https://api.moyasar.com/v1/payments/pay_service_123' => Http::response([
            'id' => 'pay_service_123',
            'status' => 'paid',
            'amount' => 20000,
            'currency' => 'SAR',
            'source' => [
                'type' => 'creditcard'
            ],
            'metadata' => [
                'user_id' => $user->id,
                'payable_id' => $service->id,
                'payable_type' => Service::class
            ]
        ], 200)
    ]);

    $response = $this->actingAs($user, 'api')
        ->postJson("/api/payments/pay_service_123/verify");

    $response->assertStatus(200)
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'paid',
        'pay_id' => 'pay_service_123'
    ]);
});

test('service payment webhook verification handles payment paid event successfully', function () {
    $user = User::factory()->create(['role' => 'researcher']);
    $service = Service::factory()->create(['starting_price' => 200.00]);
    $payment = Payment::create([
        'user_id' => $user->id,
        'payable_type' => Service::class,
        'payable_id' => $service->id,
        'amount' => 200.00,
        'payment_gateway' => 'moyasar',
        'payment_gateway_id' => 'pay_service_123',
        'status' => 'pending'
    ]);

    Http::fake([
        'https://api.moyasar.com/v1/payments/pay_service_123' => Http::response([
            'id' => 'pay_service_123',
            'status' => 'paid',
            'amount' => 20000,
            'currency' => 'SAR',
            'source' => [
                'type' => 'creditcard'
            ],
            'metadata' => [
                'user_id' => $user->id,
                'payable_id' => $service->id,
                'payable_type' => Service::class
            ]
        ], 200)
    ]);

    $webhookPayload = json_encode([
        'id' => 'evt_service_123',
        'type' => 'payment.paid',
        'data' => [
            'id' => 'pay_service_123',
            'status' => 'paid',
            'amount' => 20000,
            'currency' => 'SAR'
        ]
    ]);

    $signature = hash_hmac('sha256', $webhookPayload, 'whsec_webhook_secret');

    $response = $this->withHeaders([
        'x-moyasar-signature' => $signature
    ])->postJson('/api/payments/webhook/moyasar', json_decode($webhookPayload, true));

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success');

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'paid',
    ]);
});
