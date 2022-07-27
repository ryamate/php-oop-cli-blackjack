<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Card.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Card;

class CardTest extends TestCase
{
    public function testCreateNewDeck(): void
    {
        $card = new Card();
        // カードの枚数をテストする
        $this->assertSame(52, count($card->createNewDeck()));
    }
}
