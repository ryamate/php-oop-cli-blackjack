<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Player.php');
require_once(__DIR__ . '/../lib/Deck.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Player;
use Blackjack\Deck;

class PlayerTest extends TestCase
{
    public function testInitHand()
    {
        $player = new Player();
        $deck = new Deck();
        $deck->initDeck();
        $player->initHand($deck);

        // カードの枚数をテストする
        $this->assertSame(2, count($player->getHand()));
    }

    public function testDrawACard()
    {
        $player = new Player();
        $deck = new Deck();
        $deck->initDeck();
        $player->initHand($deck);
        $player->drawACard($deck);

        // カードの枚数をテストする
        $this->assertSame(3, count($player->getHand()));
    }

    // public function testCalcScoreTotal()
    // {
    // }

    public function testChangeStatus()
    {
        $player = new Player();
        $player->changeStatus('burst');
        $this->assertSame('burst', $player->getStatus());
    }
}
