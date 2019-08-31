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
     * @ORM\OneToOne(targetEntity="App\Entity\PaymentProfil", cascade={"persist", "remove"})
     */
    private $paymentProfil;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCaptured;

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

    public function getPaymentProfil(): ?PaymentProfil
    {
        return $this->paymentProfil;
    }

    public function setPaymentProfil(?PaymentProfil $paymentProfil): self
    {
        $this->paymentProfil = $paymentProfil;

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
}
