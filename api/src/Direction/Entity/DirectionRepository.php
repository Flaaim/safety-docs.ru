<?php

namespace App\Direction\Entity;

interface DirectionRepository
{
    public function getBySlug(string $slug): Direction;
}