<?php

namespace Snowdog\Academy\Model;

class Cryptocurrency
{
    private string $id;
    private string $symbol;
    private string $name;
    private float $price;

    public function getId(): string
    {
        return $this->id;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
    public function setId($id){
        $this->id=$id;
    }
}
