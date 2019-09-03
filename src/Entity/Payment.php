<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uniqKey;



    /**
     * @ORM\Column(type="boolean")
     */
    private $isCaptured;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $method;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentProfil", inversedBy="payments")
     */
    private $payment_profil;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniqKey(): ?string
    {
        return $this->uniqKey;
    }

    public function setUniqKey(string $uniqKey): self
    {
        $this->uniqKey = $uniqKey;

        return $this;
    }

    public function getIsCaptured(): ?bool
    {
        return $this->isCaptured;
    }

    public function setIsCaptured(bool $isCaptured): self
    {
        $this->isCaptured = $isCaptured;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getPaymentProfil(): ?PaymentProfil
    {
        return $this->payment_profil;
    }

    public function setPaymentProfil(?PaymentProfil $payment_profil): self
    {
        $this->payment_profil = $payment_profil;

        return $this;
    }
}
