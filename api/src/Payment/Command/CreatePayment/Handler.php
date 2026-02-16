<?php

namespace App\Payment\Command\CreatePayment;

use App\Flusher;
use App\Payment\Entity\DTO\MakePaymentDTO;
use App\Payment\Entity\Email;
use App\Payment\Entity\Payment;
use App\Payment\Entity\PaymentRepository;
use App\Payment\Entity\PaymentStatus;
use App\Payment\Entity\Price;
use App\Payment\Entity\Token;
use App\Shared\Domain\Query\ProductQueryInterface;
use App\Shared\Domain\Service\Payment\PaymentException;
use App\Shared\Domain\Service\Payment\Provider\YookassaProvider;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\Id;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class Handler
{

    public function __construct(
        private readonly Flusher $flusher,
        private readonly ProductQueryInterface $productQuery,
        private readonly YookassaProvider $yookassaProvider,
        private readonly PaymentRepository $payments,
        private readonly LoggerInterface $logger
    )
    {}
    public function handle(Command $command): Response
    {
        $email = new Email($command->email);
        $product = $this->productQuery->getProduct($command->productId);
        $returnToken = new Token(Id::generate(), new DateTimeImmutable('+ 1 hour'));
        $payment = new Payment(
            new Id(Uuid::uuid4()->toString()),
            $email,
            $product->id,
            new Price($product->amount, new Currency('RUB')),
            new DateTimeImmutable(),
            $returnToken
        );
        try {
            $paymentInfo = $this->yookassaProvider->initiatePayment(
                new MakePaymentDTO(
                    $payment->getPrice()->getValue(),
                    $payment->getPrice()->getCurrency()->getValue(),
                    $product->cipher,
                    $payment->getReturnToken()->getValue(),
                    ['email' => $email->getValue(), 'productId' => $product->id],
                    $email->getValue(),
                )
            );
            $payment->setExternalId($paymentInfo->paymentId);
        }catch (PaymentException $e){
            $this->logger->error('Failed to create payment: ', ['error' => $e->getMessage()]);
            $payment->updateStatus(PaymentStatus::cancelled());

            $this->payments->create($payment);

            $this->flusher->flush();
            throw $e;
        }

        $this->payments->create($payment);
        $this->flusher->flush();

        return new Response(
            $payment->getPrice()->getValue(),
            $payment->getPrice()->getCurrency()->getValue(),
            $payment->getStatus()->getValue(),
            $paymentInfo->redirectUrl,
        );

    }
}