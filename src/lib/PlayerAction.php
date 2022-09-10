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
     * @param Deck $deck
     * @param Dealer $dealer
     * @return void
     */
    public function action(Deck $deck, Dealer $dealer): void;

    /**
     * ヒットかスタンドを Y/N で選択する
     *
     * @return string
     */
    public function selectHitOrStand(): string;
}
