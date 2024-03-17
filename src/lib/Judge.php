<?php

namespace Blackjack;

require_once('Deck.php');
require_once('Player.php');

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
     * @param Game $game ゲームの状態を含むGameオブジェクト
     * @return void
     */
    public function judgeWinOrLose(Game $game): void
    {
        echo Message::getStandMessage($game->getDealer()->getDealerPlayer());
        sleep(Message::SECONDS_TO_DISPLAY);

        // スタンド状態のプレイヤーがいなければ早期リターン
        if (!$this->hasStand($game->getPlayers())) {
            return;
        }

        $game->getDealer()->getDealerPlayer()->action($game);
        echo Message::getScoreTotalResultMessage($game->getDealer()->getDealerPlayer());
        sleep(Message::SECONDS_TO_DISPLAY);

        // ディーラーのバースト状態をチェック
        $dealerBurst = $game->getDealer()->getDealerPlayer()->hasBurstStatus();
        if ($dealerBurst) {
            echo Message::getDealerBurstMessage();
            sleep(Message::SECONDS_TO_DISPLAY);
        }

        // すべてのプレイヤーに対して処理を行う
        foreach ($game->getPlayers() as $player) {
            // スタンド状態でなければ次のプレイヤーへ
            if (!$player->hasStandStatus()) {
                continue;
            }

            // ディーラーがバーストしていれば、プレイヤーは勝利
            if ($dealerBurst) {
                $player->changeStatus($player::WIN);
                echo Message::getWinByBurstMessage($player);
                continue;
            }

            // ディーラーがバーストしていなければ、スコアを比較
            $result = $this->compareScoreTotal($game->getDealer()->getDealerPlayer(), $player);
            $player->changeStatus($result);
            echo Message::getResultMessage($player);
            
            sleep(Message::SECONDS_TO_DISPLAY);
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
            if ($player->hasStandStatus()) {
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
        $playerScoreTotal = $player->getScoreTotal();
        $dealerScoreTotal = $dealerPlayer->getScoreTotal();
        if ($playerScoreTotal > $dealerScoreTotal) {
            return $player::WIN;
        } 
        if ($playerScoreTotal < $dealerScoreTotal) {
            return $player::LOSE;
        } 
        if ($playerScoreTotal === $dealerScoreTotal) {
            return $player::DRAW;
        }
    }
}
