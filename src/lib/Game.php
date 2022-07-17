<?php

namespace Blackjack;

require_once('Player.php');
require_once('Deck.php');

use Blackjack\Player;
use Blackjack\Deck;

class Game
{
    private const NUM_OF_CARDS_IN_HAND = 2;

    /**
     * コンストラクタ
     *
     * @param Deck $deck
     * @param Player $player
     * @param Player $dealer
     */
    public function __construct(
        private Deck $deck = new Deck(),
        private Player $player = new Player(),
        private Player $dealer = new Player()
    ) {
    }

    /**
     * deck プロパティを返す
     *
     * @return Deck $this->deck
     */
    public function getDeck()
    {
        return $this->deck;
    }

    /**
     * player プロパティを返す
     *
     * @return Player $this->player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * dealer プロパティを返す
     *
     * @return Player $this->dealer
     */
    public function getDealer()
    {
        return $this->dealer;
    }

    /**
     * ブラックジャックを開始する
     *
     * @return void
     */
    public function start()
    {
        // デッキを初期化する
        $this->deck->initDeck();

        // プレイヤーを初期化する
        // プレイヤーは手札を2枚引く
        $this->player->drawHand($this->deck);
        // デッキはカードを2枚取られる
        $this->deck->takeCard(self::NUM_OF_CARDS_IN_HAND);

        // ディーラーを初期化する
        // ディーラーは手札を2枚引く
        $this->dealer->drawHand($this->deck);
        // デッキはカードを2枚取られる
        $this->deck->takeCard(self::NUM_OF_CARDS_IN_HAND);

        // ブラックジャックの開始時メッセージを表示する
        $this->showStartMessage();

        $inputYesOrNo = trim(fgets(STDIN));
    }

    /**
     * ブラックジャックの開始時メッセージを表示する
     *
     * @return void
     */
    private function showStartMessage()
    {
        echo 'ブラックジャックを開始します。' . PHP_EOL;
        foreach ($this->player->getHand() as $card) {
            echo 'あなたの引いたカードは' .
                $card['suit'] . 'の' .
                $card['num'] . 'です。' . PHP_EOL;
        }
        echo 'ディーラーの引いたカードは' .
            $this->dealer->getHand()[0]['suit'] . 'の' .
            $this->dealer->getHand()[0]['num'] . 'です。' . PHP_EOL;

        echo 'ディーラーの引いた2枚目のカードはわかりません。' . PHP_EOL .
            PHP_EOL;

        $scoreTotal = $this->player->calcScoreTotal();
        echo 'あなたの現在の得点は' . $scoreTotal . 'です。カードを引きますか？（Y/N）' . PHP_EOL;
    }
}
