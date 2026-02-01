<?php

namespace App\Payment\Service;

use App\Payment\Entity\Email;
use App\Product\Entity\Product;
use App\Shared\Domain\Service\Template\TemplateManager;
use App\Shared\Domain\Service\Template\TemplatePath;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Twig\Environment;


class ProductSender
{
    private MailerInterface $mailer;
    private TemplatePath $templatePath;
    private Environment $twig;
    private LoggerInterface $logger;
    public function __construct(MailerInterface $mailer, TemplatePath $templatePath, Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->templatePath = $templatePath;
        $this->twig = $twig;
        $this->logger = $logger;
    }
    public function send(Email $email, Product $product): void
    {
        $message = new \Symfony\Component\Mime\Email();
        $message->subject($product->getName());
        $message->to($email->getValue());
        $message->html(
            $this->twig->render('mail/template.html.twig')
        );
        $message->addPart(
            new DataPart(
                new File(
                    (new TemplateManager(
                        $this->templatePath,
                        $product->getFile()
                    ))
                        ->getTemplate()
                )
            )
        );
        try{
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Failed to send mail: ', ['error' => $e->getMessage()]);
            throw new TransportException($e->getMessage());
        }

    }
}