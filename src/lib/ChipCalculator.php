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
        switch ($player->getSplitStatus()) {
            case $player::NO_SPLIT:
                $chipsByResult = $this->calcChipsByResult($player);
                $player->changeChips($chipsByResult);
                $this->displayMessageByResult($player);
                $player->reset();
                break;
            case $player::SPLIT_FIRST:
                echo Message::getSplitDeclarationMessage($player);
                $chipsByResult = $this->calcChipsByResult($player);
                $player->changeChips($chipsByResult);
                $this->displayMessageByResult($player);
                $player->reset();
                break;
            case $player::SPLIT_SECOND:
                $this->calcChipsSplitSecondHand($game, $player);
                $this->resetStatusSplitPlayer($game, $player);
                $this->removePlayerSecondHand($game, $player);
                break;
        }
    }

    /**
     * 結果別でチップを計算する
     *
     * @param Player $player
     * @return integer $chipsByResult
     */
    private function calcChipsByResult(Player $player): int
    {
        $chips = $player->getChips();

        switch ($player->getStatus()) {
            case $player::WIN:
                $chipsByResult = $chips + $player->getBets();
                break;
            case $player::LOSE:
            case $player::BURST:
                $chipsByResult = $chips - $player->getBets();
                break;
            case $player::DRAW:
                $chipsByResult = $chips;
                break;
        }
        return $chipsByResult;
    }

    /**
     * 結果別でメッセージを表示する
     *
     * @param Player $player
     * @return void
     */
    private function displayMessageByResult(Player $player): void
    {
        switch ($player->getStatus()) {
            case $player::WIN:
                echo Message::getWinAndGetChipsMessage($player);
                break;
            case $player::LOSE:
            case $player::BURST:
                echo Message::getLoseAndLoseChipsMessage($player);
                break;
            case $player::DRAW:
                echo Message::getDrawAndKeepChipsMessage($player);
                break;
        }
        sleep(Message::SECONDS_TO_DISPLAY);
        echo Message::getChipBalanceMessage($player);
        sleep(Message::SECONDS_TO_DISPLAY);
    }

    /**
     * スプリット宣言プレイヤーの2手目(splitStatus: 2) について、チップ残高を算出する
     *
     * @param Game $game
     * @param Player $playerSecondHand
     * @return void
     */
    private function calcChipsSplitSecondHand(Game $game, Player $playerSecondHand): void
    {
        foreach ($game->getPlayers() as $player) {
            if ($this->isSplitFirstHand($player, $playerSecondHand)) {
                $chipsByResult = $this->calcChipsByResult($player);
                $player->changeChips($chipsByResult);
                $this->displayMessageByResult($player);

                $game->removeSplitPlayer($playerSecondHand);
                break;
            }
        }
    }

    /**
     * スプリット宣言したプレイヤーのステータスをリセット削除する
     *
     * @param Game $game
     * @param Player $playerSecondHand
     * @return void
     */
    private function resetStatusSplitPlayer(Game $game, Player $playerSecondHand): void
    {
        foreach ($game->getPlayers() as $player) {
            if ($this->isSplitFirstHand($player, $playerSecondHand)) {
                $player->changeSplitStatus($player::NO_SPLIT);
                break;
            }
        }
    }

    /**
     * スプリット宣言したプレイヤーの 2 手目を削除する
     *
     * @param Game $game
     * @param Player $playerSecondHand
     * @return void
     */
    private function removePlayerSecondHand(Game $game, Player $playerSecondHand): void
    {
        foreach ($game->getPlayers() as $player) {
            if ($this->isSplitFirstHand($player, $playerSecondHand)) {
                $game->removeSplitPlayer($playerSecondHand);
                break;
            }
        }
    }

    /**
     * $player が、スプリット宣言プレイヤーの 1 手目か否かを判定する
     *
     * @param Player $player
     * @param Player $playerSecondHand
     * @return boolean
     */
    private function isSplitFirstHand(Player $player, Player $playerSecondHand): bool
    {
        if (
            $player->getName() === $playerSecondHand->getName() &&
            $player->getSplitStatus() === $playerSecondHand::SPLIT_FIRST &&
            $playerSecondHand->getSplitStatus() === $playerSecondHand::SPLIT_SECOND
        ) {
            return true;
        }
        return false;
    }
}
