<?php

namespace App\Repositories;

use App\Models\Game;

class GameRepository extends BaseRepository
{
    public function __construct(Game $game)
    {
        parent::__construct($game);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
