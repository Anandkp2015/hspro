<?php

$array = array(true => 'a', 1 => 'b');
var_dump ($array);
die;
function some_func_name($from, $to) {
    $workingDays = [1, 2, 3, 4, 5]; # date format = N
    $workingHours = ['from' => ['08', '00'], 'to' => ['16', '00']];

    $start = new DateTime($from);
    $end = new DateTime($to);

    $startP = clone $start;
    $startP->setTime(0, 0, 0);
    $endP = clone $end;
    $endP->setTime(23, 59, 59);
    $interval = new DateInterval('P1D');
    $periods = new DatePeriod($startP, $interval, $endP);

    $sum = [];
    foreach ($periods as $i => $period) {
        if (!in_array($period->format('N'), $workingDays)) continue;

        $startT = clone $period;
        $startT->setTime($workingHours['from'][0], $workingHours['from'][1]);
        if (!$i && $start->diff($startT)->invert) $startT = $start;

        $endT = clone $period;
        $endT->setTime($workingHours['to'][0], $workingHours['to'][1]);
        if (!$end->diff($endT)->invert) $endT = $end;

        #echo $startT->format('Y-m-d H:i') . ' - ' . $endT->format('Y-m-d H:i') . "\n"; # debug

        $diff = $startT->diff($endT);
        if ($diff->invert) continue;
        foreach ($diff as $k => $v) {
            if (!isset($sum[$k])) $sum[$k] = 0;
            $sum[$k] += $v;
        }
    }

    if (!$sum) return 'ccc, no time on job?';

    $spec = "P{$sum['y']}Y{$sum['m']}M{$sum['d']}DT{$sum['h']}H{$sum['i']}M{$sum['s']}S";
    $interval = new DateInterval($spec);
    $startS = new DateTime;
    $endS = clone $startS;
    $endS->sub($interval);
    $diff = $endS->diff($startS);

    $labels = [
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];
    $return = [];
    foreach ($labels as $k => $v) {
        if ($diff->$k) {
            $return[] = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        }
    }

    return implode(', ', $return);
}


$from = '2018-11-16 15:00:00';
$to   = '2018-11-16 15:40:00';
echo some_func_name($from, $to);
?>