<?php

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: "email_logs")]
class EmailLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $recipient;

    #[ORM\Column(type: "string", length: 255)]
    private string $subject;

    #[ORM\Column(type: "text")]
    private string $body;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $sentAt;

    public function __construct(string $recipient, string $subject, string $body)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->body = $body;
        $this->sentAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getSentAt(): DateTimeImmutable
    {
        return $this->sentAt;
    }
}
