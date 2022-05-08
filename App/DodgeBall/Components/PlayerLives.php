<?php

namespace App\DodgeBall\Components;

/**
 *
 */
class PlayerLives
{
    /**
     * @var int
     */
    private int $lives;

    private int $totalLives;

    /**
     * @param int $lives
     */
    public function __construct(int $lives = 3)
    {
        $this->lives = $lives;
        $this->totalLives = $lives;
    }

    /**
     * @return void
     */
    public function removeOneLife(): void
    {
        $this->lives  -= 1;
    }

    /**
     * @return int
     */
    public function getLivesLeft(): int
    {
        return $this->lives;
    }

    public function restoreLives(): void
    {
        $this->lives = $this->totalLives;
    }

}