<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Dealer.php');
require_once(__DIR__ . '/../lib/Player.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Dealer;
use Blackjack\Deck;
use Blackjack\Player;

class DealerTest extends TestCase
{
    public function testCheckBurst()
    {
        $dealer = new Dealer();

        $player1 = new Player([], 20, 'hit');
        $this->assertSame(false, $dealer->checkBurst($player1));
        $player2 = new Player([], 21, 'hit');
        $this->assertSame(false, $dealer->checkBurst($player2));
        $player3 = new Player([], 22, 'hit');
        $this->assertSame(true, $dealer->checkBurst($player3));
    }

    public function testJudgeWinOrLose()
    {
        $dealer1 = new Dealer([], 20, 'hit');
        $player1 = new Player([], 21, 'hit');
        $dealer1->judgeWinOrLose($player1, $dealer1);
        $this->assertSame('win', $player1->getStatus());

        $dealer2 = new Dealer([], 21, 'hit');
        $player2 = new Player([], 20, 'hit');
        $dealer2->judgeWinOrLose($player2, $dealer2);
        $this->assertSame('lose', $player2->getStatus());

        $dealer3 = new Dealer([], 20, 'hit');
        $player3 = new Player([], 20, 'hit');
        $dealer3->judgeWinOrLose($player3, $dealer3);
        $this->assertSame('draw', $player3->getStatus());
    }

    public function testDrawAfterAllPlayerStand()
    {
        $deck = new Deck();
        $deck->initDeck();

        $dealer1 = new Dealer([], 20, 'hit');
        $dealer1->drawAfterAllPlayerStand($deck);
        $this->assertSame(20, $dealer1->getScoreTotal());

        $dealer2 = new Dealer([], 17, 'hit');
        $dealer2->drawAfterAllPlayerStand($deck);
        $this->assertSame(17, $dealer2->getScoreTotal());

        $dealer3 = new Dealer([], 16, 'hit');
        $dealer3->drawAfterAllPlayerStand($deck);
        $this->assertGreaterThan(16, $dealer3->getScoreTotal());
    }
}
