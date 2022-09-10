<?php

namespace Blackjack;

require_once('Deck.php');
require_once('Player.php');

use Blackjack\Deck;
use Blackjack\Player;

class Judge
{
    /**
     * カードの合計値が 21 を超えているかを判定する
     *
     * @param Player $player
     * @return bool 21 を超えたら true
     */
    public function checkBurst(Player $player): bool
    {
        if ($player->getScoreTotal() > 21) {
            $player->changeStatus(Player::BURST);
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
    public function judgeWinOrLose(Deck $deck, Dealer $dealer,  array $players): void
    {
        echo Message::getStandMessage($dealer->getDealerPlayer());

        if ($this->hasStand($players)) {
            $dealer->getDealerPlayer()->action($deck, $dealer);

            $messages = [];
            $messages[] = Message::getScoreTotalResultMessage($dealer->getDealerPlayer());

            if ($dealer->getDealerPlayer()->getStatus() === Player::BURST) {
                $messages[] = Message::getDealerBurstMessage();
                foreach ($players as $player) {
                    if ($player->getStatus() === Player::STAND) {
                        $player->changeStatus(Player::WIN);
                        $messages[] = Message::getWinByBurstMessage($player);
                    }
                }
            } else {
                foreach ($players as $player) {
                    if ($player->getStatus() === Player::STAND) {
                        $result = $this->compareScoreTotal($dealer->getDealerPlayer(), $player);
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
            if ($player->getStatus() === Player::STAND) {
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
    private function compareScoreTotal(DealerPlayer $dealerPlayer, Player $player): string
    {
        $result = '';
        $playerScoreTotal = $player->getScoreTotal();
        $dealerScoreTotal = $dealerPlayer->getScoreTotal();
        if ($playerScoreTotal > $dealerScoreTotal) {
            $result = Player::WIN;
        } elseif ($playerScoreTotal < $dealerScoreTotal) {
            $result = Player::LOSE;
        } elseif ($playerScoreTotal === $dealerScoreTotal) {
            $result = Player::DRAW;
        }
        return $result;
    }
}
