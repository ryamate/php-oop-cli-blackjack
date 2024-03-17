<?php

namespace Blackjack;

/**
 * カードクラス
 * 
 * このクラスはブラックジャックのカードを表現します。
 */
class Card
{
    /** 
     * @var array<string,int> 各カードの点数 
     * 
     * 2から9までは、書かれている数の通りの点数
     * 10,J,Q,Kは10点
     * Aは1点あるいは11点として、手の点数が最大となる方で数える（初期値 11 にする）
     */
    private const CARD_SCORE = [
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
        'A' => 11,
    ];

    /** 
     * @var array<int,string> 各カードのマーク 
     * '♠', '♥', '♦', '♣'の4種類のマークが存在します。
     */
    private const SUITS = [
        '♠',
        '♥',
        '♦',
        '♣',
    ];

    /**
     * 新しくデッキを作成する
     *
     * @return array<int,array<string,int|string>> デッキ
     * 
     * 各カードは'suit'（マーク）, 'num'（数字）, 'score'（点数）の3つの属性を持つ配列として表現されます。
     */
    public function createNewDeck(): array
    {
        $deck = [];
        foreach (self::SUITS as $suit) {
            foreach (self::CARD_SCORE as $num => $score) {
                $deck[] = [
                    'suit' => $suit,
                    'num' => (string)$num,
                    'score' => $score,
                ];
            }
        }
        return $deck;
    }
}
