<?php

namespace Blackjack;

require_once('Card.php');

use Blackjack\Card;

/**
 * デッキクラス
 */
class Deck
{
    /**
     * コンストラクタ
     *
     * @param array<int,array<string,int|string>> $deck デッキ
     */
    public function __construct(
        private array $deck = []
    ) {
    }

    /**
     * deck プロパティを返す
     *
     * @return array<int,array<string,int|string>> $deck デッキ
     */
    public function getDeck(): array
    {
        return $this->deck;
    }

    /**
     * デッキを初期化する
     *
     * @return array<int,array<string,int|string>> $deck デッキ
     */
    public function initDeck(): array
    {
        $card = new Card();
        $this->deck = $card->createNewDeck();
        shuffle($this->deck);
        return $this->deck;
    }

    /**
     * デッキから、プレイヤーが引いたカードを１枚除く
     *
     * @return void
     */
    public function takeACard(): void
    {
        $this->deck = array_slice($this->deck, 1);
    }
}
