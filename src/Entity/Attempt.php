<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttemptRepository")
 */
class Attempt
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $api;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sms", inversedBy="attempts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $messageId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApi(): ?int
    {
        return $this->api;
    }

    public function setApi(int $api): self
    {
        $this->api = $api;

        return $this;
    }

    public function getMessageId(): ?Sms
    {
        return $this->messageId;
    }

    public function setMessageId(?Sms $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }
}
