<?php

use phpOMS\DataStorage\Database\Query\Builder;

$query = new Builder($db);
$query->raw(
    'SELECT Users.name, ' . $system . '.' . $system . '_elo
    FROM ' . $system . '
    left join Users on ' . $system . '.' . $system . '_driver = Users.id
    WHERE (' . $system . '_driver, ' . $system . '_datetime) IN (
        SELECT ' . $system . '_driver, MAX(' . $system . '_datetime) AS max_datetime
        FROM ' . $system . '
        where ' . $system . '_datetime > 1688162399
        GROUP BY ' . $system . '_driver
    )
    group by ' . $system . '_driver
    order by ' . $system . '_elo desc
    limit 10000;'
);
$drivers = $query->execute()->fetchAll();
?>
<table>
    <thead>
        <tr>
            <td>Rank</td>
            <td>Name</td>
            <td>Rating</td>
        </tr>
    </thead>
    <tbody>
        <?php $rank = 0; foreach ($drivers as $driver) : ++$rank; ?>
        <tr>
            <td><?= $rank; ?>
            <td><?= \htmlspecialchars($driver['name']); ?>
            <td><?= (int) $driver['' . $system . '_elo']; ?>
        <?php endforeach; ?>
    </tbody>
</table>