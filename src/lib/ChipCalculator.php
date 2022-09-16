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
        if ($player->getSplitStatus() === Player::NO_SPLIT) {
            $chips = $player->getChips();
            if ($player->getStatus() === Player::WIN) {
                $chips += $player->getBets();
                echo $player->getName() . 'は勝ったため、チップ ' . $player->getBets() . ' ドルと同額の配当を得られます。' . PHP_EOL;
            } elseif ($player->getStatus() === Player::LOSE || $player->getStatus() === Player::BURST) {
                $chips -= $player->getBets();
                echo $player->getName() . 'は負けたため、チップ ' . $player->getBets() . ' ドルは没収されます。' . PHP_EOL;
            } elseif ($player->getStatus() === Player::DRAW) {
                // チップ残高の変更なし
                echo $player->getName() . 'は引き分けたため、チップ ' . $player->getBets() . ' ドルはそのままです。' . PHP_EOL;
            }
            sleep(1);
            $player->changeChips($chips);
            echo $player->getName() . 'のチップ残高は ' . $player->getChips() . ' ドルです。' . PHP_EOL;
            sleep(1);
            $player->reset();
        } elseif ($player->getSplitStatus() === Player::SPLIT_FIRST) {
            $this->calcChipsDeclaredPlayer($player);
        } elseif ($player->getSplitStatus() === Player::SPLIT_SECOND) {
            $this->calcChipsSplitPlayer($game, $player);
        }
    }

    /**
     * スプリット宣言プレイヤー(splitStatus: 1) について
     *
     * @param Player $player
     * @return void
     */
    private function calcChipsDeclaredPlayer(Player $player)
    {
        echo $player->getName() . 'は、スプリットを宣言しています。' . PHP_EOL;
        $chips = $player->getChips();
        if ($player->getStatus() === Player::WIN) {
            $chips += $player->getBets();
            echo '1 手目: 勝ったため、チップ ' . $player->getBets() . ' ドルと同額の配当を得られます。' . PHP_EOL;
        } elseif ($player->getStatus() === Player::LOSE || $player->getStatus() === Player::BURST) {
            $chips -= $player->getBets();
            echo '1 手目: 負けたため、チップ ' . $player->getBets() . ' ドルは没収されます。' . PHP_EOL;
        } elseif ($player->getStatus() === Player::DRAW) {
            // チップ残高の変更なし
            echo '1 手目: 引き分けたため、チップ ' . $player->getBets() . ' ドルはそのままです。' . PHP_EOL;
        }
        sleep(1);
        $player->changeChips($chips);
        echo $player->getName() . 'のチップ残高は ' . $player->getChips() . ' ドルです。' . PHP_EOL;
        sleep(1);
        $player->reset();
    }

    /**
     * スプリット宣言プレイヤーの2手目(splitStatus: 2) について
     *
     * @param Player $player
     * @return void
     */
    private function calcChipsSplitPlayer(Game $game, Player $splitPlayer)
    {
        foreach ($game->getPlayers() as $player) {
            if (
                $player->getName() === $splitPlayer->getName() &&
                $player->getSplitStatus() === Player::SPLIT_FIRST &&
                $splitPlayer->getSplitStatus() === Player::SPLIT_SECOND
            ) {
                $chips = $player->getChips();
                if ($splitPlayer->getStatus() === Player::WIN) {
                    $chips += $splitPlayer->getBets();
                    echo '2 手目: 勝ったため、チップ ' . $splitPlayer->getBets() . ' ドルと同額の配当を得られます。' . PHP_EOL;
                } elseif ($splitPlayer->getStatus() === Player::LOSE || $splitPlayer->getStatus() === Player::BURST) {
                    $chips -= $splitPlayer->getBets();
                    echo '2 手目: 負けたため、チップ ' . $splitPlayer->getBets() . ' ドルは没収されます。' . PHP_EOL;
                } elseif ($splitPlayer->getStatus() === Player::DRAW) {
                    // チップ残高の変更なし
                    echo '2 手目: 引き分けたため、チップ ' . $splitPlayer->getBets() . ' ドルはそのままです。' . PHP_EOL;
                }
                sleep(1);
                $player->changeChips($chips);
                echo $player->getName() . 'のチップ残高は ' . $player->getChips() . ' ドルです。' . PHP_EOL;
                sleep(1);

                // スプリット宣言したプレイヤーのステータスリセット、2手目の削除
                $player->changeSplitStatus(Player::NO_SPLIT);
                $game->removeSplitPlayer($splitPlayer);
                break;
            }
        }
    }
}
