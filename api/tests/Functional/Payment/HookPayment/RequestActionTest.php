<?php

namespace Test\Functional\Payment\HookPayment;

use App\Http\JsonResponse;
use App\Payment\Command\HookPayment\Handler;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            RequestFixture::class,
        ]);
    }
    public function testSuccess(): void
    {
        $this->mailer()->clear();

        $response = $this->app()->handle(self::json('POST', '/payment-service/payment-webhook',
            $this->getRequestBody()
        ));

        self::assertEquals(204, $response->getStatusCode());
        self::assertEquals('', (string)$response->getBody());

        $json = file_get_contents('http://mailer:8025/api/v2/search?query=test@app.ru&kind=to');
        $data = Json::decode($json);

        self::assertGreaterThan(0, $data['total']);
    }

    private function getRequestBody(): array
    {
        return [
            'type' => 'notification',
            'event' => 'payment.succeeded',
            'object' => [
                'id' => 'hook_test_payment_id',
                'status' => 'succeeded',
                'paid' => true,
                'amount' => [
                    'value' => '350.00',
                    'currency' => 'RUB'
                ],
                'income_amount' => [
                    'value' => '325.00',
                    'currency' => 'RUB'
                ],
                'recipient' => [
                    'account_id' => '221345',
                    'gateway_id' => '2093840'
                ],
                'created_at' => '2025-10-13T05:19:27.347Z',
                'captured_at' => '2025-10-13T05:20:00.000Z',
                'metadata' => [
                    'productId' => 'b38e76c0-ac23-4c48-85fd-975f32c8801f',
                    'cms_name' => 'yookassa_sdk_php_3',
                    'email' => 'test@app.ru'
                ]
            ]
        ];
    }
}