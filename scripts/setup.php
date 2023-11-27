<?php

include __DIR__ . '/../db.php';

$lastId = 0;

while (($comp = CompetitionMatchScoresMapper::get()
    ->where('id', $lastId, '>')
    ->sort('id', 'ASC')
    ->limit(1)
    ->execute())->id !== 0
) {
    $lastId = $comp->id;

    $match = CompetitionMatchesMapper::get()
        ->where('id', $comp->matchId)
        ->execute();

    if ($match->id === 0) {
        continue;
    }

    \preg_match('/\d{4}-\d{2}-\d{2}/', $match->name, $matches);

    $date = $matches[0] ?? '';
    if ($date === '') {
        continue;
    }

    $comp->grank = 64 * $match->position + $comp->rank;
    $comp->compId = $date;
    CompetitionMatchScoresMapper::update()->execute($comp);

    if ($lastId % 100000 === 0) {
        echo $lastId . "\n";
    }
}