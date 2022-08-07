<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

class NonPlayerCharacter extends Player
{
    /**
     * プレーヤーの順番が来たらカードの追加を行うかを選択する
     *
     * @return string $inputYesOrNo
     */
    public function selectHitOrStand(): string
    {
        if ($this->getScoreTotal() < 17) {
            $inputYesOrNo = 'Y';
        } else {
            $inputYesOrNo = 'N';
        }
        return $inputYesOrNo;
    }
}
