<?php

namespace App\DodgeBall\Components;

/**
 *
 */
class Player
{

    /**
     * @var string
     */
    private string $id;

    /**
     * @var Lives
     */
    private Lives $lives;

    /**
     * @param int $id
     * @param Lives $lives
     */
    public function __construct(string $id, Lives $lives)
    {
        $this->id = $id;
        $this->lives = $lives;
    }

    /**
     * @param Player $playerTwo
     * @return void
     */
    public function hit(Player $playerTwo): void
    {
        $playerTwo->lives->removeOneLife();
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLivesLeft(): int
    {
        return $this->lives->getLivesLeft();
    }

    /**
     * @return bool
     */
    public function hasZeroLivesLeft(): bool
    {
        return $this->getLivesLeft() === 0;
    }

    /**
     * @return void
     */
    public function restoreLivesToOriginal(): void
    {
        $this->lives->restoreLives();
    }

}