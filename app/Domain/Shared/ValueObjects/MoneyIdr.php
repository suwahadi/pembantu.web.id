<?php

namespace App\Domain\Shared\ValueObjects;

class MoneyIdr
{
    private int $amount;

    public function __construct(int $amount)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Jumlah tidak boleh negatif');
        }
        $this->amount = $amount;
    }

    public static function fromFloat(float $amount): self
    {
        return new self((int) round($amount));
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function formatted(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function add(MoneyIdr $other): self
    {
        return new self($this->amount + $other->amount());
    }

    public function subtract(MoneyIdr $other): self
    {
        return new self($this->amount - $other->amount());
    }

    public function multiply(int|float $factor): self
    {
        return new self((int) round($this->amount * $factor));
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    public function isGreaterThan(MoneyIdr $other): bool
    {
        return $this->amount > $other->amount();
    }

    public function isGreaterThanOrEqual(MoneyIdr $other): bool
    {
        return $this->amount >= $other->amount();
    }

    public function isLessThan(MoneyIdr $other): bool
    {
        return $this->amount < $other->amount();
    }

    public function isLessThanOrEqual(MoneyIdr $other): bool
    {
        return $this->amount <= $other->amount();
    }

    public function equals(MoneyIdr $other): bool
    {
        return $this->amount === $other->amount();
    }
}
