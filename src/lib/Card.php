<?php

namespace Blackjack;

/**
 * カードクラス
 */
class Card
{
    /** 各カードの点数 */
    private const CARD_SCORE = [
        'A' => 1, // Aは1点あるいは11点として、手の点数が最大となる方で数える
        '2' => 2, // 2から9までは、書かれている数の通りの点数
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10, // 10,J,Q,Kは10点
        'J' => 10,
        'Q' => 10,
        'K' => 10,
    ];

    /** 各カードのマーク */
    private array $suits = [
        'スペード',
        'ハート',
        'ダイヤ',
        'クラブ',
    ];

    /**
     * 新しくデッキを作成する
     *
     * @return array $deck デッキ
     */
    public function createNewDeck(): array
    {
        $deck = [];
        foreach ($this->suits as $suit) {
            foreach (self::CARD_SCORE as $num => $score) {
                $deck[] = [
                    'suit' => $suit,
                    'num' => $num,
                    'score' => $score,
                ];
            }
        }
        return $deck;
    }
}
