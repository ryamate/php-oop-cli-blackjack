<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/DealerPlayer.php');

use PHPUnit\Framework\TestCase;
use Blackjack\DealerPlayer;

class DealerPlayerTest extends TestCase
{
    public function testSelectHitOrStand(): void
    {
        $dealerPlayer1 = new DealerPlayer('ディーラー1', 0, 0, [], 18, 0, 'hit');
        $this->assertSame('N', $dealerPlayer1->selectHitOrStand());

        $dealerPlayer2 = new DealerPlayer('ディーラー2', 0, 0, [], 17, 0, 'hit');
        $this->assertSame('N', $dealerPlayer2->selectHitOrStand());

        $dealerPlayer3 = new DealerPlayer('ディーラー3', 0, 0, [], 16, 0, 'hit');
        $this->assertSame('Y', $dealerPlayer3->selectHitOrStand());
    }
}
