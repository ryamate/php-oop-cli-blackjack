<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/SpecialRule.php');
require_once(__DIR__ . '/../lib/Game.php');

use PHPUnit\Framework\TestCase;
use Blackjack\SpecialRule;
use Blackjack\Game;

class SpecialRuleTest extends TestCase
{
    public function testApplySpecialRule(): void
    {
        $inputAction = 'DD';
        $game = new Game();
        $game->getDeck()->initDeck();
        $game->getDealer()->dealOutFirstHand($game->getDeck(), $game->getPlayers()[0]);

        $specialRule = new SpecialRule();
        $specialRule->applySpecialRule($inputAction, $game, $game->getPlayers()[0]);
        $this->assertSame(3, count($game->getPlayers()[0]->getHand()));

        $inputAction = 'AA';
        $this->assertSame(
            'Y/N（DD/SP/SR は、最初に手札が配られたときのみ）を入力してください。' . PHP_EOL,
            $specialRule->applySpecialRule($inputAction, $game, $game->getPlayers()[0])
        );
    }
}
