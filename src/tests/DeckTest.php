<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Deck.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Deck;
use Blackjack\Card;

class DeckTest extends TestCase
{
    /**
     * デッキを初期化するテスト
     */
    public function testInitDeck(): void
    {
        $deck = new Deck();
        
        $this->assertSame(52, count($deck->getDeck()));
    }

    /**
     * デッキからカードを引くテスト
     */
    public function testTakeCard(): void
    {
        $deck = new Deck();
        
        $initialCount = count($deck->getDeck());
        $card = $deck->takeCard();
        $this->assertSame($initialCount - 1, count($deck->getDeck()));
        $this->assertInstanceOf(Card::class, $card);
    }
}
