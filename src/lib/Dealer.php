<?php

namespace Blackjack;

require_once('Deck.php');
require_once('DealerPlayer.php');
require_once('Player.php');

use Blackjack\Deck;
use Blackjack\DealerPlayer;
use Blackjack\Player;

class Dealer
{
    public const NUM_OF_FIRST_HAND = 2;

    /**
     * コンストラクタ
     *
     * @param DealerPlayer $dealerPlayer
     * @param Judge $judge
     * @param ChipCalculator $chipCalculator
     * @param SpecialRule $specialRule
     */
    public function __construct(
        private DealerPlayer $dealerPlayer,
        private Judge $judge,
        private ChipCalculator $chipCalculator,
        private SpecialRule $specialRule
    ) {
    }

    /**
     * DealerPlayer を返す
     *
     * @return DealerPlayer $this->dealerPlayer
     */
    public function getDealerPlayer(): DealerPlayer
    {
        return $this->dealerPlayer;
    }

    /**
     * Judge を返す
     *
     * @return Judge $this->judge
     */
    public function getJudge(): Judge
    {
        return $this->judge;
    }

    /**
     * ChipCalculator を返す
     *
     * @return ChipCalculator $this->chipCalculator
     */
    public function getChipCalculator(): ChipCalculator
    {
        return $this->chipCalculator;
    }

    /**
     * SpecialRule を返す
     *
     * @return SpecialRule $this->specialRule
     */
    public function getSpecialRule(): SpecialRule
    {
        return $this->specialRule;
    }

    /**
     * 初めの手札2枚を配る
     *
     * @param Deck $deck
     * @param Player $player
     */
    public function dealOutFirstHand(Deck $deck, Player $player): void
    {
        for ($i = 1; $i <= self::NUM_OF_FIRST_HAND; $i++) {
            $this->dealOneCard($deck, $player);
        }
    }

    /**
     * 手札の枚数が初めの2枚か
     *
     * @param array<int,array<string,int|string>> $hand 手札
     * @return bool
     */
    public function isFirstHand(array $hand): bool
    {
        if (count($hand) === self::NUM_OF_FIRST_HAND) {
            return true;
        }
        return false;
    }

    /**
     * カードを1枚配る（デッキからカードを1枚引いて、プレイヤーの手札に加える）
     *
     * @param Deck $deck
     * @param Player $player
     */
    public function dealOneCard(Deck $deck, Player $player): void
    {
        $cardDrawn = array_slice($deck->getDeck(), 0, 1);
        $deck->takeACard();
        $player->addACardToHand($cardDrawn);
        $player->calcScoreTotal();
    }
}
