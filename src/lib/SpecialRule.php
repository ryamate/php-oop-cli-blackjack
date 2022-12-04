<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

class SpecialRule
{
    use Validator;


    public const DOUBLE_DOWN = 'DD';
    public const SPLIT = 'SP';
    public const SURRENDER = 'SR';

    /**
     * 特殊ルールを適用する
     *
     * @param string $inputYesOrNo
     * @param Game $game
     * @param ManualPlayer|AutoPlayer $player
     * @return string $message
     */
    public function applySpecialRule(string $inputYesOrNo, Game $game, ManualPlayer|AutoPlayer $player): string
    {
        $message = '';
        switch ($inputYesOrNo) {
            case self::DOUBLE_DOWN:
                $message = $this->doubleDown(
                    $game->getDeck(),
                    $game->getDealer(),
                    $player
                );
                break;
            case self::SPLIT:
                $message = $this->split($game, $player);
                break;
            case self::SURRENDER:
                $message = $this->surrender($player);
                break;
            default:
                $message .= Message::getInputErrorMessage();
                break;
        }
        return $message;
    }

    /**
     * 入力値が特殊ルールであるかどうかを判定する
     *
     * @param string $inputYesOrNo
     * @return boolean
     */
    public function isSpecialRule(string $inputYesOrNo): bool
    {
        if (
            $inputYesOrNo === self::DOUBLE_DOWN ||
            $inputYesOrNo === self::SPLIT ||
            $inputYesOrNo === self::SURRENDER
        ) {
            return true;
        }
        return false;
    }

    /**
     * ダブルダウン(Double Down)
     * 最初に賭けた賭け金と同額を追加して3枚目のカードを追加する。
     * また、ダブルダウンを宣言するとヒットは1回のみ行う。
     *
     * @param Deck $deck
     * @param Dealer $dealer
     * @param Player $player
     * @return string
     */
    private function doubleDown(Deck $deck, Dealer $dealer, Player $player): string
    {
        $message = '';
        $firstBets = $player->getBets();
        $doubleDownBets = $firstBets * 2;
        $player->changeBets($doubleDownBets);
        $player->changeStatus($player::STAND);
        $message .= 'ダブルダウンを宣言しました。' . PHP_EOL .
            '最初に賭けたチップ ' . $firstBets . ' ドルと同額を追加して、チップ ' . $doubleDownBets . ' ドルを賭けます。 ' . PHP_EOL .
            'ヒットは 1 回のみ行い 3 枚目のカードを追加します。' . PHP_EOL;

        $dealer->dealOneCard($deck, $player);
        $dealer->getJudge()->checkBurst($player);
        $message .= Message::getCardDrawnMessage($player);
        if ($player->hasStandStatus()) {
            $message .= Message::getScoreTotalMessage($player);
        }
        return $message;
    }

    /**
     * スプリット(Split)
     * 最初に配られたカードが同数の場合、カードを2つに分けてそれぞれ別のハンドとしてゲームをプレイする。
     * 最初に掛けた金額と同額の掛け金が必要となる。
     *
     * @param Game $game
     * @param ManualPlayer|AutoPlayer $player
     * @return string
     */
    private function split(Game $game, ManualPlayer|AutoPlayer $player): string
    {
        $message = '';
        $message = $this->validateSplitConditions($player);
        if ($message === '') {
            $secondPlayer = clone $player;

            // $player → 手札の1枚目を持つ, $playerSplit → 手札の2枚目を持つ
            $player->changeHand(array_slice($player->getHand(), 0, 1));
            $secondPlayer->changeHand(array_slice($secondPlayer->getHand(), 1));

            $game->addPlayerAsSecondHand($secondPlayer);

            $player->changeSplitStatus($player::SPLIT_FIRST);
            $secondPlayer->changeSplitStatus($player::SPLIT_SECOND);

            foreach ([$player, $secondPlayer] as $playerAfterSplit) {
                $game->getDealer()->dealOneCard($game->getDeck(), $playerAfterSplit);
                echo $playerAfterSplit->getSplitStatus() . ' 手目: ' .
                    Message::getCardDrawnMessage($playerAfterSplit);
                echo $playerAfterSplit->getSplitStatus() . ' 手目: ' .
                    Message::getScoreTotalMessage($playerAfterSplit);
            }

            foreach ([$player, $secondPlayer] as $playerAfterSplit) {
                // 一度だけ、HITかSTANDかを選択する
                while ($playerAfterSplit->hasHitStatus()) {
                    echo $playerAfterSplit->getSplitStatus() . ' 手目: ' . 'カードを引きますか？（Y/N）' . PHP_EOL;
                    $inputYesOrNo = $playerAfterSplit->selectHitOrStand();
                    $error = $this->validateInputYesOrNo($inputYesOrNo);
                    if ($error === '') {
                        if ($inputYesOrNo === 'Y') {
                            $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' .
                                'ヒットを宣言しました。（カードを引きます）' . PHP_EOL;

                            $game->getDealer()->dealOneCard($game->getDeck(), $playerAfterSplit);
                            $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' .
                                Message::getCardDrawnMessage($playerAfterSplit);

                            $playerAfterSplit->changeStatus($player::STAND);
                            $game->getDealer()->getJudge()->checkBurst($playerAfterSplit);
                            $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' .
                                Message::getScoreTotalMessage($playerAfterSplit);
                        } elseif ($inputYesOrNo === 'N') {
                            $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' .
                                'スタンドを宣言しました。（カードを引きません）' . PHP_EOL;

                            $playerAfterSplit->changeStatus($player::STAND);
                            $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' .
                                Message::getScoreTotalMessage($playerAfterSplit) . PHP_EOL;
                        }
                    } else {
                        echo $error . PHP_EOL;
                        $inputYesOrNo = '';
                    }
                    echo $message;
                    $message = '';
                }
            }
        }
        return $message;
    }

    /**
     * サレンダー(Surrender)
     * サレンダーを宣言したら、掛け金の半分を戻してもらいゲームを降りる。
     *
     * @param ManualPlayer|AutoPlayer $player
     * @return string
     */
    private function surrender(ManualPlayer|AutoPlayer $player): string
    {
        $message = '';
        $firstBets = $player->getBets();
        $surrenderBets = $firstBets - bcmul("0.5", (string)$player->getBets(), 0);
        $player->changeBets($surrenderBets);
        $player->changeStatus($player::LOSE);
        $message .= 'サレンダーを宣言しました。' . PHP_EOL .
            'ベットしたチップ' . $firstBets . 'ドルの半分' . $surrenderBets . 'ドルを戻してゲームを降ります。' . PHP_EOL;
        return $message;
    }
}
