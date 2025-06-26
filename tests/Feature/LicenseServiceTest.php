<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\User;
use App\Models\BonusCredit;
use App\Services\LicenseService;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LicenseServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $licenseService;
    protected $referralService;

    public function setUp(): void
    {
        parent::setUp();
        $this->licenseService = app(LicenseService::class);
        $this->referralService = app(ReferralService::class);
    }

    /**
     * Test license creation with the service
     */
    public function testCreateLicense()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a license
        $license = $this->licenseService->createLicense(
            $user,
            'test_payment_123',
            500.00,
            'CHF',
            'annual',
            false,
            0,
            [
                'payment_method' => 'card',
                'customer_email' => $user->email,
                'session_id' => 'test_session_123',
            ]
        );

        // Assert license was created correctly
        $this->assertInstanceOf(License::class, $license);
        $this->assertEquals($user->id, $license->user_id);
        $this->assertEquals('test_payment_123', $license->payment_id);
        $this->assertEquals(500.00, $license->amount);
        $this->assertEquals('CHF', $license->currency);
        $this->assertEquals('annual', $license->type);
        $this->assertEquals('active', $license->status);
        $this->assertNotNull($license->license_key);
        $this->assertNotNull($license->expires_at);
    }

    /**
     * Test license creation with discount
     */
    public function testCreateLicenseWithDiscount()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a license with discount
        $license = $this->licenseService->createLicense(
            $user,
            'test_payment_456',
            450.00,
            'CHF',
            'annual',
            true,
            50.00,
            [
                'payment_method' => 'card',
                'customer_email' => $user->email,
                'session_id' => 'test_session_456',
            ]
        );

        // Assert license was created correctly with discount
        $this->assertInstanceOf(License::class, $license);
        $this->assertEquals($user->id, $license->user_id);
        $this->assertEquals('test_payment_456', $license->payment_id);
        $this->assertEquals(450.00, $license->amount);
        $this->assertEquals('CHF', $license->currency);
        $this->assertEquals('annual', $license->type);
        $this->assertEquals('active', $license->status);
        $this->assertEquals(true, $license->discount_applied);
        $this->assertEquals(50.00, $license->discount_amount);
    }

    /**
     * Test referral bonus crediting
     */
    public function testReferralBonusCrediting()
    {
        // Create referrer and referred users
        $referrer = User::factory()->create();
        $referredUser = User::factory()->create();

        // Create a pending bonus credit
        $bonusCredit = BonusCredit::create([
            'user_id' => $referrer->id,
            'referred_user_id' => $referredUser->id,
            'amount' => 0, // Will be set to 100 when credited
            'status' => 'pending',
            'credited' => false,
            'referral_code_used' => $referrer->referral_code ?? 'test_code',
            'type' => 'referral'
        ]);

        // Credit the bonus
        $result = $this->referralService->creditReferralBonus(
            $referredUser,
            500.00,
            'CHF',
            'test_payment_789'
        );

        // Assert bonus was credited correctly
        $this->assertNotNull($result);
        $this->assertEquals($bonusCredit->id, $result['bonus_credit_id']);
        $this->assertEquals($referrer->id, $result['referrer_id']);
        $this->assertEquals(100.00, $result['amount']);

        // Reload the bonus credit from database
        $updatedBonus = BonusCredit::find($bonusCredit->id);
        $this->assertEquals('credited', $updatedBonus->status);
        $this->assertEquals(true, $updatedBonus->credited);
        $this->assertEquals(100.00, $updatedBonus->amount);
        $this->assertEquals('test_payment_789', $updatedBonus->payment_id);
    }

    /**
     * Test duplicate payment detection
     */
    public function testDuplicatePaymentDetection()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create first license
        $license1 = $this->licenseService->createLicense(
            $user,
            'duplicate_payment_id',
            500.00,
            'CHF',
            'annual',
            false,
            0,
            [
                'payment_method' => 'card',
                'customer_email' => $user->email,
                'session_id' => 'test_session_dup',
            ]
        );

        // Try to create another license with the same payment ID
        $license2 = $this->licenseService->createLicense(
            $user,
            'duplicate_payment_id',
            500.00,
            'CHF',
            'annual',
            false,
            0,
            [
                'payment_method' => 'card',
                'customer_email' => $user->email,
                'session_id' => 'test_session_dup2',
            ]
        );

        // Assert that the second license is the same as the first (duplicate detected)
        $this->assertEquals($license1->id, $license2->id);
        $this->assertEquals($license1->license_key, $license2->license_key);
        
        // Assert that only one license was created
        $this->assertEquals(1, License::where('payment_id', 'duplicate_payment_id')->count());
    }
}
