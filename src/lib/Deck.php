<?php

namespace Blackjack;

require_once('Card.php');
require_once('CardSuit.php');
require_once('CardNumber.php');

use Blackjack\Card;
use Blackjack\CardSuit;
use Blackjack\CardNumber;

/**
 * デッキクラス
 */
class Deck
{
    /**
     * コンストラクタ
     *
     * @param array<Card> $deck デッキ
     */
    public function __construct(
        private array $deck = []
    ) {
    }

    /**
     * デッキを作成する
     *
     * @return Deck
     */
    public function createDeck(): Deck
    {
        $newDeck = [];
        $suits = ['♠', '♥', '♦', '♣'];
        $numbers = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

        foreach ($suits as $suitStr) {
            $suit = new CardSuit($suitStr);
            foreach ($numbers as $numberStr) {
                $number = new CardNumber($numberStr);
                $newDeck[] = new Card($suit, $number);
            }
        }

        return new self($newDeck);
    }

    /**
     * デッキをシャッフルする
     */
    public function shuffleDeck(): void
    {
        shuffle($this->deck);
    }

    /**
     * デッキからカードを１枚取る
     *
     * @return Card カード
     */
    public function takeCard(): Card
    {
        return array_shift($this->deck);
    }

    /**
     * deckプロパティを返す
     *
     * @return array<Card> デッキ
     */
    public function getDeck(): array
    {
        return $this->deck;
    }
}
