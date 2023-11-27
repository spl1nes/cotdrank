<?php

include __DIR__ . '/../db.php';

use phpOMS\Algorithm\Rating\Elo as RatingElo;
use phpOMS\Algorithm\Rating\Glicko1;
use phpOMS\Algorithm\Rating\Glicko2 as RatingGlicko2;

$lastDateTime = 0;
$MAX_ELO_CHANGE = 300;

function createResultMatrixForRating($driver, $drivers) {
    $result = [];
    foreach ($drivers as $match) {
        if ($driver->userId === $match->userId) {
            continue;
        }

        if ($driver->grank < $match->grank) {
            $result[$match->userId] = 1;
        } elseif ($driver->grank === $match->grank) {
            $result[$match->userId] = 0.5;
        } else {
            $result[$match->userId] = 0;
        }
    }

    return $result;
}

function getOps($driver, $drivers) {
    $ops = [];
    foreach ($drivers as $id => $d) {
        if ($driver === $id) {
            continue;
        }

        $ops[$id] = $d;
    }

    return $ops;
}

$eloAlgorithm = new RatingElo();
$glickoAlgorithm = new Glicko1();
$glicko2Algorithm = new RatingGlicko2();

$start_date = new DateTime('2020-11-01');
$end_date = new DateTime('2023-11-18');

$current_date = clone $start_date;

// We assume there are not more than 8 concurrent matches of maximum of 16 players in one match
while ($current_date->getTimestamp() < $end_date->getTimestamp()) {
    $current_date->modify('+1 day');
    $lastDateTime = clone $current_date;

    $matches = CompetitionMatchScoresMapper::getAll()
        ->where('compId', $lastDateTime->format('Y-m-d'))
        ->execute();

    if (empty($matches)) {
        continue;
    }

    $driverIds = [];

    $elo = [];
    $glicko = [];
    $glicko2 = [];

    foreach ($matches as $match) {
        //$user = DriverMapper::get()->where('id', $match->userId)->execute();

        $driverIds[] = $match->userId;

        // Set up ratings incl. default ratings if not available
        $elo[$match->userId] = EloMapper::get()
            ->where('driver', $match->userId)
            ->where('datetime', $lastDateTime->getTimestamp(), '<=')
            ->sort('datetime', 'DESC')
            ->limit(1)
            ->execute();

        if ($elo[$match->userId]->id === 0) {
            $rating = new Elo();
            $rating->driver = $match->userId;
            $rating->datetime = 0;
            EloMapper::create()->execute($rating);
            $elo[$match->userId] = $rating;
        }

        $glicko[$match->userId] = GlickoMapper::get()
            ->where('driver', $match->userId)
            ->where('datetime', $lastDateTime->getTimestamp(), '<=')
            ->sort('datetime', 'DESC')
            ->limit(1)
            ->execute();

        if ($glicko[$match->userId]->id === 0) {
            $rating = new Glicko();
            $rating->driver = $match->userId;
            $rating->datetime = 0;
            GlickoMapper::create()->execute($rating);
            $glicko[$match->userId] = $rating;
        }

        $glicko2[$match->userId] = Glicko2Mapper::get()
            ->where('driver', $match->userId)
            ->where('datetime', $lastDateTime->getTimestamp(), '<=')
            ->sort('datetime', 'DESC')
            ->limit(1)
            ->execute();

        if ($glicko2[$match->userId]->id === 0) {
            $rating = new Glicko2();
            $rating->driver = $match->userId;
            $rating->datetime = 0;
            Glicko2Mapper::create()->execute($rating);
            $glicko2[$match->userId] = $rating;
        }
    }

    // There can be multiple matches at a given datetime
    $eloTemp = [];
    $glickoTemp = [];
    $glicko2Temp = [];

    foreach ($matches as $match) {
        $eloTemp[$match->userId] = $elo[$match->userId];
        $glickoTemp[$match->userId] = $glicko[$match->userId];
        $glicko2Temp[$match->userId] = $glicko2[$match->userId];
    }

    foreach ($matches as $match) {
        $results = createResultMatrixForRating($match, $matches);

        // Handle elo
        $ops = getOps($match->userId, $eloTemp);
        $rating = $eloAlgorithm->rating(
            $eloTemp[$match->userId]->elo,
            \array_map(function ($vars) {
                return $vars->elo;
            }, $ops),
            $results
        );

        $newRating = new Elo();
        $newRating->driver = $match->userId;
        $newRating->datetime = $lastDateTime->getTimestamp();
        $newRating->elo = (int) \max(\min($eloTemp[$match->userId]->elo + ($rating['elo'] - $eloTemp[$match->userId]->elo) / 10, $eloTemp[$match->userId]->elo + $MAX_ELO_CHANGE), $eloTemp[$match->userId]->elo - $MAX_ELO_CHANGE);
        EloMapper::create()->execute($newRating);

        // Handle glicko
        $ops = getOps($match->userId, $glickoTemp);
        $rating = $glickoAlgorithm->rating(
            $glickoTemp[$match->userId]->elo,
            $glickoTemp[$match->userId]->rd,
            $glickoTemp[$match->userId]->last_match,
            (int) ($lastDateTime->getTimestamp() / (60 * 60 * 24)),
            \array_map(function ($vars) {
                return $vars->elo;
            }, $ops),
            $results,
            \array_map(function ($vars) {
                return $vars->rd;
            }, $ops),
        );

        $newRating = new Glicko();
        $newRating->driver = $match->userId;
        $newRating->datetime = $lastDateTime->getTimestamp();
        $newRating->elo = (int) \max(\min($glickoTemp[$match->userId]->elo + ($rating['elo'] - $glickoTemp[$match->userId]->elo) / 10, $glickoTemp[$match->userId]->elo + $MAX_ELO_CHANGE), $glickoTemp[$match->userId]->elo - $MAX_ELO_CHANGE);
        $newRating->rd = $rating['rd'];
        $newRating->last_match = (int) ($lastDateTime->getTimestamp() / (60 * 60 * 24));
        GlickoMapper::create()->execute($newRating);

        // Handle glicko2
        $ops = getOps($match->userId, $glicko2Temp);
        $rating = $glicko2Algorithm->rating(
            $glicko2Temp[$match->userId]->elo,
            $glicko2Temp[$match->userId]->rd,
            $glicko2Temp[$match->userId]->vol,
            \array_map(function ($vars) {
                return $vars->elo;
            }, $ops),
            $results,
            \array_map(function ($vars) {
                return $vars->rd;
            }, $ops),
        );

        $newRating = new glicko2();
        $newRating->driver = $match->userId;
        $newRating->datetime = $lastDateTime->getTimestamp();
        $newRating->elo = (int) \max(\min($glicko2Temp[$match->userId]->elo + ($rating['elo'] - $glicko2Temp[$match->userId]->elo) / 10, $glicko2Temp[$match->userId]->elo + $MAX_ELO_CHANGE), $glicko2Temp[$match->userId]->elo - $MAX_ELO_CHANGE);
        $newRating->rd = $rating['rd'];
        $newRating->vol = $rating['vol'];
        Glicko2Mapper::create()->execute($newRating);
    }
}
