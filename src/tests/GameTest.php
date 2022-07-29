<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Game.php');
require_once(__DIR__ . '/../lib/Card.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Game;
use Blackjack\Card;

class GameTest extends TestCase
{
    public function testStart(): void
    {
        $game = new Game();

        // プレイヤーの手札の枚数とデッキの枚数をテストする
        $this->assertSame(48, count($game->getDeck()->getDeck()));
        $this->assertSame(2, count($game->getPlayer()->getHand()));
        $this->assertSame(2, count($game->getDealer()->getHand()));
    }
}
