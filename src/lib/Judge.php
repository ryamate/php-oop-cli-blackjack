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
            $player->changeStatus($player::BURST);
            return true;
        }
        return false;
    }

    /**
     * 勝敗を判定する
     *
     * @param Game $game
     * @return void
     */
    public function judgeWinOrLose(Game $game): void
    {
        $dealersSecondCard = $game->getDealer()->getDealerPlayer()->getHand()[1];
        echo 'ディーラーの引いた2枚目のカードは' . $dealersSecondCard['suit'] . 'の' . $dealersSecondCard['num'] . 'でした。' . PHP_EOL;
        sleep(1);

        if ($this->hasStand($game->getPlayers())) {
            $game->getDealer()->getDealerPlayer()->action($game);

            echo Message::getScoreTotalResultMessage($game->getDealer()->getDealerPlayer());
            sleep(1);

            if ($game->getDealer()->getDealerPlayer()->getStatus() === Player::BURST) {
                echo Message::getDealerBurstMessage();
                sleep(1);

                foreach ($game->getPlayers() as $player) {
                    if ($player->getStatus() === $player::STAND) {
                        $player->changeStatus($player::WIN);
                        echo Message::getWinByBurstMessage($player);
                        sleep(1);
                    }
                }
            } else {
                foreach ($game->getPlayers() as $player) {
                    if ($player->getStatus() === $player::STAND) {
                        $result = $this->compareScoreTotal($game->getDealer()->getDealerPlayer(), $player);
                        $player->changeStatus($result);
                        echo Message::getResultMessage($player);
                        sleep(1);
                    }
                }
            }
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
            if ($player->getStatus() === $player::STAND) {
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
            $result = $player::WIN;
        } elseif ($playerScoreTotal < $dealerScoreTotal) {
            $result = $player::LOSE;
        } elseif ($playerScoreTotal === $dealerScoreTotal) {
            $result = $player::DRAW;
        }
        return $result;
    }
}
