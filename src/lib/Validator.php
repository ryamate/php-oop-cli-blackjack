<?php

namespace Blackjack;

trait Validator
{
    /**
     * ベットする額の入力を検証する
     *
     * @param string $input
     * @return string $error
     */
    public function validateInputBets(string $input): string
    {
        $error = '';
        // 未入力の場合
        // 整数ではない、もしくは、チップ残高以上、上限 1000 以上の場合
        if (!isset($input) || strlen($input) === 0) {
            $error = 'ベット額は 1 〜 1000 ドル（チップ残高以下）で入力してください。' . PHP_EOL;
        } elseif (
            !preg_match('/^-?[0-9]+$/', $input) ||
            (int)$input < 1 || $this->getChips() < (int)$input || 1000 < (int)$input
        ) {
            $error = 'ベット額は 1 〜 1000 ドル（チップ残高以下）で入力してください。' . PHP_EOL;
        }
        return $error;
    }
}
