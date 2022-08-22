<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/AutoPlayer.php');

use PHPUnit\Framework\TestCase;
use Blackjack\AutoPlayer;

class AutoPlayerTest extends TestCase
{
    public function testSelectHitOrStand()
    {
        $NPC1 = new AutoPlayer('NPC1', [], 18, 0, 'hit');
        $this->assertSame('N', $NPC1->selectHitOrStand());

        $NPC2 = new AutoPlayer('NPC2', [], 17, 0, 'hit');
        $this->assertSame('N', $NPC2->selectHitOrStand());

        $NPC3 = new AutoPlayer('NPC3', [], 16, 0, 'hit');
        $this->assertSame('Y', $NPC3->selectHitOrStand());
    }
}
