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
    private const NUM_OF_FIRST_HAND = 2;

    /**
     * コンストラクタ
     *
     * @param DealerPlayer $dealerPlayer
     */
    public function __construct(private DealerPlayer $dealerPlayer)
    {
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

    /**
     * カードの合計値が 21 を超えているかを判定する
     *
     * @param Player $player
     * @return bool 21 を超えたら true
     */
    public function checkBurst(Player $player): bool
    {
        if ($player->getScoreTotal() > 21) {
            $player->changeStatus('burst');
            return true;
        }
        return false;
    }

    /**
     * 勝敗を判定する
     *
     * @param Deck $deck
     * @param array<int,Player> $players
     * @return void
     */
    public function judgeWinOrLose(Deck $deck, array $players): void
    {
        echo Message::getStandMessage($this->dealerPlayer);

        if ($this->hasStand($players)) {
            $this->dealerPlayer->action($deck, $this);

            $messages = [];
            $messages[] = Message::getScoreTotalResultMessage($this->dealerPlayer);

            if ($this->dealerPlayer->getStatus() === 'burst') {
                $messages[] = Message::getDealerBurstMessage();
                foreach ($players as $player) {
                    if ($player->getStatus() === 'stand') {
                        $player->changeStatus('win');
                        $messages[] = Message::getWinByBurstMessage($player);
                    }
                }
            } else {
                foreach ($players as $player) {
                    if ($player->getStatus() === 'stand') {
                        $result = $this->compareScoreTotal($player);
                        $player->changeStatus($result);
                        $messages[] = Message::getResultMessage($player);
                    }
                }
            }
            foreach ($messages as $message) {
                echo $message;
            }
            unset($message);
        }
    }

    /**
     * スタンドのプレイヤーがいるかについて、 bool を返す
     *
     * @param array<int,Player> $players
     * @return bool
     */
    private function hasStand(array $players): bool
    {
        foreach ($players as $player) {
            if ($player->getStatus() === 'stand') {
                return true;
            }
        }
        return false;
    }

    /**
     * プレイヤーとディーラーの得点を比較して、勝敗を返す
     *
     * @param Player $player
     * @return string $result
     */
    private function compareScoreTotal(Player $player): string
    {
        $result = '';
        $playerScoreTotal = $player->getScoreTotal();
        $dealerScoreTotal = $this->dealerPlayer->getScoreTotal();
        if ($playerScoreTotal > $dealerScoreTotal) {
            $result = 'win';
        } elseif ($playerScoreTotal < $dealerScoreTotal) {
            $result = 'lose';
        } elseif ($playerScoreTotal === $dealerScoreTotal) {
            $result = 'draw';
        }
        return $result;
    }
}
