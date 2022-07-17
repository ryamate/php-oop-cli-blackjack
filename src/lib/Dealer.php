<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

class Dealer extends Player
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
            $player->changeStatus('burst');
            return true;
        }
        return false;
    }

    /**
     * 勝敗を判定する
     *
     * @param Player $player
     * @param Dealer $dealer
     * @return void
     */
    public function judgeWinOrLose(Player $player, Dealer $dealer)
    {
        $playerScoreTotal = $player->getScoreTotal();
        $dealerScoreTotal = $dealer->getScoreTotal();

        if ($playerScoreTotal > $dealerScoreTotal) {
            $player->changeStatus('win');
        } elseif ($playerScoreTotal < $dealerScoreTotal) {
            $player->changeStatus('lose');
        } elseif ($playerScoreTotal === $dealerScoreTotal) {
            $player->changeStatus('draw');
        }
    }

    /**
     * すべてのプレーヤーがカードを引くのをやめた（スタンド）後に、ディーラーは自分のカードの合計値が17以上になるまで引き続ける
     *
     * @param Deck $deck
     * @return void
     */
    public function drawAfterAllPlayerStand(Deck $deck)
    {
        while ($this->getScoreTotal() < 17) {
            $this->drawACard($deck);
        }
    }
}
