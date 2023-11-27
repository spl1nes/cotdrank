<?php

include_once __DIR__ . '/../../phpOMS/Autoloader.php';

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Database\Connection\SQLiteConnection;
use phpOMS\DataStorage\Database\DatabaseStatus;

class Driver
{
    public int $id = 0;
    public string $uid = '';
    public string $name = '';
}

class NullDriver extends Driver {}

class DriverMapper extends DataMapperFactory
{
    public const COLUMNS = [
        'id'   => ['name' => 'id',   'type' => 'int',    'internal' => 'id'],
        'uid' => ['name' => 'uid', 'type' => 'string', 'internal' => 'uid'],
        'name' => ['name' => 'name', 'type' => 'string', 'internal' => 'name'],
    ];

    public const TABLE = 'Users';
    public const PRIMARYFIELD = 'id';
}

class Elo
{
    public int $id = 0;
    public int $datetime = 0;
    public int $driver = 0;
    public int $elo = 1500;
}

class NullElo extends Elo {}

class EloMapper extends DataMapperFactory
{
    public const COLUMNS = [
        'elo_id'     => ['name' => 'elo_id',     'type' => 'int',    'internal' => 'id'],
        'elo_datetime'    => ['name' => 'elo_datetime',    'type' => 'int',    'internal' => 'datetime'],
        'elo_driver' => ['name' => 'elo_driver', 'type' => 'int', 'internal' => 'driver'],
        'elo_elo'    => ['name' => 'elo_elo',    'type' => 'int',    'internal' => 'elo'],
    ];

    public const TABLE = 'elo';
    public const PRIMARYFIELD = 'elo_id';
}

class Glicko
{
    public int $id = 0;
    public int $datetime = 0;
    public int $driver = 0;
    public int $elo = 1500;
    public int $rd = 50;
    public int $last_match = 0;
}

class NullGlicko extends Glicko {}

class GlickoMapper extends DataMapperFactory
{
    public const COLUMNS = [
        'glicko_id'     => ['name' => 'glicko_id',     'type' => 'int',    'internal' => 'id'],
        'glicko_datetime'    => ['name' => 'glicko_datetime',    'type' => 'int',    'internal' => 'datetime'],
        'glicko_driver' => ['name' => 'glicko_driver', 'type' => 'int', 'internal' => 'driver'],
        'glicko_elo'    => ['name' => 'glicko_elo',    'type' => 'int',    'internal' => 'elo'],
        'glicko_rd'     => ['name' => 'glicko_rd',     'type' => 'int',    'internal' => 'rd'],
        'glicko_last_match'     => ['name' => 'glicko_last_match',     'type' => 'int',    'internal' => 'last_match'],
    ];

    public const TABLE = 'glicko';
    public const PRIMARYFIELD = 'glicko_id';
}

class Glicko2
{
    public int $id = 0;
    public int $driver = 0;
    public int $datetime = 0;
    public int $elo = 1500;
    public float $vol = 0.06;
    public int $rd = 50;
}

class NullGlicko2 extends Glicko2 {}

class Glicko2Mapper extends DataMapperFactory
{
    public const COLUMNS = [
        'glicko2_id'     => ['name' => 'glicko2_id',     'type' => 'int',    'internal' => 'id'],
        'glicko2_datetime' => ['name' => 'glicko2_datetime', 'type' => 'int', 'internal' => 'datetime'],
        'glicko2_driver' => ['name' => 'glicko2_driver', 'type' => 'int', 'internal' => 'driver'],
        'glicko2_vol'    => ['name' => 'glicko2_vol',    'type' => 'float',  'internal' => 'vol'],
        'glicko2_elo'    => ['name' => 'glicko2_elo',    'type' => 'int',    'internal' => 'elo'],
        'glicko2_rd'     => ['name' => 'glicko2_rd',     'type' => 'int',    'internal' => 'rd'],
    ];

    public const TABLE = 'glicko2';
    public const PRIMARYFIELD = 'glicko2_id';
}

class CompetitionMatchScores
{
    public int $id = 0;
    public int $rank = 0;
    public int $grank = 0;
    public int $userId = 0;
    public int $matchId = 0;
    public string $compId = '';
}

class NullCompetitionMatchScores extends CompetitionMatchScores {}

class CompetitionMatchScoresMapper extends DataMapperFactory
{
    public const COLUMNS = [
        'Id'     => ['name' => 'Id',     'type' => 'int',    'internal' => 'id'],
        'Rank'     => ['name' => 'Rank',     'type' => 'int',    'internal' => 'rank'],
        'GlobalRank'     => ['name' => 'GlobalRank',     'type' => 'int',    'internal' => 'grank'],
        'UserId'     => ['name' => 'UserId',     'type' => 'int',    'internal' => 'userId'],
        'MatchId'     => ['name' => 'MatchId',     'type' => 'int',    'internal' => 'matchId'],
        'CompetitionId'     => ['name' => 'CompetitionId',     'type' => 'string',    'internal' => 'compId'],
    ];

    public const TABLE = 'CompetitionMatchScores';
    public const PRIMARYFIELD = 'Id';
}

class CompetitionMatches
{
    public int $id = 0;
    public int $nid = 0;
    public string $name = '';
    public int $position = 0;
}

class NullCompetitionMatches extends CompetitionMatches {}

class CompetitionMatchesMapper extends DataMapperFactory
{
    public const COLUMNS = [
        'Id'     => ['name' => 'Id',     'type' => 'int',    'internal' => 'id'],
        'NadeoId'     => ['name' => 'NadeoId',     'type' => 'int',    'internal' => 'nid'],
        'Name'     => ['name' => 'Name',     'type' => 'string',    'internal' => 'name'],
        'Position'     => ['name' => 'Position',     'type' => 'int',    'internal' => 'position'],
    ];

    public const TABLE = 'CompetitionMatches';
    public const PRIMARYFIELD = 'Id';
}

class Competitions
{
    public int $id = 0;
    public int $nid = 0;
    public string $lid = '';
    public string $name = '';
    public int $start = 0;
    public int $end = 0;
    public int $pcount = 0;
}

class NullCompetitions extends Competitions {}

class CompetitionsMapper extends DataMapperFactory
{
    public const COLUMNS = [
        'Id'     => ['name' => 'Id',     'type' => 'int',    'internal' => 'id'],
        'NadeoId'     => ['name' => 'NadeoId',     'type' => 'int',    'internal' => 'nid'],
        'LiveId'     => ['name' => 'LiveId',     'type' => 'string',    'internal' => 'lid'],
        'Name'     => ['name' => 'Name',     'type' => 'string',    'internal' => 'name'],
        'Start'     => ['name' => 'Start',     'type' => 'int',    'internal' => 'start'],
        'End'     => ['name' => 'End',     'type' => 'int',    'internal' => 'end'],
        'PlayerCount'     => ['name' => 'PlayerCount',     'type' => 'int',    'internal' => 'pcount'],
    ];

    public const TABLE = 'Competitions';
    public const PRIMARYFIELD = 'Id';
}

// DB connection
$db = new SQLiteConnection([
    'db' => 'sqlite',
    'database' => __DIR__ . '/cotdrank.sqlite',
]);

$db->connect();

if ($db->getStatus() !== DatabaseStatus::OK) {
    exit;
}

DataMapperFactory::db($db);