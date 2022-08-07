<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Game.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Game;

class GameTest extends TestCase
{
    // TODO:テスト書く
    // public function testSet(): void
    // {
    // }

    public function testStart(): void
    {
        $game = new Game();

        // プレイヤーの手札の枚数とデッキの枚数をテストする
        $this->assertSame(52, count($game->getDealer()->getDeck()->getDeck()));

        $game->getDealer()->dealOutFirstHand($game->getPlayer());
        $game->getDealer()->dealOutFirstHand($game->getDealer());

        $this->assertSame(48, count($game->getDealer()->getDeck()->getDeck()));
        $this->assertSame(2, count($game->getPlayer()->getHand()));
        $this->assertSame(2, count($game->getDealer()->getHand()));
    }
}
