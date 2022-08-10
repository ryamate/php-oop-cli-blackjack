<?php

namespace Blackjack;

require_once('Player.php');
require_once('NonPlayerCharacter.php');
require_once('Dealer.php');

use Blackjack\Player;
use Blackjack\NonPlayerCharacter;
use Blackjack\Dealer;

/**
 * ゲームクラス
 */
class Game
{
    /**
     * コンストラクタ
     *
     * @param Dealer $dealer
     * @param Player $player
     * @param array<int,NonPlayerCharacter> $nPCs
     */
    public function __construct(
        private ?Dealer $dealer = null,
        private ?Player $player = null,
        private array $nPCs = [],
    ) {
        $this->dealer = $dealer ?? new Dealer('ディーラー');
        $this->player = $player ?? new Player('あなた');
    }

    /**
     * player プロパティを返す
     *
     * @return Player $this->player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * dealer プロパティを返す
     *
     * @return Dealer $this->dealer
     */
    public function getDealer(): Dealer
    {
        return $this->dealer;
    }

    /**
     * ブラックジャックの設定（人数）をする
     *
     * @return void
     */
    public function set()
    {
        $this->displaySettingMessage();

        $inputNumOfPlayer = '';

        while ($inputNumOfPlayer !== '1' && $inputNumOfPlayer !== '2' && $inputNumOfPlayer !== '3') {
            // プレイヤー人数について、 1, 2, 3 での入力を求める
            $inputNumOfPlayer = trim(fgets(STDIN));

            if ($inputNumOfPlayer === '1') {
                // 何もしない（NPC を new しない）
            } elseif ($inputNumOfPlayer === '2') {
                $this->nPCs[] = new NonPlayerCharacter('NPC1');
            } elseif ($inputNumOfPlayer === '3') {
                $this->nPCs[] = new NonPlayerCharacter('NPC1');
                $this->nPCs[] = new NonPlayerCharacter('NPC2');
            } else {
                $this->displaySettingInputErrorMessage();
            }
        }
    }

    /**
     * ブラックジャックを開始する
     *
     * @return void
     */
    public function start()
    {
        $this->dealer->dealOutFirstHand($this->player);
        $numOfNPC = count($this->nPCs);
        if ($numOfNPC > 0) {
            for ($i = 0; $i < $numOfNPC; $i++) {
                $this->dealer->dealOutFirstHand($this->nPCs[$i]);
            }
        }
        $this->dealer->dealOutFirstHand($this->dealer);

        $this->displayStartMessage();

        $this->player->action($this->dealer);
        if ($this->player->getStatus() === 'burst') {
            $this->displayLoseByBurstMessage($this->player);
        }
        if ($numOfNPC > 0) {
            for ($i = 0; $i < $numOfNPC; $i++) {
                $this->nPCs[$i]->action($this->dealer);
                if ($this->nPCs[$i]->getStatus() === 'burst') {
                    $this->displayLoseByBurstMessage($this->nPCs[$i]);
                }
            }
        }

        $this->dealer->judgeWinOrLose($this->player, $this->nPCs);

        $this->displayEndMessage();
    }

    /**
     * ブラックジャックの開始前の設定メッセージを表示する
     *
     * @return void
     */
    private function displaySettingMessage()
    {
        echo 'ブラックジャックの設定をします。' . PHP_EOL;
        echo 'プレイヤーの人数を選んでください。（1, 2, 3）' . PHP_EOL;
    }

    /**
     * Y/N 以外の値が入力された時のメッセージを表示する
     *
     * @return void
     */
    private function displaySettingInputErrorMessage()
    {
        echo '1, 2, 3 で入力してください。（1, 2, 3）' . PHP_EOL;
    }

    /**
     * ブラックジャックの開始時メッセージを表示する
     *
     * @return void
     */
    private function displayStartMessage()
    {
        echo 'ブラックジャックを開始します。' . PHP_EOL;

        foreach ($this->player->getHand() as $card) {
            echo $this->player->getName() . 'の引いたカードは' .
                $card['suit'] . 'の' . $card['num'] . 'です。' . PHP_EOL;
        }
        unset($card);
        echo PHP_EOL;

        $numOfNPC = count($this->nPCs);
        if ($numOfNPC > 0) {
            for ($i = 0; $i < $numOfNPC; $i++) {
                foreach ($this->nPCs[$i]->getHand() as $card) {
                    echo $this->nPCs[$i]->getName() . 'の引いたカードは' .
                        $card['suit'] . 'の' . $card['num'] . 'です。' . PHP_EOL;
                }
                unset($card);
                echo PHP_EOL;
            }
        }

        $dealersFirstCard = $this->dealer->getHand()[0];
        echo 'ディーラーの引いたカードは' .
            $dealersFirstCard['suit'] . 'の' . $dealersFirstCard['num'] . 'です。' . PHP_EOL;
        echo 'ディーラーの引いた2枚目のカードはわかりません。' . PHP_EOL .
            PHP_EOL;
    }

    /**
     * プレイヤーのカードの合計値が 21 を超え、プレイヤーの負けであることを伝えるメッセージを表示する
     *
     * @param Player $player
     * @return void
     */
    private function displayLoseByBurstMessage(Player $player): void
    {
        echo $player->getScoreTotalResultMessage($player);
        echo '合計値が21を超えたので、' . $player->getName() . 'の負けです。' . PHP_EOL
            . PHP_EOL;
    }

    /**
     * 終了時メッセージを表示する
     *
     * @return void
     */
    private function displayEndMessage(): void
    {
        echo 'ブラックジャックを終了します。' . PHP_EOL;
    }
}
