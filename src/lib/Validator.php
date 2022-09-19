<?php

namespace Blackjack;

trait Validator
{
    /**
     * Y/N の入力を検証する
     *
     * @param string $input
     * @return string $error
     */
    public function validateInputYesOrNo(string $input): string
    {
        $error = '';
        if ($input !== 'Y' && $input !== 'N') {
            $error = 'Y/N で入力してください。' . PHP_EOL;
        }
        return $error;
    }

    /**
     * プレイヤー人数の入力を検証する
     *
     * @param string $input
     * @param int $maxNumOfPlayers
     * @return string $error
     */
    public function validateInputNumOfPlayer(string $input, int $maxNumOfPlayers): string
    {
        $error = '';
        if (strlen($input) === 0) {
            $error = 'プレイヤーの人数は 1〜' . $maxNumOfPlayers . ' の半角数字を入力してください。' . PHP_EOL;
        } elseif (
            !preg_match('/^-?[0-9]+$/', $input) ||
            (int)$input < 1 || $maxNumOfPlayers < (int)$input
        ) {
            $error = 'プレイヤーの人数は 1〜' . $maxNumOfPlayers . ' の半角数字で入力してください。' . PHP_EOL;
        }
        return $error;
    }

    /**
     * ベットする額の入力を検証する
     *
     * @param string $input
     * @param ManualPlayer|AutoPlayer $player
     * @return string $error
     */
    public function validateInputBets(string $input, ManualPlayer|AutoPlayer $player): string
    {
        $error = '';
        if (strlen($input) === 0) {
            $error = 'ベット額は 1 〜 1000 （チップ残高以下）の半角数字を入力してください。' . PHP_EOL;
        } elseif (
            !preg_match('/^-?[0-9]+$/', $input) ||
            (int)$input < 1 || $player->getChips() < (int)$input || 1000 < (int)$input
        ) {
            $error = 'ベット額は 1 〜 1000 （チップ残高以下）の半角数字で入力してください。' . PHP_EOL;
        }
        return $error;
    }

    /**
     * スプリットを宣言する条件を満たしているかを検証する
     *
     * @param ManualPlayer|AutoPlayer $player
     * @return string $error
     */
    public function validateSplitConditions(ManualPlayer|AutoPlayer $player): string
    {
        $error = '';
        if ($player->getHand()[0]['score'] !== $player->getHand()[1]['score']) {
            $error = 'スプリットを宣言するには、最初に配られたカードが同数である必要があります。' . PHP_EOL;
        } elseif ($player->getChips() < ($player->getBets() * 2)) {
            $error = 'スプリットを宣言するための、チップ残高が足りません。' . PHP_EOL;
        }
        return $error;
    }
}
