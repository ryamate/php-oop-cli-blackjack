<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/AutoPlayer.php');

use PHPUnit\Framework\TestCase;
use Blackjack\AutoPlayer;

class AutoPlayerTest extends TestCase
{
    public function testSelectHitOrStand(): void
    {
        $nPC1 = new AutoPlayer('NPC1', 0, 0, [], 18, 0, 'hit');
        $this->assertSame('N', $nPC1->selectHitOrStand());

        $nPC2 = new AutoPlayer('NPC2', 0, 0, [], 17, 0, 'hit');
        $this->assertSame('N', $nPC2->selectHitOrStand());

        $nPC3 = new AutoPlayer('NPC3', 0, 0, [], 16, 0, 'hit');
        $this->assertSame('Y', $nPC3->selectHitOrStand());
    }
}
