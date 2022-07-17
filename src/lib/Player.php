<?php

namespace Blackjack;

require_once('Deck.php');

use Blackjack\Deck;

class Player
{

    private const INIT_NUM_OF_CARDS_IN_HAND = 2;

    /**
     * コンストラクタ
     *
     * @param array $hand 手札
     * @param int $scoreTotal プレイヤーの現在の得点
     * @param int $status プレイヤーの状態
     */
    public function __construct(
        private array $hand = [],
        private int $scoreTotal = 0,
        private string $status = 'hit'
    ) {
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
     * 得点を返す
     *
     * @return int $this->scoreTotal 得点
     */
    public function getScoreTotal(): int
    {
        return $this->scoreTotal;
    }

    /**
     * プレイヤーの状態を返す
     *
     * @return string $this->status プレイヤーの状態
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * 手札を初期化する
     *
     * @param Deck $deck
     * @return void
     */
    public function initHand(Deck $deck): void
    {
        for ($i = 1; $i <= self::INIT_NUM_OF_CARDS_IN_HAND; $i++) {
            $this->drawACard($deck);
        }
        $this->calcScoreTotal();
    }

    /**
     * デッキからカードを引いて、手札に加える
     *
     * @param Deck $deck
     * @return void
     */
    public function drawACard(Deck $deck): void
    {
        $cardDrawn =  array_slice($deck->getDeck(), 0, 1);
        $deck->takeACard();
        $this->hand = array_merge($this->hand, $cardDrawn);
        $this->calcScoreTotal();
    }

    /**
     * プレイヤーの現在の得点を計算する
     *
     */
    private function calcScoreTotal(): void
    {
        $this->scoreTotal = 0;
        foreach ($this->hand as $card) {
            $this->scoreTotal += $card['score'];
        }
    }

    /**
     * ステータスを変更する
     *
     * @param string $status
     * @return void
     */
    public function changeStatus(string $status): void
    {
        $this->status = $status;
    }
}
