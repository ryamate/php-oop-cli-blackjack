<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Player.php');
require_once(__DIR__ . '/../lib/Card.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Player;
use Blackjack\Card;

class PlayerTest extends TestCase
{
    public function testGetName(): void
    {
        $player = new Player('あなた');
        $this->assertSame('あなた', $player->getName());
        $nPC1 = new Player('NPC1');
        $this->assertSame('NPC1', $nPC1->getName());
    }

    public function testGetHand(): void
    {
        $player = new Player('あなた', [
            ['suit' => 'スペード', 'num' => '2', 'score' => 2],
            ['suit' => 'スペード', 'num' => '3', 'score' => 3],
        ]);
        $this->assertSame(
            [
                ['suit' => 'スペード', 'num' => '2', 'score' => 2],
                ['suit' => 'スペード', 'num' => '3', 'score' => 3],
            ],
            $player->getHand()
        );
    }

    public function testGetScoreTotal(): void
    {
        $player = new Player('あなた');
        $this->assertSame(0, $player->getScoreTotal());
        $nPC1 = new Player('NPC1', [], 21, 1, 'stand');
        $this->assertSame(21, $nPC1->getScoreTotal());
    }

    public function testGetCountAce(): void
    {
        $player = new Player('あなた');
        $this->assertSame(0, $player->getCountAce());
        $nPC1 = new Player('NPC1', [], 21, 1, 'stand');
        $this->assertSame(1, $nPC1->getCountAce());
    }

    public function testGetStatus(): void
    {
        $player = new Player('あなた');
        $this->assertSame('hit', $player->getStatus());
        $nPC1 = new Player('NPC1', [], 21, 1, 'stand');
        $this->assertSame('stand', $nPC1->getStatus());
    }

    public function testAddACardToHand(): void
    {
        $player = new Player('あなた', [
            ['suit' => 'スペード', 'num' => '2', 'score' => 2],
            ['suit' => 'スペード', 'num' => '3', 'score' => 3],
        ]);
        $player->addACardToHand([
            ['suit' => 'スペード', 'num' => '4', 'score' => 4],
        ]);
        $this->assertSame(
            [
                ['suit' => 'スペード', 'num' => '2', 'score' => 2],
                ['suit' => 'スペード', 'num' => '3', 'score' => 3],
                ['suit' => 'スペード', 'num' => '4', 'score' => 4],
            ],
            $player->getHand()
        );
    }

    public function testCalcScoreTotal(): void
    {
        $player = new Player('あなた', [
            ['suit' => 'スペード', 'num' => 'A', 'score' => 11],
            ['suit' => 'スペード', 'num' => '3', 'score' => 3],
        ]);
        $player->calcScoreTotal();
        $this->assertSame(14, $player->getScoreTotal());

        $player->addACardToHand([
            ['suit' => 'ハート', 'num' => 'A', 'score' => 11],
        ]);
        $player->calcScoreTotal();
        $this->assertSame(15, $player->getScoreTotal());

        $player->addACardToHand([
            ['suit' => 'ダイヤ', 'num' => 'A', 'score' => 11],
        ]);
        $player->calcScoreTotal();
        $this->assertSame(16, $player->getScoreTotal());

        $player->addACardToHand([
            ['suit' => 'クラブ', 'num' => 'A', 'score' => 11],
        ]);
        $player->calcScoreTotal();
        $this->assertSame(17, $player->getScoreTotal());
    }

    public function testChangeStatus(): void
    {
        $player = new Player('あなた');
        $player->changeStatus('burst');
        $this->assertSame('burst', $player->getStatus());
    }
}
