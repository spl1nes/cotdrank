<?php

use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Math\Statistic\Average;

$players = ['CarlJr.', 'AffiTM', 'mime.cut', 'AR_Mudda', 'GranaDy.', 'Massa.4PF', 'Otaaaq', 'BrenTM'];

foreach ($players as $playername) {
    $query = new Builder($db);
    $query->raw(
        'SELECT ' . $system . '.' . $system . '_datetime, ' . $system . '.' . $system . '_elo
        FROM ' . $system . '
        left join Users on ' . $system . '.' . $system . '_driver = Users.id
        WHERE Users.name = \'' . $playername . '\'
        ORDER BY ' . $system . '.' . $system . '_datetime ASC;'
    );
    $player[$playername] = $query->execute()->fetchAll();
}

$start_date = new DateTime('2020-11-02');
$end_date = new DateTime('2023-11-18');

$playerdata = [];

foreach ($player as $name => $p) {
    $playerdata[$name] = [];

    $temp_date = clone $start_date;
    $previous = \reset($p);
    $previousscore = \array_fill(0, 10, $previous['' . $system . '_elo']);

    foreach ($p as $index => $data) {
        $temp_date->modify('+1 day');

        while ($temp_date->getTimestamp() < $data['' . $system . '_datetime']) {
            $previous['' . $system . '_datetime'] = $temp_date->getTimestamp();
            $playerdata[$name][] = $previous;

            $temp_date->modify('+1 day');
        }

        \array_shift($previousscore);
        $previousscore[] = $data['' . $system . '_elo'];

        $data['' . $system . '_elo'] = \array_sum($previousscore) / \count($previousscore);
        $playerdata[$name][] = $data;

        $previous = $data;
    }

    while ($temp_date->getTimestamp() < $end_date->getTimestamp()) {
        $previous['' . $system . '_datetime'] = $temp_date->getTimestamp();
        $playerdata[$name][] = $previous;

        $temp_date->modify('+1 day');
    }

    \array_shift($playerdata[$name]);
}

?>

<script src="/../../Resources/chartjs/chart.js"></script>

<canvas class="chart" data-chart='
{
    "type": "line",
    "data": {
        "labels": [
            <?php
            echo \implode(',', \array_map(function ($var) use($system) {return '"' . \date('Y-m-d', $var['' . $system . '_datetime']) . '"';}, $playerdata[$name]));
            ?>
        ],
        "datasets": [
            <?php $index = 0; foreach ($playerdata as $name => $data) : ++$index; ?>
            {
                "label": "<?= $name; ?>",
                "data": [
                    <?php //\array_unshift($data, ['' . $system . '_elo' => 1500], ['' . $system . '_elo' => 1500]);
                    //echo \implode(',', \array_map(function ($var) {return \sprintf('%02d', $var);}, Average::totalMovingAverage(\array_map(function ($var) use($system) {return $var['' . $system . '_elo'];}, $data), 3)));
                    echo \implode(',', \array_map(function ($var) use($system) {return \sprintf('%02d', $var['' . $system . '_elo']);}, $data));
                    ?>
                ],
                "yAxisID": "y",
                "fill": false,
                "tension": 0.1,
                "pointRadius": 0
            }
            <?= $index === \count($player) ? '' : ','; ?>
            <?php endforeach; ?>
        ]
    },
    "options": {
        "responsive": true,
        "scales": {
            "y": {
                "title": {
                    "display": true,
                    "text": "Rating (Avg. 10 COTD)"
                },
                "display": true,
                "position": "left"
            }
        },
        "plugins": {
            "title": {
                "display": true,
                "text": "COTD Player <?= \ucfirst($system); ?> Comparison"
            }
        }
    }
}
'
></canvas>

<script>
const chart = document.getElementsByTagName('canvas')[0];
const data = JSON.parse(chart.getAttribute('data-chart'));
const myChart = new Chart(chart.getContext('2d'), data);
</script>
