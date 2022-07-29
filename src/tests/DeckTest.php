<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Deck.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Deck;

class DeckTest extends TestCase
{
    public function testInitDeck(): void
    {
        $deck = new Deck();
        $deck->initDeck();
        // デッキの枚数をテストする
        $this->assertSame(52, count($deck->getDeck()));
    }

    public function testTakeACard(): void
    {
        $deck = new Deck();
        $deck->initDeck();

        // デッキの枚数をテストする
        $deck->takeACard();
        $this->assertSame(51, count($deck->getDeck()));

        $deck->takeACard();
        $this->assertSame(50, count($deck->getDeck()));

        $deck->takeACard();
        $this->assertSame(49, count($deck->getDeck()));
    }
}
