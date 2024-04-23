<?php

namespace Blackjack;

/**
 * カードのスートを表すクラス
 */
class CardSuit
{
    /** 有効なスート */
    private const VALID_SUITS = ['♠', '♥', '♦', '♣'];

    /**
     * コンストラクタ
     *
     * @param string $suit スート
     */
    public function __construct(
        private readonly string $suit
    ) {
        if (!in_array($suit, self::VALID_SUITS)) {
            throw new \InvalidArgumentException("Invalid suit: {$suit}");
        }
    }

    /**
     * スートを取得する
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->suit;
    }
}
