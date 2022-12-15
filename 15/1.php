<?php

// Init vars
$input_file = 'input.txt';
$rowY = 2000000;

$sensors = [];
$beacons = [];
$distance = [];

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $matches = [];
        if (preg_match('/Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)/', trim($line), $matches) === 1)
        {
            $sensors[] = $matches[1] . ',' . $matches[2];
            $beacons[] = $matches[3] . ',' . $matches[4];
            $distance[] = distance($matches[1], $matches[2], $matches[3], $matches[4]);
        }
    }

    fclose($handle);
}

// Find all sensor fields intersecting examined row
$rangesLeftX = [];
$rangesRightX = [];
foreach ($sensors as $index => $sensor)
{
    // Get sensor coordinates
    [$sensorX, $sensorY] = explode(',', $sensor);

    $delta = $distance[$index] - abs($rowY - $sensorY);
    if ($delta >= 0)
    {
        // There's an intersection
        $intersectionLeftX = $sensorX - $delta;
        $intersectionRightX = $sensorX + $delta;
    
        // Add it to intersections with examined row
        foreach ($rangesLeftX as $index => $rangeLeftX)
        {
            $rangeRightX = $rangesRightX[$index];
    
            // Check if intersection overlaps range
            if ($rangeLeftX > $intersectionRightX OR $rangeRightX < $intersectionLeftX)
            {
                // They don't overlap
    
                // Do nothing
            }
            else
            {
                // They do overlap
    
                // Join them in new intersection
                $intersectionLeftX = min($intersectionLeftX, $rangeLeftX);
                $intersectionRightX = max($intersectionRightX, $rangeRightX);
    
                // Discard old range
                unset($rangesLeftX[$index]);
                unset($rangesRightX[$index]);
            }
        }

        // Add intersection to ranges
        $rangesLeftX[] = $intersectionLeftX;
        $rangesRightX[] = $intersectionRightX;
    }
}

// Collect beacons in examined row, discarding duplicates
$beaconsInRowX = [];
foreach ($beacons as $beacon)
{
    [$beaconX, $beaconY] = explode(',', $beacon);

    if ($beaconY == $rowY AND ! in_array($beaconX, $beaconsInRowX))
    {
        // Add it
        $beaconsInRowX[] = $beaconX;
    }
}

// Sum range widths
$result = 0;
foreach ($rangesLeftX as $index => $rangeLeftX)
{
    $rangeRightX = $rangesRightX[$index];

    $result += $rangeRightX - $rangeLeftX + 1;
}

// Discard points in ranges that are beacons
foreach ($beaconsInRowX as $beaconInRowX)
{
    foreach ($rangesLeftX as $index => $rangeLeftX)
    {
        $rangeRightX = $rangesRightX[$index];

        if ($beaconInRowX >= $rangeLeftX AND $beaconInRowX <= $rangeRightX)
        {
            // It's in a range
            $result--;
        }
    }
}

echo($result);


// ---- Functions -------------------------------------------------------------

function distance($aX, $aY, $bX, $bY)
{
    return (abs($aX - $bX) + abs($aY - $bY));
}