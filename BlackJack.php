<?php

require_once(__DIR__ . '/BlackJackCard.php');
require_once(__DIR__ . '/BlackJackPlayer.php');
require_once(__DIR__ . '/CreateSuitNum.php');
require_once(__DIR__ . '/Judge.php');

class BlackJack
{
    const BLACKJACK = 21;

    public function isSplit($playerHands): bool
    {
        return count(array_unique(array_map(fn ($playerHand) => $playerHand->getRank(),$playerHands))) === 1;
    }

    public function drawCard(): BlackJackCard
    {
        $card = new CreateSuitNum();
        return new BlackJackCard($card->createSuitNum());
    }

    public function start()
    {
        echo 'ブラックジャックを開始します。' . PHP_EOL;
        $isSurrender = 0;
        $doneSplit = 0;
        $player = new BlackJackPlayer('あなた');
        $player2 = new BlackJackPlayer('プレイヤー2');
        $player3 = new BlackJackPlayer('プレイヤー3');
        $dealer = new BlackJackPlayer('ディーラー');

        $gamePlayers = [$player, $player2, $player3, $dealer];
        foreach ($gamePlayers as $gamePlayer) {
            $gamePlayer->setHand($this->drawCard());
            $gamePlayer->setHand($this->drawCard());
        }

        // プレイヤー
        $playerHands = $player->getHand();
        foreach ($playerHands as $playerHand) {
            echo 'あなたの引いたカードは' . $playerHand->getCardName() . 'です。' . PHP_EOL;
        }

        // あなた以外のプレイヤー達
        foreach ([$player2, $player3, $dealer] as $notPlayer) {
            $notPlayerHands = $notPlayer->getHand();
            echo $notPlayer->getName() . 'の引いたカードは' . $notPlayerHands[0]->getCardName() . 'です。' . PHP_EOL;
            echo $notPlayer->getName() .'の引いた2枚目のカードはわかりません。' . PHP_EOL;
        }

        // サレンダー判定
        echo 'サレンダーしますか？（Y/N）' . PHP_EOL;
        $stdin = trim(fgets(STDIN));
        if ($stdin === 'Y') {
            $isSurrender = 1;
            echo 'あなたはサレンダーしました。' . PHP_EOL;
        }
        // スプリット
        if ($this->isSplit($playerHands) && !$isSurrender) {
            echo 'スプリットが可能です。スプリットを行いますか？(Y/N)' . PHP_EOL;
            $stdin = trim(fgets(STDIN));
            if ($stdin === 'Y') {
                echo 'スプリットを行います。あなた（Ａ）とあなた（Ｂ）にカードを分けます' .PHP_EOL;
                $playerA = new BlackJackPlayer('あなたA');
                $playerA->setHand($playerHands[0]);
                $newCard = $this->drawCard();
                echo 'あなたAの新たに引いたカードは'. $newCard->getCardName() .'です。' . PHP_EOL;
                $playerA->setHand($newCard);

                $playerB = new BlackJackPlayer('あなたB');
                $playerB->setHand($playerHands[1]);
                $newCard = $this->drawCard();
                echo 'あなたBの新たに引いたカードは'. $newCard->getCardName() .'です。' . PHP_EOL;
                $playerB->setHand($newCard);
                $doneSplit = 1;
            }
        }
        if ($doneSplit && !$isSurrender) {
            foreach ([$playerA, $playerB] as $anotherPlayer) {
                echo $anotherPlayer->getName() . 'の現在の得点は' . $anotherPlayer->getScore() . 'です。カードを引きますか？（Y/N）' . PHP_EOL;
                $stdin = trim(fgets(STDIN));
                while ($stdin === 'Y') {
                    // カードを追加する
                    $newCard = $this->drawCard();
                    echo $anotherPlayer->getName() .'の引いたカードは'. $newCard->getCardName() .'です。' . PHP_EOL;
                    $anotherPlayer->setHand($newCard);
                    if ($anotherPlayer->getScore() > self::BLACKJACK) {
                        echo 'バーストしました。' . PHP_EOL;
                        break;
                    }
                    echo $anotherPlayer->getName() .'の現在の得点は' . $anotherPlayer->getScore() . 'です。カードを引きますか？（Y/N）' . PHP_EOL;
                    $stdin = trim(fgets(STDIN));
                }
            }
            // バーストしていない点数の大きいほうを採用
            if ($playerA->getScore() >= $playerB->getScore() && $playerA->getScore() <= self::BLACKJACK) {
                $player = $playerA;
            } elseif ($playerA->getScore() <= $playerB->getScore() && $playerB->getScore() <= self::BLACKJACK) {
                $player = $playerB;
            } elseif ($playerA->getScore() > self::BLACKJACK) {
                $player = $playerB;
            } else {
                $player = $playerA;
            }

        } elseif (!$isSurrender) {
            // ダブルダウン
            echo 'あなたの現在の得点は' . $player->getScore() . 'です。ダブルダウンしますか？（Y/N）' . PHP_EOL;
            $stdin = trim(fgets(STDIN));
            if ($stdin === 'Y') {
                echo 'ダブルダウンを実行します。一度だけカードを引きます。' . PHP_EOL;
                $newCard = $this->drawCard();
                echo 'あなたの引いたカードは'. $newCard->getCardName() .'です。' . PHP_EOL;
                $player->setHand($newCard);
                echo 'あなたの現在の得点は' . $player->getScore() . PHP_EOL;
            } else {
                echo 'ダブルダウンは行いません。' . PHP_EOL;
                echo 'あなたの現在の得点は' . $player->getScore() . 'です。カードを引きますか？（Y/N）' . PHP_EOL;
                $stdin = trim(fgets(STDIN));
                while ($stdin === 'Y') {
                    // カードを追加する
                    // カードの点数を追加する
                    $newCard = $this->drawCard();
                    echo 'あなたの引いたカードは'. $newCard->getCardName() .'です。' . PHP_EOL;
                    $player->setHand($newCard);
                    // プレイヤーの点数がself::BLACKJACKをこえたらカードの追加もやめる
                    if ($player->getScore() > self::BLACKJACK) {
                        echo 'バーストしました。' . PHP_EOL;
                        break;
                    }
                    echo 'あなたの現在の得点は' . $player->getScore() . 'です。カードを引きますか？（Y/N）' . PHP_EOL;
                    $stdin = trim(fgets(STDIN));
                }
            }
        }

        // npc
        foreach ([$player2, $player3] as $nonePlayer) {
            $nonePlayerHands = $nonePlayer->getHand();
            echo $nonePlayer->getName() . 'の引いた2枚目のカードは' . $nonePlayerHands[1]->getCardName() . 'です。' . PHP_EOL;
            echo $nonePlayer->getName() . 'の現在の得点は' . $nonePlayer->getScore() . 'です。' . PHP_EOL;
            // 点数が17を超えるまではカードをひく。
            while ($nonePlayer->getScore() < 17) {
                $newCard = $this->drawCard();
                $nonePlayer->setHand($newCard);
                echo $nonePlayer->getName() . 'の引いたカードは'. $newCard->getCardName() .'です。' . PHP_EOL;
                echo $nonePlayer->getName() . 'の現在の得点は' . $nonePlayer->getScore() . 'です。' . PHP_EOL;
            }
        }

        // ディーラー処理
        $dealerHands = $dealer->getHand();
        echo 'ディーラーの引いた2枚目のカードは' . $dealerHands[1]->getCardName() . 'です。' . PHP_EOL;
        echo 'ディーラーの現在の得点は' . $dealer->getScore() . 'です。' . PHP_EOL;
        // ディーラー点数が17を超えるまでカードをひく
        while ($dealer->getScore() < 17 && ($player->getScore() <= self::BLACKJACK || $player2->getScore() <= self::BLACKJACK || $player3->getScore() <= self::BLACKJACK)) {
            $newCard = $this->drawCard();
            $dealer->setHand($newCard);
            echo 'ディーラーの引いたカードは'. $newCard->getCardName() .'です。' . PHP_EOL;
            echo 'ディーラーの現在の得点は' . $dealer->getScore() . 'です。' . PHP_EOL;
        }


        if ($isSurrender) {
            echo 'あなたはサレンダーしています。' . PHP_EOL;
        } else {
            echo 'あなたの得点は' . $player->getScore() . 'です。' . PHP_EOL;
        }
        foreach ([$player2, $player3, $dealer] as $NotPlayer) {
            echo $NotPlayer->getName() . 'の得点は' . $NotPlayer->getScore() . 'です。' . PHP_EOL;
        }

        // 勝敗判定
        if ($isSurrender) {
            echo 'あなたはサレンダーしています。' . PHP_EOL;
        } else {
            $judge = new Judge($player->getScore(), $dealer->getScore());
            $winner = $judge->getWinner();
            if ($winner === 1) {
                echo 'あなたの勝ちです！' . PHP_EOL;
            } elseif ($winner === 2) {
                echo 'あなたの負けです。' . PHP_EOL;
            } elseif ($winner === 0) {
                echo '引き分けです！' . PHP_EOL;
            }
        }
        foreach ([$player2, $player3] as $nonePlayer) {
            $judge = new Judge($nonePlayer->getScore(), $dealer->getScore());
            $winner = $judge->getWinner();
            if ($winner === 1) {
                echo $nonePlayer->getName() .'はディーラーに勝利!' . PHP_EOL;
            } elseif ($winner === 2) {
                echo $nonePlayer->getName() .'は敗北。' . PHP_EOL;
            } elseif ($winner === 0) {
                echo $nonePlayer->getName() .'は引き分けです。' . PHP_EOL;
            }
        }
        echo 'ブラックジャックを終了します。' . PHP_EOL;
    }
}

$game = new BlackJack();
$game->start();
