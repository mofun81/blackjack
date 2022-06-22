<?php

require_once(__DIR__ . '/BlackJackCard.php');

class BlackJackPlayer
{
    const BLACKJACK = 21;

    private array $hand;

    public function __construct(private string $name)
    {
    }


    public function setHand(BlackJackCard $card)
    {
        $this->hand[] = $card;
    }

    public function getHand(): array
    {
        return $this->hand;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScore(): int
    {
        $cardRanks = array_map(fn ($card) => $card->getRank(), $this->hand);
        $score = array_sum($cardRanks);
        // Aがふくまれている場合
        foreach ($cardRanks as $cardRank) {
            if ($cardRank === 1 && $score + 10  <= self::BLACKJACK) {
                $score += 10;
            }
        }
        return $score;
    }
}
