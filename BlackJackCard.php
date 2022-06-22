<?php

// new BlackJackCard('C10')
class BlackJackCard
{
    const CARD_NAME = [
        'C' => 'クラブ',
        'S' => 'スペード',
        'D' => 'ダイヤ',
        'H' => 'ハート'
    ];


    const CARD_RANK = [
        'A' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        'J' => 10,
        'Q' => 10,
        'K' => 10,
    ];

    public function __construct(private string $suitNumber)
    {
    }

    public function getRank(): int
    {
        return self::CARD_RANK[substr($this->suitNumber, 1, strlen($this->suitNumber) - 1)];
    }

    public function getCardName(): string
    {
        return self::CARD_NAME[substr($this->suitNumber, 0, 1)] . 'の' . substr($this->suitNumber, 1, strlen($this->suitNumber) - 1);
    }
}
