<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Deck.php');
require_once(__DIR__ . '/../lib/Card.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Card;
use Blackjack\Deck;

class DeckTest extends TestCase
{
    public function testInitDeck()
    {
        $deck = new Deck();
        // デッキの枚数をテストする
        $this->assertSame(52, count($deck->initDeck()));
    }
}
