<?php

namespace Blackjack;

/**
 * カードクラス
 *
 * ブラックジャックのカードを表現する。
 */
class Card
{
    /**
     * コンストラクタ
     *
     * @param CardSuit $suit カードのスート
     * @param CardNumber $number カードの数字('2', '3', ..., 'A')
     */
    public function __construct(
        private readonly CardSuit $suit,
        private readonly CardNumber $number,
    ) {
    }

    /**
     * カードのスートを取得する
     *
     * @return CardSuit カードのスート
     */
    public function getSuit(): CardSuit
    {
        return $this->suit;
    }

    /**
     * カードの数字を取得する
     *
     * @return CardNumber カードの数字
     */
    public function getNumber(): CardNumber
    {
        return $this->number;
    }

    /**
     * カードの点数を取得する
     *
     * @return int カードの点数
     */
    public function getScore(): int
    {
        return $this->calculateScore($this->number);
    }

    /**
     * カードの得点を計算する
     *
     * @param CardNumber $number カードの数字
     * @return int カードの得点
     */
    private function calculateScore(CardNumber $number): int
    {
        $numberValue = $number->getValue();
        return match ($numberValue) {
            'A' => 11,
            'K', 'Q', 'J' => 10,
            default => (int)$numberValue,
        };
    }
}
