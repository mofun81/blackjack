<?php

class Judge
{
    const MAX_SCORE = 21;

    public function __construct(private int $playerScore, private int $dealerScore)
    {
    }

    public function getWinner(): int
    {
        // 0 引き分け 1 playerの勝ち  2 dealerの勝ち
        if ($this->isBurst($this->playerScore) && $this->isBurst($this->dealerScore)) {
            return 0;
        } elseif ($this->isBurst($this->playerScore) && $this->dealerScore <= self::MAX_SCORE) {
            return 2;
        } elseif ($this->isBurst($this->dealerScore) && $this->playerScore <= self::MAX_SCORE) {
            return 1;
        } elseif ($this->playerScore > $this->dealerScore) {
            return 1;
        } elseif ($this->playerScore < $this->dealerScore) {
            return 2;
        } elseif ($this->playerScore === $this->dealerScore) {
            return 0;
        }
    }

    public function isBurst($score): bool
    {
        return $score > self::MAX_SCORE;
    }
}
