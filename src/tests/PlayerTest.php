<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Player.php');
require_once(__DIR__ . '/../lib/Deck.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Player;
use Blackjack\Deck;

class PlayerTest extends TestCase
{
    public function testChangeStatus(): void
    {
        $player = new Player('test');
        $player->changeStatus('burst');
        $this->assertSame('burst', $player->getStatus());
    }
}
