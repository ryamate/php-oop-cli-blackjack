<?php

namespace Blackjack;

require_once('Player.php');
require_once('Dealer.php');

use Blackjack\Player;
use Blackjack\Dealer;

/**
 * メッセージクラス
 */
class Message
{
    /**
     * ブラックジャックの開始前の設定メッセージを返す
     *
     * @return string
     */
    public static function getSettingMessage(): string
    {
        return 'ブラックジャックの設定をします。' . PHP_EOL;
    }

    /**
     * ブラックジャックの開始前の設定メッセージを返す
     *
     * @return string
     */
    public static function getInputNumOfPlayerMessage(): string
    {
        return 'プレイヤーの人数を選んでください。（1, 2, 3）' . PHP_EOL;
    }

    /**
     * Y/N 以外の値が入力された時のメッセージを返す
     *
     * @return string
     */
    public static function getSettingInputErrorMessage(): string
    {
        return '1, 2, 3 で入力してください。' . PHP_EOL;
    }

    /**
     * ブラックジャックの開始時メッセージを返す
     *
     * @return string
     */
    public static function getStartMessage(): string
    {
        return 'ブラックジャックを開始します。' . PHP_EOL;
    }

    /**
     * プレイヤーが最初に引いたカードについてのメッセージを返す
     *
     * @param Player $player
     * @return string $messages
     */
    public static function getFirstHandMessage(Player $player): string
    {
        $message = '';
        foreach ($player->getHand() as $card) {
            $message .= $player->getName() . 'の引いたカードは' .
                $card['suit'] . 'の' . $card['num'] . 'です。' . PHP_EOL;
        }
        unset($card);
        $message .= PHP_EOL;

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
     * 引いたカード、現在の得点、カードを引くか、のメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getProgressMessage(Player $player): string
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
        return  'カードを引きますか？（Y/N）' . PHP_EOL;
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
        $dealersHand = $dealerPlayer->getHand();
        $dealersSecondCard = end($dealersHand);
        $message = 'ディーラーの引いた2枚目のカードは' .
            $dealersSecondCard['suit'] . 'の' .
            $dealersSecondCard['num'] . 'でした。' . PHP_EOL;
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
        $message = $player->getName() . 'の勝ちです！' . PHP_EOL;
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
        $playerName = $player->getName();
        if ($player->getStatus() === 'win') {
            $message = $playerName . 'の勝ちです！' . PHP_EOL;
        } elseif ($player->getStatus() === 'lose') {
            $message = $playerName . 'の負けです…' . PHP_EOL;
        } elseif ($player->getStatus() === 'draw') {
            $message = $playerName . 'は引き分けです。' . PHP_EOL;
        }
        return $message;
    }

    /**
     * 終了時メッセージを返す
     *
     * @return string
     */
    public static function getEndMessage(): string
    {
        return 'ブラックジャックを終了します。' . PHP_EOL;
    }
}
