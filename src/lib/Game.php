<?php

namespace Blackjack;

require_once('Player.php');
require_once('NonPlayerCharacter.php');
require_once('Dealer.php');

use Blackjack\Player;
use Blackjack\NonPlayerCharacter;
use Blackjack\Dealer;

class Game
{
    /**
     * コンストラクタ
     *
     * @param Dealer $dealer
     * @param Player $player
     * @param array<int,NonPlayerCharacter> $npc
     */
    public function __construct(
        private ?Dealer $dealer = null,
        private ?Player $player = null,
        private array $npc = [],
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
     * npc プロパティを返す
     *
     * @return array<int,NonPlayerCharacter> $this->npc
     */
    public function getNpc(): array
    {
        return $this->npc;
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
                $this->npc[] = new NonPlayerCharacter('NPC1');
            } elseif ($inputNumOfPlayer === '3') {
                $this->npc[] = new NonPlayerCharacter('NPC1');
                $this->npc[] = new NonPlayerCharacter('NPC2');
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

        $this->dealer->dealOutFirstHand($this->dealer);

        $numOfNpc = count($this->npc);
        if ($numOfNpc > 0) {
            for ($i = 0; $i < $numOfNpc; $i++) {
                $this->dealer->dealOutFirstHand($this->npc[$i]);
            }
        }

        $this->displayStartMessage();
        $this->displayProgressMessage($this->player);

        while ($this->player->getStatus() === 'hit') {
            // カードを引くか、 Y/N での入力を求める
            $inputYesOrNo = trim(fgets(STDIN));

            if ($inputYesOrNo === 'Y') {
                $this->dealer->dealOneCard($this->player);
                $this->dealer->checkBurst($this->player);

                $this->displayCardDrawnMessage($this->player);
                if ($this->player->getStatus() === 'hit') {
                    $this->displayProgressMessage($this->player);
                }
            } elseif ($inputYesOrNo === 'N') {
                $this->player->changeStatus('stand');
                echo PHP_EOL . PHP_EOL;
            } else {
                $this->displayInputErrorMessage();
            }
        }

        // プレイヤーのカードの合計値が 21 を超えていた場合
        if ($this->player->getStatus() === 'burst') {
            $this->displayLoseByBurstMessage($this->player);
        }

        // NPCがいたら、自分のカードの合計値が17以上になるまで引き続ける
        if ($numOfNpc > 0) {
            for ($i = 0; $i < $numOfNpc; $i++) {
                $this->displayProgressMessage($this->npc[$i]);

                while ($this->npc[$i]->getStatus() === 'hit') {
                    $inputYesOrNo = $this->npc[$i]->selectHitOrStand();
                    echo $inputYesOrNo . PHP_EOL;

                    if ($inputYesOrNo === 'Y') {
                        $this->dealer->dealOneCard($this->npc[$i]);
                        $this->dealer->checkBurst($this->npc[$i]);

                        $this->displayCardDrawnMessage($this->npc[$i]);
                        if ($this->npc[$i]->getStatus() === 'hit') {
                            $this->displayProgressMessage($this->npc[$i]);
                        }
                    } elseif ($inputYesOrNo === 'N') {
                        $this->npc[$i]->changeStatus('stand');
                        echo PHP_EOL . PHP_EOL;
                    }
                }
                // プレイヤーのカードの合計値が 21 を超えていた場合
                if ($this->npc[$i]->getStatus() === 'burst') {
                    $this->displayLoseByBurstMessage($this->npc[$i]);
                }
            }
        }

        $standingPlayers = [];
        if ($this->player->getStatus() === 'stand') {
            $standingPlayers[] = $this->player;
        };
        if ($numOfNpc > 0) {
            for ($i = 0; $i < $numOfNpc; $i++) {
                if ($this->npc[$i]->getStatus() === 'stand') {
                    $standingPlayers[] = $this->npc[$i];
                }
            }
        }

        // status => stand のプレイヤーが残っていたら
        if (count($standingPlayers) > 0) {
            $this->displayStandMessage();

            // ディーラーは自分のカードの合計値が17以上になるまで引き続ける
            $this->dealer->drawAfterAllPlayerStand();
            $this->dealer->checkBurst($this->dealer);
            $this->displayCardsDrawnByDealer();
            $this->displayDealerScoreTotalMessage();

            // ディーラーのカードの合計値が 21 を超えていた場合
            if ($this->dealer->getStatus() === 'burst') {
                $this->displayWinByBurstMessage($standingPlayers);
            } else {
                // 勝敗を判定する
                foreach ($standingPlayers as $standingPlayer) {
                    $this->dealer->judgeWinOrLose($standingPlayer);
                    $this->displayResultMessage($standingPlayer);
                }
            }
        }
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

        if (count($this->npc) > 0) {
            for ($i = 0; $i < count($this->npc); $i++) {
                # code...
                foreach ($this->npc[$i]->getHand() as $card) {
                    echo $this->npc[$i]->getName() . 'の引いたカードは' .
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
     * Y/N 以外の値が入力された時のメッセージを表示する
     *
     * @return void
     */
    private function displayInputErrorMessage()
    {
        echo 'Y/N で入力してください。カードを引きますか？（Y/N）' . PHP_EOL;
    }

    /**
     * 配られたカードを表示する
     *
     * @param Player $player
     * @return void
     */
    private function displayCardDrawnMessage(Player $player): void
    {
        $hand = $player->getHand();
        $cardDrawn = end($hand);
        echo $player->getName() . 'の引いたカードは' .
            $cardDrawn['suit'] . 'の' . $cardDrawn['num'] . 'です。' . PHP_EOL;
    }

    /**
     * プレイヤーのカードの合計値が 21 を超え、プレイヤーの負けであることを伝えるメッセージを表示する
     *
     * @param Player $player
     * @return void
     */
    private function displayLoseByBurstMessage(Player $player): void
    {
        echo $player->getName() . 'の現在の得点は' . $player->getScoreTotal() . 'です。' . PHP_EOL;
        echo '合計値が21を超えたので、' . $player->getName() . 'の負けです。' . PHP_EOL
            . PHP_EOL;
    }

    /**
     * ディーラーのカードの合計値が 21 を超え、プレイヤーの勝ちであることを伝えるメッセージを表示する
     *
     * @param array<int,Player> $standingPlayers
     * @return void
     */
    private function displayWinByBurstMessage(array $standingPlayers): void
    {
        echo '合計値が21を超えたので、ディーラーはバーストしました。' . PHP_EOL;
        foreach ($standingPlayers as $standingPlayer) {
            echo $standingPlayer->getName() . 'の勝ちです！' . PHP_EOL;
        }
    }

    /**
     * 引いたカード、現在の得点、カードを引くか、のメッセージを表示する
     *
     * @param Player $player
     * @return void
     */
    private function displayProgressMessage(Player $player): void
    {
        echo $player->getName() . 'の現在の得点は' . $player->getScoreTotal() .
            'です。カードを引きますか？（Y/N）' . PHP_EOL;
    }

    /**
     * これ以上カードを引かないと宣言した後のメッセージを表示する
     *
     * @return void
     */
    private function displayStandMessage(): void
    {
        $dealersHand = $this->dealer->getHand();
        $dealersSecondCard = end($dealersHand);
        echo 'ディーラーの引いた2枚目のカードは' .
            $dealersSecondCard['suit'] . 'の' .
            $dealersSecondCard['num'] . 'でした。' . PHP_EOL;
        echo 'ディーラーの現在の得点は' .
            $this->dealer->getScoreTotal() . 'です。' . PHP_EOL;
    }

    /**
     * ディーラーがカードの合計値が17以上になるまで引いた追加のカードを表示する
     *
     * @return void
     */
    private function displayCardsDrawnByDealer(): void
    {
        $dealersHand = $this->dealer->getHand();
        $cardsDrawnByDealer = array_slice($dealersHand, 2);
        foreach ($cardsDrawnByDealer as $card) {
            echo 'ディーラーの引いたカードは' .
                $card['suit'] . 'の' . $card['num'] . 'です。' . PHP_EOL;
        }
    }

    /**
     * プレイヤーの得点、勝敗の結果メッセージを表示する
     *
     * @param Player $player
     * @return void
     */
    private function displayResultMessage(Player $player): void
    {
        $playerName = $player->getName();
        echo $playerName . 'の得点は' . $player->getScoreTotal() . 'です。' . PHP_EOL;

        if ($player->getStatus() === 'win') {
            echo $playerName . 'の勝ちです！' . PHP_EOL;
        } elseif ($player->getStatus() === 'lose') {
            echo $playerName . 'の負けです…' . PHP_EOL;
        } elseif ($player->getStatus() === 'draw') {
            echo $playerName . 'は引き分けです。' . PHP_EOL;
        }
        echo PHP_EOL;
    }

    /**
     * プレイヤーの得点、勝敗の結果メッセージを表示する
     *
     * @param Player $player
     * @return void
     */
    private function displayDealerScoreTotalMessage(): void
    {
        echo 'ディーラーの得点は' . $this->dealer->getScoreTotal() . 'です。' . PHP_EOL;
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
