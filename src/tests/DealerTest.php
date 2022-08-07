<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Dealer.php');
require_once(__DIR__ . '/../lib/Deck.php');
require_once(__DIR__ . '/../lib/Player.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Dealer;
use Blackjack\Deck;
use Blackjack\Player;

class DealerTest extends TestCase
{
    public function testDealOutFirstHand(): void
    {
        $player = new Player('player');
        $dealer = new Dealer('dealer');
        $dealer->dealOutFirstHand($player);

        // カードの枚数をテストする
        $this->assertSame(2, count($player->getHand()));
    }

    public function testDealOneCard(): void
    {
        $dealer = new Dealer('dealer');
        $player = new Player('player');
        $player = $dealer->dealOneCard($player);

        // 1枚引いたカードの枚数をテストする
        $this->assertSame(1, count($player->getHand()));

        // 複数枚引いたカードの枚数をテストする
        $player = $dealer->dealOneCard($player);
        $this->assertSame(2, count($player->getHand()));
    }


    public function testCheckBurst()
    {
        $dealer = new Dealer('dealer');

        $player1 = new Player('player1', [], 20, 0, 'hit');
        $this->assertSame(false, $dealer->checkBurst($player1));
        $player2 = new Player('player2', [], 21, 0, 'hit');
        $this->assertSame(false, $dealer->checkBurst($player2));
        $player3 = new Player('player3', [], 22, 0, 'hit');
        $this->assertSame(true, $dealer->checkBurst($player3));
    }

    public function testJudgeWinOrLose()
    {
        $dealer1 = new Dealer('dealer1', [], 20, 0, 'hit');
        $player1 = new Player('player1', [], 21, 0, 'hit');
        $dealer1->judgeWinOrLose($player1);
        $this->assertSame('win', $player1->getStatus());

        $dealer2 = new Dealer('dealer2', [], 21, 0, 'hit');
        $player2 = new Player('player2', [], 20, 0, 'hit');
        $dealer2->judgeWinOrLose($player2);
        $this->assertSame('lose', $player2->getStatus());

        $dealer3 = new Dealer('dealer3', [], 20, 0, 'hit');
        $player3 = new Player('player3', [], 20, 0, 'hit');
        $dealer3->judgeWinOrLose($player3);
        $this->assertSame('draw', $player3->getStatus());
    }

    public function testDrawAfterAllPlayerStand()
    {
        $dealer1 = new Dealer('dealer1', [], 20, 0, 'hit');
        $dealer1->drawAfterAllPlayerStand();
        $this->assertSame(20, $dealer1->getScoreTotal());

        $dealer2 = new Dealer('dealer2', [], 17, 0, 'hit');
        $dealer2->drawAfterAllPlayerStand();
        $this->assertSame(17, $dealer2->getScoreTotal());

        $dealer3 = new Dealer('dealer3', [], 16, 0, 'hit');
        $dealer3->drawAfterAllPlayerStand();
        $this->assertGreaterThan(16, $dealer3->getScoreTotal());
    }
}
