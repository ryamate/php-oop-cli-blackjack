<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Game.php');
require_once(__DIR__ . '/../lib/Player.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Game;
use Blackjack\Player;

class GameTest extends TestCase
{
    public function testStart()
    {
        $game = new Game();
        $game->start();

        // プレイヤーの手札の枚数とデッキの枚数をテストする
        $this->assertSame(2, count($game->getPlayer()->getHand()));
        $this->assertSame(2, count($game->getDealer()->getHand()));
        $this->assertSame(48, count($game->getDeck()->getDeck()));
    }
}
