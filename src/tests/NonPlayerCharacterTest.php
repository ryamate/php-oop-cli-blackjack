<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/NonPlayerCharacter.php');
require_once(__DIR__ . '/../lib/Deck.php');

use PHPUnit\Framework\TestCase;
use Blackjack\NonPlayerCharacter;
use Blackjack\Deck;

class NonPlayerCharacterTest extends TestCase
{
    public function testSelectHitOrStand()
    {
        $deck = new Deck();
        $deck->initDeck();

        $npc1 = new NonPlayerCharacter('test1', [], 20, 0, 'hit');
        $this->assertSame('N', $npc1->selectHitOrStand());

        $npc2 = new NonPlayerCharacter('test2', [], 17, 0, 'hit');
        $this->assertSame('N', $npc2->selectHitOrStand());

        $npc3 = new NonPlayerCharacter('test3', [], 16, 0, 'hit');
        $this->assertSame('Y', $npc3->selectHitOrStand());
    }
}
