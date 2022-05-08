<?php

namespace App\DodgeBall\Components;

/**
 *
 */
class ScoreKeeper
{
    /**
     * @var bool
     */
    private bool $isPlaying = false;

    /**
     * @var Team
     */
    private Team $teamOne;

    /**
     * @var Team
     */
    private Team $teamTwo;

    /**
     * @var Team|null
     */
    private ?Team $winningTeam;


    /**
     * @param Team $teamOne
     * @param Team $teamTwo
     */
    public function __construct(Team $teamOne, Team $teamTwo)
    {
        $this->teamOne = $teamOne;
        $this->teamTwo = $teamTwo;
    }

    /**
     * @return void
     */
    public function begin(): void
    {
        if ($this->matchIsStillPlaying()) {
            return;
        }

        $this->resetAllScores($this->teamOne);
        $this->resetAllScores($this->teamTwo);
        $this->isPlaying = true;
    }

    /**
     * @param Player $playerOne
     * @param Player $playerTwo
     * @return void
     * @throws \Exception
     */
    public function teamOnePlayerHitAnotherTeamPlayer(Player $playerOne, Player $playerTwo): void
    {
        $this->attackingTeam($this->teamOne, $playerOne, $this->teamTwo, $playerTwo);
    }

    /**
     * @param Player $playerOne
     * @param Player $playerTwo
     * @return void
     * @throws \Exception
     */
    public function teamTwoPlayerHitAnotherTeamPlayer(Player $playerOne, Player $playerTwo): void
    {
        $this->attackingTeam($this->teamTwo, $playerOne, $this->teamOne, $playerTwo);
    }

    /**
     * @param Team $attackerTeam
     * @param Player $attacker
     * @param Team $dodgeTeam
     * @param Player $dodgePlayer
     * @return void
     * @throws \Exception
     */
    private function attackingTeam(Team $attackerTeam, Player $attacker, Team $dodgeTeam, Player $dodgePlayer): void
    {
        $this->beforeAttackingTeamValidateActions($attackerTeam, $attacker, $dodgeTeam, $dodgePlayer);

        $attacker->hit($dodgePlayer);
        if ($dodgePlayer->hasZeroLivesLeft() === false) {
            return;
        }

        $dodgeTeam->removePlayer($dodgePlayer);

        if ($dodgeTeam->teamHasNoPlayersLeft()) {
            $this->end();
            $this->winningTeam = $attackerTeam;
        }

    }


    /**
     * @return void
     */
    public function end(): void
    {
        $this->isPlaying = false;
        $this->determineWinningTeam();
    }

    public function determineWinningTeam(): void
    {
        if (!empty($this->winningTeam)) {
            return;
        }

        $teamOneLivesLeft = $this->teamOne->getAllPlayersLivesLeft();
        $teamTwoLivesLeft = $this->teamTwo->getAllPlayersLivesLeft();

        if ($teamOneLivesLeft === $teamTwoLivesLeft) {
            return;
        }

        if ($teamOneLivesLeft > $teamTwoLivesLeft) {
            $this->winningTeam = $this->teamOne;
            return;
        }

        $this->winningTeam = $this->teamTwo;
    }

    /**
     * @return bool
     */
    public function matchIsStillPlaying():bool
    {
        return $this->isPlaying === true;
    }

    /**
     * @return bool
     */
    public function matchHasEndeed(): bool
    {
        return $this->isPlaying === false;
    }

    /**
     * @param Team $team
     * @return void
     */
    public function resetAllScores(Team $team): void
    {
        if ($this->matchIsStillPlaying()) {
            return;
        }

        foreach ($team->getPlayers() as $player) {
            $player->restoreLivesToOriginal();
        }
    }

    /**
     * @return Team|null
     */
    public function getWinningTeam(): ?Team
    {
        if ($this->matchIsStillPlaying() || empty($this->winningTeam)) {
            return null;
        }

        return $this->winningTeam;
    }

    /**
     * @param Team $attackerTeam
     * @param Player $attacker
     * @param Team $dodgeTeam
     * @param Player $dodgePlayer
     * @return void
     * @throws \Exception
     */
    public function beforeAttackingTeamValidateActions(Team $attackerTeam, Player $attacker, Team $dodgeTeam, Player $dodgePlayer): void
    {
        if ($this->matchHasEndeed()) {
            throw new \Exception('Match is over or did not even start, illegal throw');
        }

        if (!$attackerTeam->playerExistsInTeam($attacker)) {
            throw new \Exception('Player with ID ' . $attacker->getIdentifier() . ' does not exists in team ' . $attackerTeam->getName());
        }

        if (!$dodgeTeam->playerExistsInTeam($dodgePlayer)) {
            throw new \Exception('Player with ID ' . $attacker->getIdentifier() . ' does not exists in dodge team ' . $attackerTeam->getName());
        }
    }

}