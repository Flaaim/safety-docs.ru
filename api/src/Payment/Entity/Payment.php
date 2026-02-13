<?php

namespace App\Payment\Entity;

use App\Product\Entity\Price;
use App\Shared\Domain\ValueObject\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'payments')]
class Payment
{
    #[ORM\Id]
    #[ORM\Column(type:'id', length: 255)]
    private Id $id;
    #[ORM\Column(type:'string', length: 255, nullable: true)]
    private ?string $externalId = null;
    #[ORM\Column(type:'status')]
    private PaymentStatus $status;
    #[ORM\Column(type: 'email')]
    private Email $email;
    #[ORM\Column(type:'string', length: 255)]
    private string $productId;
    #[ORM\Column(type:'price')]
    private Price $price;
    #[ORM\Embedded(class: Token::class)]
    private Token $returnToken;
    #[ORM\Column(type:'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    public function __construct(Id $id, Email $email, string $productId, Price $price, \DateTimeImmutable $createdAt, Token $returnToken)
    {
        $this->id = $id;
        $this->status = PaymentStatus::pending();
        $this->email = $email;
        $this->productId = $productId;
        $this->price = $price;
        $this->createdAt = $createdAt;
        $this->returnToken = $returnToken;
    }
    public function getId(): Id
    {
        return $this->id;
    }
    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }
    public function getEmail(): Email
    {
        return $this->email;
    }
    public function getProductId(): string
    {
        return $this->productId;
    }
    public function getPrice(): Price
    {
        return $this->price;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }
    public function getExternalId(): string
    {
        return $this->externalId;
    }
    public function getReturnToken(): Token
    {
        return $this->returnToken;
    }
    public function setStatus(PaymentStatus $newStatus): void
    {
        if($this->status->getValue() === $newStatus->getValue()) {
            throw new \DomainException('Status already set');
        }
        $this->status = $newStatus;
    }
    public function validateToken(string $token, \DateTimeImmutable $date): void
    {
        $this->returnToken->validate($token, $date);
    }
    public function updateStatus(PaymentStatus $newStatus): void
    {
        $this->setStatus($newStatus);
    }
}