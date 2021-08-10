<?php

namespace Bug2676ReturnTypeRule;

use DoctrineIntersectionTypeIsSupertypeOf\Collection;

class BankAccount
{
}

/**
 * @ORM\Table
 * @ORM\Entity
 */
class Wallet
{
    /**
     * @var Collection<BankAccount>
     *
     * @ORM\OneToMany(targetEntity=BankAccount::class, mappedBy="wallet")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $bankAccountList;

    /**
     * @return Collection<BankAccount>
     */
    public function getBankAccountList(): Collection
    {
        return $this->bankAccountList;
    }
}
