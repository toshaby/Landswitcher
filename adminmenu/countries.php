<?php
$db = Shop::Container()->getDB();

$rez = \Plugin\landswitcher\Models\ModelRedirect::loadAll($db, [], []);

foreach($rez as $item)
    var_dump($item);