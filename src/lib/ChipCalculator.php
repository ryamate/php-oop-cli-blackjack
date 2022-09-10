<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

class ChipCalculator
{
    /**
     * 勝敗、特殊ルールに応じたプレイヤーのチップ残高を算出する
     *
     * @param Player $player
     * @return void
     */
    public function calcChips(Player $player): void
    {
        $chips = $player->getChips();
        if ($player->getStatus() === Player::WIN) {
            $chips += $player->getBets();
            echo '勝ったため、チップ ' . $player->getBets() . ' ドルと同額の配当を得られます。' . PHP_EOL;
        } elseif ($player->getStatus() === Player::LOSE || $player->getStatus() === Player::BURST) {
            $chips -= $player->getBets();
            echo '負けたため、チップ ' . $player->getBets() . ' ドルは没収されます。' . PHP_EOL;
        } elseif ($player->getStatus() === Player::DRAW) {
            // チップ残高の変更なし
            echo '引き分けたため、チップ ' . $player->getBets() . ' ドルはそのままです。' . PHP_EOL;
        }
        $player->changeChips($chips);
        echo $player->getName() . 'のチップ残高は ' . $player->getChips() . ' ドルです。' . PHP_EOL;
        $player->reset();
    }
}
