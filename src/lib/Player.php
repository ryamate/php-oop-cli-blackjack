<?php

namespace Blackjack;

require_once('Deck.php');

use Blackjack\Deck;

class Player
{
    /**
     * コンストラクタ
     *
     * @param array $hand
     */
    public function __construct(private array $hand = [])
    {
    }

    /**
     * 手札を返す
     *
     * @return array $this->hand 手札
     */
    public function getHand(): array
    {
        return $this->hand;
    }

    /**
     * 手札を引く
     *
     * @return array $hand 2枚の手札
     */
    public function drawHand(Deck $deck): array
    {
        $this->hand = array_slice($deck->getDeck(), 0, 2);
        return $this->hand;
    }

    /**
     * プレイヤーの現在の得点を計算する
     *
     * @return int $scoreTotal プレイヤーの現在の得点
     */
    public function calcScoreTotal(): int
    {
        $scoreTotal = 0;
        foreach ($this->hand as $card) {
            $scoreTotal += $card['score'];
        }
        return $scoreTotal;
    }
}
