<?php

// ランダムな文字列と数字を生成するクラス
// C D S Hの文字列+2～10、A J Q Kを生成 例:D10 H2 JAなど
class CreateSuitNum
{
    const Suit = [
        1 => 'C',
        2 => 'D',
        3 => 'S',
        4 => 'H'
    ];

    public function createSuitNum(): string
    {
        $suit = rand(1,4);
        $num = rand(1,13);
        if ($num === 1) {
            $num = 'A';
        } elseif ($num === 11) {
            $num = 'J';
        } elseif ($num === 12) {
            $num = 'Q';
        } elseif ($num === 13) {
            $num = 'K';
        }
        return self::Suit[$suit] . $num;
    }
}
