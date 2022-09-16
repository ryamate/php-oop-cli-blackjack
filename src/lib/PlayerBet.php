<?php

namespace Blackjack;

/**
 * プレイヤーベットインターフェイス
 */
interface PlayerBet
{
    /**
     * プレイヤーのタイプ別にチップをベットする行動を選択する
     *
     * @return void
     */
    public function bet(): void;

    /**
     * ベットする額を選択する
     *
     * @return string
     */
    public function selectBets(): string;
}
