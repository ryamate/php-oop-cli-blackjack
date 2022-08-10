<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

/**
 * ノンプレイヤーキャラクタークラス
 */
class NonPlayerCharacter extends Player
{
    /**
     * ヒットかスタンドを Y/N で選択する（カードの合計値が17以上になるまで引き続ける）
     *
     * @return string $inputYesOrNo
     */
    protected function selectHitOrStand(): string
    {
        if ($this->getScoreTotal() < 17) {
            $inputYesOrNo = 'Y';
            echo 'Y' . PHP_EOL;
        } else {
            $inputYesOrNo = 'N';
            echo 'N' . PHP_EOL;
        }
        return $inputYesOrNo;
    }
}
