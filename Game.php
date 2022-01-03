<?php
function echoln($string) {
    echo $string."\n";
}

class Game {
    private array $players;
    private array $places;
    private array $purses ;
    private array $inPenaltyBox ;

    private array $popQuestions;
    private array $scienceQuestions;
    private array $sportsQuestions;
    private array $rockQuestions;

    private int $currentPlayer = 0;
    private $isGettingOutOfPenaltyBox;

    function  __construct(){

        $this->initEmptyArrays();
        $this->initQuestions();
    }

    private function createRockQuestion($index): string
    {
        return "Rock Question " . $index;
    }

    public function add($playerName): void
    {
        array_push($this->players, $playerName);
        $this->initNewPlayer();
        echoln($playerName . " was added");
        echoln("They are player number " . $this->howManyPlayers());
    }

    private function howManyPlayers(): int
    {
        return count($this->players);
    }

    public function  roll(int $roll) {
        echoln($this->getCurrentPlayer() . " is the current player");
        echoln("They have rolled a " . $roll);

        if ($this->isPenaltyBox()) {
            if ($roll % 2 != 0) {
                $this->isGettingOutOfPenaltyBox = true;
                echoln($this->getCurrentPlayer() . " is getting out of the penalty box");
                $this->changePlayerPlace($roll);
            } else {
                echoln($this->getCurrentPlayer() . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }

        } else {

            $this->changePlayerPlace($roll);
        }

    }

    function  askQuestion() {
        if ($this->currentCategory() == "Pop")
            echoln(array_shift($this->popQuestions));
        if ($this->currentCategory() == "Science")
            echoln(array_shift($this->scienceQuestions));
        if ($this->currentCategory() == "Sports")
            echoln(array_shift($this->sportsQuestions));
        if ($this->currentCategory() == "Rock")
            echoln(array_shift($this->rockQuestions));
    }


    function currentCategory(): string
    {
        $category = "Rock";
        if ($this->getPlayerCurrentPlace() % 2 == 0)
        {
            $category = "Sports";
        }

        if ($this->getPlayerCurrentPlace() % 2 != 0)
        {
            $category = "Science";
        }

        if ($this->getPlayerCurrentPlace() % 4 == 0)
        {
            $category = "Pop";
        }
        return $category;
    }

    function wasCorrectlyAnswered(): bool
    {
        if ($this->isPenaltyBox()){
            if ($this->isGettingOutOfPenaltyBox) {
                return $this->actionForCorrectAnswer();
            } else {
                $this->currentPlayer++;
                $this->checkAndResetLastPlayer();
                return true;
            }
        } else
        {

            return $this->actionForCorrectAnswer();
        }
    }

    function wrongAnswer(): bool
    {
        echoln("Question was incorrectly answered");
        echoln($this->getCurrentPlayer() . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;
        $this->currentPlayer++;
        $this->checkAndResetLastPlayer();
        return true;
    }


    function didPlayerWin(): bool
    {
        return !($this->purses[$this->currentPlayer] == 6);
    }

    private function initQuestions()
    {
        for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, ("Science Question " . $i));
            array_push($this->sportsQuestions, ("Sports Question " . $i));
            array_push($this->rockQuestions, $this->createRockQuestion($i));
        }
    }

    private function initNewPlayer()
    {
        $this->places[$this->howManyPlayers()] = 0;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->inPenaltyBox[$this->howManyPlayers()] = false;
    }

    private function initEmptyArrays(): void
    {
        $this->players = [];
        $this->places = [0];
        $this->purses = [0];
        $this->inPenaltyBox = [0];

        $this->popQuestions = [];
        $this->scienceQuestions = [];
        $this->sportsQuestions = [];
        $this->rockQuestions = [];
    }

    /**
     * @return mixed
     */
    private function isPenaltyBox()
    {
        return (bool)$this->inPenaltyBox[$this->currentPlayer];
    }

    /**
     * @param int $roll
     */
    private function changePlayerPlace(int $roll): void
    {
        $this->places[$this->currentPlayer] = $this->getPlayerCurrentPlace() + $roll;
        if ($this->getPlayerCurrentPlace() > 11) $this->places[$this->currentPlayer] = $this->getPlayerCurrentPlace() - 12;

        echoln($this->getCurrentPlayer()
            . "'s new location is "
            . $this->getPlayerCurrentPlace());
        echoln("The category is " . $this->currentCategory());
        $this->askQuestion();
    }

    /**
     * @return mixed
     */
    private function getCurrentPlayer()
    {
        return $this->players[$this->currentPlayer];
    }

    /**
     * @return mixed
     */
    private function getPlayerCurrentPlace()
    {
        return $this->places[$this->currentPlayer];
    }

    /**
     * @return bool
     */
    private function actionForCorrectAnswer(): bool
    {
        echoln("Answer was correct!!!!");
        $this->purses[$this->currentPlayer]++;
        echoln($this->getCurrentPlayer()
            . " now has "
            . $this->purses[$this->currentPlayer]
            . " Gold Coins.");

        $winner = $this->didPlayerWin();
        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }

        return $winner;
    }

    private function checkAndResetLastPlayer(): void
    {
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }
    }
}
