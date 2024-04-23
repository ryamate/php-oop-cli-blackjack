<?php

namespace Blackjack;

/**
 * カードの数字を表すクラス
 */
class CardNumber
{
    /** 有効なカードの数字 */
    private const VALID_NUMBERS = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

    /**
     * コンストラクタ
     *
     * @param string $number カードの数字
     */
    public function __construct(
        private readonly string $number
    ) {
        if (!in_array($number, self::VALID_NUMBERS)) {
            throw new \InvalidArgumentException("Invalid card number: {$number}");
        }
    }

    /**
     * カードの数字を取得する
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->number;
    }
}
