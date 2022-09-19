<?php

namespace Blackjack;

require_once('Player.php');
require_once('DealerPlayer.php');

use Blackjack\Player;
use Blackjack\DealerPlayer;

/**
 * メッセージクラス
 */
class Message
{
    /**
     * Y/N 以外の値が入力された時のメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getPlaceYourBetsMessage(Player $player): string
    {
        $message = '';
        $message .= $player->getName() . 'の持っているチップは' . $player->getChips() . 'ドルです。' . PHP_EOL
            . 'ベットする額を入力してください。（1〜1000ドル）' . PHP_EOL .
            '💲 ';
        return $message;
    }

    /**
     * ディーラーが最初に引いたカードについてのメッセージを返す
     *
     * @param DealerPlayer $dealerPlayer
     * @return string $message
     */
    public static function getDealerFirstHandMessage(DealerPlayer $dealerPlayer): string
    {
        $message = '';
        $dealersFirstCard = $dealerPlayer->getHand()[0];
        $message .= 'ディーラーの引いたカードは' .
            $dealersFirstCard['suit'] . 'の' . $dealersFirstCard['num'] . 'です。' . PHP_EOL;
        $message .= 'ディーラーの引いた2枚目のカードはわかりません。' . PHP_EOL . PHP_EOL;
        return $message;
    }

    /**
     * プレイヤーのカードの合計値が 21 を超え、プレイヤーの負けであることを伝えるメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getLoseByBurstMessage(Player $player): string
    {
        $message = self::getScoreTotalResultMessage($player);
        $message .= '合計値が21を超えたので、バーストしました。' . $player->getName() . 'は負けです…' . PHP_EOL
            . PHP_EOL;
        return $message;
    }

    /**
     * 現在の得点 のメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getScoreTotalMessage(Player $player): string
    {
        return $player->getName() . 'の現在の得点は' . $player->getScoreTotal() . 'です。' . PHP_EOL;
    }

    /**
     * カードを引くか、のメッセージを返す
     *
     * @return string
     */
    public static function getProgressQuestionMessage(): string
    {
        return  'カードを引きますか？（Y/N / DD/SP/SR）' . PHP_EOL .
            '※ 特殊ルール（DD: ダブルダウン, SP: スプリット, SR: サレンダー）は、最初に手札が配られたときのみ有効' . PHP_EOL .
            '👉 ';
    }

    /**
     * 配られたカードのメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getCardDrawnMessage(Player $player): string
    {
        $hand = $player->getHand();
        $cardDrawn = end($hand);
        $message = $player->getName() . 'の引いたカードは' .
            $cardDrawn['suit'] . 'の' . $cardDrawn['num'] . 'です。' . PHP_EOL;
        return $message;
    }

    /**
     * Y/N 以外の値が入力された時のメッセージを返す
     *
     * @return string
     */
    public static function getInputErrorMessage(): string
    {
        return 'Y/N で入力してください。' . PHP_EOL;
    }

    /**
     * プレイヤーの得点結果メッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getScoreTotalResultMessage(Player $player): string
    {
        $message = $player->getName() . 'の得点は' . $player->getScoreTotal() . 'です。' . PHP_EOL;
        return $message;
    }

    /**
     * これ以上カードを引かないと宣言した後のメッセージを返す
     *
     * @param DealerPlayer $dealerPlayer
     * @return string $message
     */
    public static function getStandMessage(DealerPlayer $dealerPlayer): string
    {
        $dealersSecondCard = $dealerPlayer->getHand()[1];
        $message = 'ディーラーの引いた2枚目のカードは' .
            $dealersSecondCard['suit'] . 'の' . $dealersSecondCard['num'] . 'でした。' . PHP_EOL;
        return $message;
    }

    /**
     * ディーラーのカードの合計値が 21 を超え、プレイヤーの勝ちであることを伝えるメッセージを返す
     *
     * @return string $message
     */
    public static function getDealerBurstMessage(): string
    {
        $message = '合計値が21を超えたので、ディーラーはバーストしました。' . PHP_EOL . PHP_EOL;
        return $message;
    }

    /**
     * ディーラーのカードの合計値が 21 を超え、プレイヤーの勝ちであることを伝えるメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getWinByBurstMessage(Player $player): string
    {
        $message = '';
        $splitStatus = $player->getSplitStatus();
        $playerName = $player->getName();
        if ($splitStatus === $player::NO_SPLIT) {
            $message = $playerName . 'の勝ちです！🎉' . PHP_EOL;
        } elseif ($splitStatus === $player::SPLIT_FIRST) {
            $message = $playerName . '(1手目)の勝ちです！🎉' . PHP_EOL;
        } elseif ($splitStatus === $player::SPLIT_SECOND) {
            $message = $playerName . '(2手目)の勝ちです！🎉' . PHP_EOL;
        }
        return $message;
    }

    /**
     * プレイヤーの勝敗結果メッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getResultMessage(Player $player): string
    {
        $message = '';
        $status = $player->getStatus();
        $splitStatus = $player->getSplitStatus();
        $playerName = $player->getName();
        if ($splitStatus === $player::SPLIT_FIRST) {
            $playerName = $playerName . '(1手目)';
        } elseif ($splitStatus === $player::SPLIT_SECOND) {
            $playerName = $playerName . '(2手目)';
        }

        if ($status === $player::WIN) {
            $message = $playerName . 'の勝ちです！🎉' . PHP_EOL;
        } elseif ($status === $player::LOSE) {
            $message = $playerName . 'の負けです…' . PHP_EOL;
        } elseif ($status === $player::DRAW) {
            $message = $playerName . 'は引き分けです。' . PHP_EOL;
        }
        return $message;
    }
}
