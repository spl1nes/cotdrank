<?php

use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Math\Statistic\Average;

$playername = $request->getData('name');

$query = new Builder($db);
$query->raw(
    'SELECT elo.elo_datetime, elo.elo_elo
    FROM elo
    left join Users on elo.elo_driver = Users.id
    WHERE Users.name = \'' . $playername . '\'
    ORDER BY elo.elo_datetime ASC;'
);
$elos = $query->execute()->fetchAll();
\array_shift($elos);

$query = new Builder($db);
$query->raw(
    'SELECT glicko.glicko_datetime, glicko.glicko_elo
    FROM glicko
    left join Users on glicko.glicko_driver = Users.id
    WHERE Users.name = \'' . $playername . '\'
    ORDER BY glicko.glicko_datetime ASC;'
);
$glickos = $query->execute()->fetchAll();
\array_shift($glickos);

$query = new Builder($db);
$query->raw(
    'SELECT glicko2.glicko2_datetime, glicko2.glicko2_elo
    FROM glicko2
    left join Users on glicko2.glicko2_driver = Users.id
    WHERE Users.name = \'' . $playername . '\'
    ORDER BY glicko2.glicko2_datetime ASC;'
);
$glicko2s = $query->execute()->fetchAll();
\array_shift($glicko2s);
?>

<script src="/../../Resources/chartjs/chart.js"></script>

<canvas class="chart" data-chart='
{
    "type": "line",
    "data": {
        "labels": [
            <?php
            echo \implode(',', \array_map(function ($var) {return '"' . \date('Y-m-d', $var['glicko2_datetime']) . '"';}, $glicko2s));
            ?>
        ],
        "datasets": [
            {
                "label": "Elo",
                "data": [
                    <?php \array_unshift($elos, ['elo_elo' => 1500], ['elo_elo' => 1500]);
                    echo \implode(',', \array_map(function ($var) {return \sprintf('%02d', $var);}, Average::totalMovingAverage(\array_map(function ($var) {return $var['elo_elo'];}, $elos), 3)));
                    ?>
                ],
                "yAxisID": "y",
                "fill": false,
                "tension": 0.0,
                "borderColor": "rgb(54, 162, 235)",
                "backgroundColor": "rgb(54, 162, 235)"
            },
            {
                "label": "Glicko1",
                "data": [
                    <?php \array_unshift($glickos, ['glicko_elo' => 1500], ['glicko_elo' => 1500]);
                    echo \implode(',', \array_map(function ($var) {return \sprintf('%02d', $var);}, Average::totalMovingAverage(\array_map(function ($var) {return $var['glicko_elo'];}, $glickos), 3)));
                    ?>
                ],
                "yAxisID": "y",
                "fill": false,
                "tension": 0.0,
                "borderColor": "rgb(46, 204, 113)",
                "backgroundColor": "rgb(46, 204, 113)"
            },
            {
                "label": "Glicko2",
                "data": [
                    <?php \array_unshift($glicko2s, ['glicko2_elo' => 1500], ['glicko2_elo' => 1500]);
                    echo \implode(',', \array_map(function ($var) {return \sprintf('%02d', $var);}, Average::totalMovingAverage(\array_map(function ($var) {return $var['glicko2_elo'];}, $glicko2s), 3)));
                    ?>
                ],
                "yAxisID": "y",
                "fill": false,
                "tension": 0.0,
                "borderColor": "rgb(204, 46, 46)",
                "backgroundColor": "rgb(204, 46, 46)"
            }
        ]
    },
    "options": {
        "responsive": true,
        "scales": {
            "y": {
                "title": {
                    "display": true,
                    "text": "Rating (Avg. 3 COTD)"
                },
                "display": true,
                "position": "left"
            }
        },
        "plugins": {
            "title": {
                "display": true,
                "text": "<?= \htmlspecialchars($playername); ?>"
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