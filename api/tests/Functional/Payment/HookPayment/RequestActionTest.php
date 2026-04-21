<?php

namespace Test\Functional\Payment\HookPayment;

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\ImageSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    private InMemoryFileSystemPath $fileSystem;
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            RequestFixture::class,
        ]);
        $this->fileSystem = InMemoryFileSystemPath::createReal();
        $container = $this->container;
        $container->set(InMemoryFileSystemPath::class, $this->fileSystem);
        $container->set(FileSystemPath::class, $this->fileSystem);
        $container->set(ImageSystemPath::class, $this->fileSystem);
    }
    public function testSuccess(): void
    {
        $this->mailer()->clear();
        $this->createFile('ot201.18.rar', 'test content');

        $response = $this->app()->handle(self::json('POST', '/v1/payments/payment-webhook',
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

    private function createFile(string $name, string $content): void
    {
        $pathToFile = $this->fileSystem->getValue(). '/b38e76c0-ac23-4c48-85fd-975f32c8801f/'. $name;
        mkdir(dirname($pathToFile), 0777, true);
        $result = file_put_contents($pathToFile, $content);
        if(!$result){
            throw new \RuntimeException('Unable to write file');
        }
    }
    public function tearDown(): void
    {
        //$this->fileSystem->clear();
    }
}