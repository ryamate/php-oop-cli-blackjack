<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Player.php');
require_once(__DIR__ . '/../lib/Deck.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Player;
use Blackjack\Deck;

class PlayerTest extends TestCase
{
    public function testDrawHand()
    {
        $player = new Player();
        $deck = new Deck();
        $deck->initDeck();

        // カードの枚数をテストする
        $this->assertSame(2, count($player->drawHand($deck)));
    }
}
