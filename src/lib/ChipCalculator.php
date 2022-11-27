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
                echo Message::getWinAndGetChipsMessage($player);
            } elseif ($player->getStatus() === $player::LOSE || $player->getStatus() === $player::BURST) {
                $chips -= $player->getBets();
                echo Message::getLoseAndLoseChipsMessage($player);
            } elseif ($player->getStatus() === $player::DRAW) {
                // チップ残高の変更なし
                echo Message::getDrawAndKeepChipsMessage($player);
            }
            sleep(Message::SECONDS_TO_DISPLAY);

            $player->changeChips($chips);
            echo Message::getChipBalanceMessage($player);
            sleep(Message::SECONDS_TO_DISPLAY);
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
        echo Message::getSplitDeclarationMessage($playerFirstHand);
        $chips = $playerFirstHand->getChips();
        if ($playerFirstHand->getStatus() === $playerFirstHand::WIN) {
            $chips += $playerFirstHand->getBets();
            echo Message::getWinAndGetChipsMessage($playerFirstHand);
        } elseif (
            $playerFirstHand->getStatus() === $playerFirstHand::LOSE ||
            $playerFirstHand->getStatus() === $playerFirstHand::BURST
        ) {
            $chips -= $playerFirstHand->getBets();
            echo Message::getLoseAndLoseChipsMessage($playerFirstHand);
        } elseif ($playerFirstHand->getStatus() === $playerFirstHand::DRAW) {
            // チップ残高の変更なし
            echo Message::getDrawAndKeepChipsMessage($playerFirstHand);
        }
        sleep(Message::SECONDS_TO_DISPLAY);

        $playerFirstHand->changeChips($chips);
        echo Message::getChipBalanceMessage($playerFirstHand);
        sleep(Message::SECONDS_TO_DISPLAY);

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
                    echo Message::getWinAndGetChipsMessage($playerSecondHand);
                } elseif (
                    $playerSecondHand->getStatus() === $playerSecondHand::LOSE ||
                    $playerSecondHand->getStatus() === $playerSecondHand::BURST
                ) {
                    $chips -= $playerSecondHand->getBets();
                    echo Message::getLoseAndLoseChipsMessage($playerSecondHand);
                } elseif ($playerSecondHand->getStatus() === $playerSecondHand::DRAW) {
                    // チップ残高の変更なし
                    echo Message::getDrawAndKeepChipsMessage($playerSecondHand);
                }
                sleep(Message::SECONDS_TO_DISPLAY);

                $player->changeChips($chips);
                echo Message::getChipBalanceMessage($playerSecondHand);
                sleep(Message::SECONDS_TO_DISPLAY);

                // スプリット宣言したプレイヤーのステータスリセット、2手目の削除
                $player->changeSplitStatus($playerSecondHand::NO_SPLIT);
                $game->removeSplitPlayer($playerSecondHand);
                break;
            }
        }
    }
}
