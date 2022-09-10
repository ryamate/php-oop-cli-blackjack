<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Dealer.php');
require_once(__DIR__ . '/../lib/DealerPlayer.php');
require_once(__DIR__ . '/../lib/Judge.php');
require_once(__DIR__ . '/../lib/ChipCalculator.php');
require_once(__DIR__ . '/../lib/Deck.php');
require_once(__DIR__ . '/../lib/ManualPlayer.php');
require_once(__DIR__ . '/../lib/Message.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Dealer;
use Blackjack\DealerPlayer;
use Blackjack\Judge;
use Blackjack\ChipCalculator;
use Blackjack\Deck;
use Blackjack\ManualPlayer;

class DealerTest extends TestCase
{
    public function testDealOutFirstHand(): void
    {
        $deck = new Deck();
        $deck->initDeck();
        $player = new ManualPlayer('あなた');
        $dealer = new Dealer(
            new DealerPlayer('ディーラー'),
            new Judge(),
            new ChipCalculator()
        );
        $dealer->dealOutFirstHand($deck, $player);

        // カードの枚数をテストする
        $this->assertSame(2, count($player->getHand()));
    }

    public function testDealOneCard(): void
    {
        $deck = new Deck();
        $deck->initDeck();
        $player = new ManualPlayer('あなた');
        $dealer = new Dealer(
            new DealerPlayer('ディーラー'),
            new Judge(),
            new ChipCalculator()
        );
        $dealer->dealOneCard($deck, $player);

        // 1枚引いたカードの枚数をテストする
        $this->assertSame(1, count($player->getHand()));

        // 複数枚引いたカードの枚数をテストする
        $dealer->dealOneCard($deck, $player);
        $this->assertSame(2, count($player->getHand()));
    }
}
