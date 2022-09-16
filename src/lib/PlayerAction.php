<?php

namespace Blackjack;

/**
 * プレイヤーアクションインターフェイス
 */
interface PlayerAction
{
    /**
     * プレイヤーのタイプ別にアクションを選択する
     *
     * @param Game $game
     * @return void
     */
    public function action(Game $game): void;

    /**
     * ヒットかスタンドを Y/N で選択する
     *
     * @return string
     */
    public function selectHitOrStand(): string;
}
