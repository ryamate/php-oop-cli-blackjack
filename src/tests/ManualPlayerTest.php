<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/ManualPlayer.php');
require_once(__DIR__ . '/../lib/AutoPlayer.php');
require_once(__DIR__ . '/../lib/Card.php');

use PHPUnit\Framework\TestCase;
use Blackjack\ManualPlayer;
use Blackjack\Card;
use Blackjack\CardSuit;
use Blackjack\CardNumber;

class ManualPlayerTest extends TestCase
{
    public function testGetName(): void
    {
        $player = new ManualPlayer('あなた');
        $this->assertSame('あなた', $player->getName());
    }

    public function testGetHand(): void
    {
        $player = new ManualPlayer('あなた', 0, 0, [
            ['suit' => '♠', 'num' => '2', 'score' => 2],
            ['suit' => '♠', 'num' => '3', 'score' => 3],
        ]);
        $this->assertSame(
            [
                ['suit' => '♠', 'num' => '2', 'score' => 2],
                ['suit' => '♠', 'num' => '3', 'score' => 3],
            ],
            $player->getHand()
        );
    }
    
    public function testGetScoreTotal(): void
    {
        $player = new ManualPlayer('あなた');
        $this->assertSame(0, $player->getScoreTotal());
        $player2 = new ManualPlayer('きみ', 0, 0, [], 21, 1, 'stand');
        $this->assertSame(21, $player2->getScoreTotal());
    }

    public function testGetStatus(): void
    {
        $player = new ManualPlayer('あなた');
        $this->assertSame('hit', $player->getStatus());
        $player2 = new ManualPlayer('きみ', 0, 0, [], 21, 1, 'stand');
        $this->assertSame('stand', $player2->getStatus());
    }

    public function testAddCardToHand(): void
    {
        $player = new ManualPlayer('あなた', 0, 0, [
            ['suit' => '♠', 'num' => '2', 'score' => 2],
            ['suit' => '♠', 'num' => '3', 'score' => 3],
        ]);
        $player->addCardToHand(new Card(new CardSuit('♠'),new CardNumber('4')));
        $this->assertSame(
            [
                ['suit' => '♠', 'num' => '2', 'score' => 2],
                ['suit' => '♠', 'num' => '3', 'score' => 3],
                ['suit' => '♠', 'num' => '4', 'score' => 4],
            ],
            $player->getHand()
        );
    }

    public function testCalcScoreTotal(): void
    {
        $player = new ManualPlayer('あなた', 0, 0, [
            ['suit' => '♠', 'num' => 'A', 'score' => 11],
            ['suit' => '♠', 'num' => '3', 'score' => 3],
        ]);
        $player->calcScoreTotal();
        $this->assertSame(14, $player->getScoreTotal());

        $player->addCardToHand(new Card(new CardSuit('♥'),new CardNumber('A')));
        $player->calcScoreTotal();
        $this->assertSame(15, $player->getScoreTotal());

        $player->addCardToHand(new Card(new CardSuit('♦'),new CardNumber('A')));
        $player->calcScoreTotal();
        $this->assertSame(16, $player->getScoreTotal());

        $player->addCardToHand(new Card(new CardSuit('♣'),new CardNumber('A')));
        $player->calcScoreTotal();
        $this->assertSame(17, $player->getScoreTotal());
    }

    public function testChangeStatus(): void
    {
        $player = new ManualPlayer('あなた');
        $player->changeStatus('burst');
        $this->assertSame('burst', $player->getStatus());
    }
}
