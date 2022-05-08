<?php

ini_set('memory_limit', '256M');

require_once '../../vendor/autoload.php';

use App\DodgeBall\Components\Lives;
use App\DodgeBall\Components\Player;
use App\DodgeBall\Components\ScoreKeeper;
use App\DodgeBall\Components\Team;
use PHPUnit\Framework\TestCase;


class DodgeBall extends TestCase {

    public function testPlayerID() {
        $lives = new Lives();
        $player = new Player(1, $lives);
        $this->assertEquals(1, $player->getIdentifier());
    }

    public function testPlayerOneThrowingBallToPlayerTwo() {
        $startWithLives = 3;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(1, $playerOneLives);
        $playerTwo = new Player(2, $playerTwoLives);

        $playerOne->hit($playerTwo);

        $this->assertEquals($playerTwo->getLivesLeft(), $startWithLives - 1);
    }

    public function testAssertPlayerIncorrectlyLivesAfterBeingHitOnce() {
        $startWithLives = 3;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(1, $playerOneLives);
        $playerTwo = new Player(2, $playerTwoLives);

        $playerOne->hit($playerTwo);

        $this->assertFalse($playerTwo->getLivesLeft() === ($startWithLives - 2));
    }

    public function testPlayerWereAllLivesHaveBeenDepleted() {
        $startWithLives = 2;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(1, $playerOneLives);
        $playerTwo = new Player(2, $playerTwoLives);

        $playerOne->hit($playerTwo);
        $playerOne->hit($playerTwo);

        $this->assertEquals(0, $playerTwo->getLivesLeft());
    }

    public function testIfMatchIsStillPlaying() {

        $startWithLives = 3;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(1, $playerOneLives);
        $playerTwo = new Player(1, $playerTwoLives);

        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        $teamOne->addPlayer($playerOne);
        $teamTwo->addPlayer($playerTwo);
        $scoreKeeper = new ScoreKeeper($teamOne, $teamTwo);

        $this->assertFalse($scoreKeeper->matchIsStillPlaying());
    }


    /**
     * @throws Exception
     */
    public function testLivesLeftAfterBeingHit() {
        $startWithLives = 1;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(1, $playerOneLives);
        $playerTwo = new Player(1, $playerTwoLives);

        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        $teamOne->addPlayer($playerOne);
        $teamTwo->addPlayer($playerTwo);

        $playerOne->hit($playerTwo);

        $this->assertSame(0, $playerTwo->getLivesLeft());

        $match = new ScoreKeeper($teamOne, $teamTwo);
        $match->begin();

        $this->assertSame($startWithLives, $playerTwo->getLivesLeft());
    }

    public function testIfPlayerGotRemovedFromTeamIfHeGotZeroLivesLeft() {
        $startWithLives = 2;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(1, $playerOneLives);
        $playerTwo = new Player(1, $playerTwoLives);

        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        $teamOne->addPlayer($playerOne);
        $teamTwo->addPlayer($playerTwo);

        $scoreKeeper = new ScoreKeeper($teamOne, $teamTwo);
        $scoreKeeper->begin();

        $scoreKeeper->teamOnePlayerHitAnotherTeamPlayer($playerOne, $playerTwo);
        $scoreKeeper->teamOnePlayerHitAnotherTeamPlayer($playerOne, $playerTwo);

        $this->assertEquals(0, $teamTwo->getTotalPlayersInTeam());
    }

    /**
     * @throws Exception
     */
    public function testWinningTeamIfThereIsNoPlayerLeft() {
        $startWithLives = 2;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(1, $playerOneLives);
        $playerTwo = new Player(1, $playerTwoLives);

        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        $teamOne->addPlayer($playerOne);
        $teamTwo->addPlayer($playerTwo);

        $scoreKeeper = new ScoreKeeper($teamOne, $teamTwo);
        $scoreKeeper->begin();

        $scoreKeeper->teamOnePlayerHitAnotherTeamPlayer($playerOne, $playerTwo);
        $scoreKeeper->teamTwoPlayerHitAnotherTeamPlayer($playerTwo, $playerOne);

        $this->assertSame($startWithLives - 1, $playerOne->getLivesLeft());

        $scoreKeeper->teamOnePlayerHitAnotherTeamPlayer($playerOne, $playerTwo);

        $this->assertTrue($scoreKeeper->matchHasEndeed());
        $this->assertFalse($scoreKeeper->matchIsStillPlaying());
        $this->assertEquals($teamOne, $scoreKeeper->getWinningTeam());
    }

    public function testToDetermineWinningTeamWithMostLivesLeftOnSuddenEndingMatch() {
        $startWithLives = 3;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(uniqid(true), $playerOneLives);
        $playerTwo = new Player(uniqid(true), $playerTwoLives);

        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        $teamOne->addPlayer($playerOne);
        $teamTwo->addPlayer($playerTwo);

        $scoreKeeper = new ScoreKeeper($teamOne, $teamTwo);
        $scoreKeeper->begin();

        $scoreKeeper->teamOnePlayerHitAnotherTeamPlayer($playerOne, $playerTwo);
        $scoreKeeper->teamOnePlayerHitAnotherTeamPlayer($playerOne, $playerTwo);

        $scoreKeeper->teamTwoPlayerHitAnotherTeamPlayer($playerTwo, $playerOne);

        $scoreKeeper->end();

        $this->assertEquals($startWithLives - 2, $playerTwo->getLivesLeft());
        $this->assertEquals($startWithLives - 1, $playerOne->getLivesLeft());

        $this->assertEquals($teamOne, $scoreKeeper->getWinningTeam());

    }

    /**
     * @throws Exception
     */
    public function testToDetermineWrongPlayersInWrongTeams() {
        $this->expectExceptionMessage('does not exists in team');
        $startWithLives = 3;

        $playerOneLives = new Lives($startWithLives);
        $playerTwoLives = new Lives($startWithLives);

        $playerOne = new Player(uniqid(true), $playerOneLives);
        $playerTwo = new Player(uniqid(true), $playerTwoLives);

        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        $teamOne->addPlayer($playerOne);
        $teamTwo->addPlayer($playerTwo);

        $scoreKeeper = new ScoreKeeper($teamOne, $teamTwo);
        $scoreKeeper->begin();

        $scoreKeeper->teamTwoPlayerHitAnotherTeamPlayer($playerOne, $playerTwo);
    }

}