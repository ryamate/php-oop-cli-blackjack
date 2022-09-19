<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

class ChipCalculator
{
    /**
     * 勝敗、特殊ルールに応じたプレイヤーのチップ残高を算出する
     *
     * @param Game $game
     * @param Player $player
     * @return void
     */
    public function calcChips(Game $game, Player $player): void
    {
        if ($player->getSplitStatus() === $player::NO_SPLIT) {
            $chips = $player->getChips();
            if ($player->getStatus() === $player::WIN) {
                $chips += $player->getBets();
                echo $player->getName() . 'は勝ったため、チップ ' . $player->getBets() . ' ドルと同額の配当を得られます。' . PHP_EOL;
            } elseif ($player->getStatus() === $player::LOSE || $player->getStatus() === $player::BURST) {
                $chips -= $player->getBets();
                echo $player->getName() . 'は負けたため、チップ ' . $player->getBets() . ' ドルは没収されます。' . PHP_EOL;
            } elseif ($player->getStatus() === $player::DRAW) {
                // チップ残高の変更なし
                echo $player->getName() . 'は引き分けたため、チップ ' . $player->getBets() . ' ドルはそのままです。' . PHP_EOL;
            }
            sleep(1);
            $player->changeChips($chips);
            echo $player->getName() . 'のチップ残高は ' . $player->getChips() . ' ドルです。' . PHP_EOL;
            sleep(1);
            $player->reset();
        } elseif ($player->getSplitStatus() === $player::SPLIT_FIRST) {
            $this->calcChipsSplitFirstHand($player);
        } elseif ($player->getSplitStatus() === $player::SPLIT_SECOND) {
            $this->calcChipsSplitSecondHand($game, $player);
        }
    }

    /**
     * スプリット宣言プレイヤー(splitStatus: 1) について
     *
     * @param Player $playerFirstHand
     * @return void
     */
    private function calcChipsSplitFirstHand(Player $playerFirstHand)
    {
        echo $playerFirstHand->getName() . 'は、スプリットを宣言しています。' . PHP_EOL;
        $chips = $playerFirstHand->getChips();
        if ($playerFirstHand->getStatus() === $playerFirstHand::WIN) {
            $chips += $playerFirstHand->getBets();
            echo '1 手目: 勝ったため、チップ ' . $playerFirstHand->getBets() . ' ドルと同額の配当を得られます。' . PHP_EOL;
        } elseif (
            $playerFirstHand->getStatus() === $playerFirstHand::LOSE ||
            $playerFirstHand->getStatus() === $playerFirstHand::BURST
        ) {
            $chips -= $playerFirstHand->getBets();
            echo '1 手目: 負けたため、チップ ' . $playerFirstHand->getBets() . ' ドルは没収されます。' . PHP_EOL;
        } elseif ($playerFirstHand->getStatus() === $playerFirstHand::DRAW) {
            // チップ残高の変更なし
            echo '1 手目: 引き分けたため、チップ ' . $playerFirstHand->getBets() . ' ドルはそのままです。' . PHP_EOL;
        }
        sleep(1);
        $playerFirstHand->changeChips($chips);
        echo $playerFirstHand->getName() . 'のチップ残高は ' . $playerFirstHand->getChips() . ' ドルです。' . PHP_EOL;
        sleep(1);
        $playerFirstHand->reset();
    }

    /**
     * スプリット宣言プレイヤーの2手目(splitStatus: 2) について
     *
     * @param Player $playerSecondHand
     * @return void
     */
    private function calcChipsSplitSecondHand(Game $game, Player $playerSecondHand)
    {
        foreach ($game->getPlayers() as $player) {
            if (
                $player->getName() === $playerSecondHand->getName() &&
                $player->getSplitStatus() === $playerSecondHand::SPLIT_FIRST &&
                $playerSecondHand->getSplitStatus() === $playerSecondHand::SPLIT_SECOND
            ) {
                $chips = $player->getChips();
                if ($playerSecondHand->getStatus() === $playerSecondHand::WIN) {
                    $chips += $playerSecondHand->getBets();
                    echo '2 手目: 勝ったため、チップ ' . $playerSecondHand->getBets() . ' ドルと同額の配当を得られます。' . PHP_EOL;
                } elseif (
                    $playerSecondHand->getStatus() === $playerSecondHand::LOSE ||
                    $playerSecondHand->getStatus() === $playerSecondHand::BURST
                ) {
                    $chips -= $playerSecondHand->getBets();
                    echo '2 手目: 負けたため、チップ ' . $playerSecondHand->getBets() . ' ドルは没収されます。' . PHP_EOL;
                } elseif ($playerSecondHand->getStatus() === $playerSecondHand::DRAW) {
                    // チップ残高の変更なし
                    echo '2 手目: 引き分けたため、チップ ' . $playerSecondHand->getBets() . ' ドルはそのままです。' . PHP_EOL;
                }
                sleep(1);
                $player->changeChips($chips);
                echo $player->getName() . 'のチップ残高は ' . $player->getChips() . ' ドルです。' . PHP_EOL;
                sleep(1);

                // スプリット宣言したプレイヤーのステータスリセット、2手目の削除
                $player->changeSplitStatus($playerSecondHand::NO_SPLIT);
                $game->removeSplitPlayer($playerSecondHand);
                break;
            }
        }
    }
}
