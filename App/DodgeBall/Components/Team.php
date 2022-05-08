<?php

namespace App\DodgeBall\Components;

class Team
{

    /**
     * @var string
     */
    private string $name;

    /**
     * @var int
     */
    private int $totalPlayersInTeam = 0;

    /**
     * @var Player[] array
     */
    private array $players;

    /**
     * @param string $name{
     */
    public function __construct(string $name)
    {
        $this->name = $name;

    }

    /**
     * @param Player $player
     * @return bool
     */
    public function playerExistsInTeam(Player $player): bool
    {
        return isset($this->players[$player->getIdentifier()]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addPlayer(Player $player): void
    {
        $this->players[$player->getIdentifier()] = $player;
        $this->totalPlayersInTeam += 1;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removePlayer(Player $player): void
    {
        unset($this->players[$player->getIdentifier()]);
        $this->totalPlayersInTeam -= 1;
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getTotalPlayersInTeam(): int
    {
        return $this->totalPlayersInTeam;
    }

    /**
     * @return int
     */
    public function getAllPlayersLivesLeft(): int
    {

        $teamTotalLivesLeft = 0;

        if ($this->teamHasNoPlayersLeft()) {
            return $teamTotalLivesLeft;
        }

        foreach ($this->players as $player) {
            $teamTotalLivesLeft += $player->getLivesLeft();
        }

        return $teamTotalLivesLeft;
    }

    /**
     * @return bool
     */
    public function teamHasNoPlayersLeft(): bool
    {
        return $this->getTotalPlayersInTeam() === 0;
    }

}