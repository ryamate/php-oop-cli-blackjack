<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Game.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Game;

class GameTest extends TestCase
{
    private const NUM_OF_CARDS_IN_HAND = 2;

    public function testStart()
    {
        $game = new Game();
        // デッキを初期化する
        $game->getDeck()->initDeck();

        // プレイヤーを初期化する
        // プレイヤーは手札を2枚引く
        $game->getPlayer()->drawHand($game->getDeck());
        // デッキはカードを2枚取られる
        $game->getDeck()->takeCard(self::NUM_OF_CARDS_IN_HAND);

        // ディーラーを初期化する
        // ディーラーは手札を2枚引く
        $game->getDealer()->drawHand($game->getDeck());
        // デッキはカードを2枚取られる
        $game->getDeck()->takeCard(self::NUM_OF_CARDS_IN_HAND);

        // プレイヤーの手札の枚数とデッキの枚数をテストする
        $this->assertSame(2, count($game->getPlayer()->getHand()));
        $this->assertSame(2, count($game->getDealer()->getHand()));
        $this->assertSame(48, count($game->getDeck()->getDeck()));
    }
}
