<?php

set_time_limit(0);

// Init vars
$input_file = 'input.txt';
$limit = 4000000;

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

// Split rows in chumks to avoid out of memory
$chunkSize = 100000;
for ($startOfChunk = 0; $startOfChunk <= $limit; $startOfChunk += $chunkSize)
{
    $endOfChunk = min($startOfChunk + $chunkSize - 1, $limit);

    // Collect horizontal ranges for current chumk of rows
    $rangesInRow = [];
    for ($rowIndex = $startOfChunk; $rowIndex <= $endOfChunk; $rowIndex++)
    {
        $rangesInRow[$rowIndex] = getRangesX($rowIndex);
    }
    
    // Find all points that don't intersect horizontal and vertical ranges for that point
    for ($columnIndex = 0; $columnIndex <= $limit; $columnIndex++)
    {
        $rangesInColumn = getRangesY($columnIndex);
    
        for ($rowIndex = $startOfChunk; $rowIndex <= $endOfChunk; $rowIndex++)
        {
            $checkedColumnIndex = checkValInRange($columnIndex, $rangesInRow[$rowIndex]);
            $checkedRowIndex = checkValInRange($rowIndex, $rangesInColumn);

            if ($columnIndex == $checkedColumnIndex AND $rowIndex == $checkedRowIndex)
            {
                // Point didn't move because it's not in horizzontal and vertical range
                // It's the solution!
                echo($columnIndex . ',' . $rowIndex . ' -> ' . ($columnIndex * 4000000 + $rowIndex));
                die();
            }
            else
            {
                // Skip all points within the vertical range
                $rowIndex = $checkedRowIndex;
            }
        }

        // Print progress
        if ($columnIndex % 200000 == 0)
        {
            echo(sprintf('%07d', $startOfChunk) . '-' . sprintf('%07d', $endOfChunk) . ',' . sprintf('%07d', $columnIndex) . ' - ' . date('c') . '<br />'. PHP_EOL);
            flush();
        }
    }
}



// ---- Functions -------------------------------------------------------------

function distance($aX, $aY, $bX, $bY)
{
    return (abs($aX - $bX) + abs($aY - $bY));
}

function getRangesX($rowY, $min = null, $max = null)
{
    global $sensors;

    return getRanges($sensors, $rowY, $min, $max);
}

function getRangesY($rowX)
{
    global $sensors;

    // Transpose sensor coordinates
    $transposedSensors = [];
    foreach ($sensors as $sensor)
    {
        [$sensorX, $sensorY] = explode(',', $sensor);
        $transposedSensors[] = $sensorY . ',' . $sensorX;
    }

    return getRanges($transposedSensors, $rowX);
}

function getRanges($sensors, $rowY, $min = null, $max = null)
{
    global $distance;

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

    // Restrict ranges to window (if specified)
    if ($min !== null AND $max !== null)
    {
        [$rangesLeftX, $rangesRightX] = getRangesInWindow($rangesLeftX, $rangesRightX, $min, $max);
    }

    // Create range tuples
    $ranges = [];
    foreach ($rangesLeftX as $index => $rangeLeftX)
    {
        $rangeRightX = $rangesRightX[$index];

        $ranges[] = [$rangeLeftX, $rangeRightX];
    }

    // Sort range tuples
    usort($ranges, 'compareRanges');

    // Join adjacent ranges
    for ($index = 0; $index < count($ranges) - 1; $index++)
    {
        if ($ranges[$index][1] == $ranges[$index + 1][0] - 1)
        {
            // They are adjacent

            // Join them and discard old left range
            $ranges[$index + 1][0] = $ranges[$index][0];
            unset($ranges[$index]);
        }
    }

    // Split sorted range tuples
    $sortedRangesLeftX = [];
    $sortedRangesRightX = [];
    foreach ($ranges as $index => $range)
    {
        $sortedRangesLeftX[] = $range[0];
        $sortedRangesRightX[] = $range[1];
        unset($ranges[$index]);
    }

    return [$sortedRangesLeftX, $sortedRangesRightX];
}

function compareRanges($a, $b)
{
    return $a[0] <=> $b[0];
}

function getRangesInWindow($rangesLeftX, $rangesRightX, $min, $max)
{
    $rangesInWindowLeftX = [];
    $rangesInWindowRightX = [];

    foreach ($rangesLeftX as $index => $rangeLeftX)
    {
        $rangeRightX = $rangesRightX[$index];

        if ($rangeRightX < $min)
        {
            // It's out of the window, on the left

            // Discard it
        }
        elseif($rangeLeftX > $max)
        {
            // It's out of the window, on the right

            // Discard it
        }
        else
        {
            // Overlaps the window

            // Add the part within the window
            $rangesInWindowLeftX[] = max($rangeLeftX, $min);
            $rangesInWindowRightX[] = min($rangeRightX, $max);
        }
    }

    return [$rangesInWindowLeftX, $rangesInWindowRightX];
}

function checkValInRange($val, $ranges)
{
    // Check every range
    foreach ($ranges[0] as $index => $left)
    {
        $right = $ranges[1][$index];

        if ($val >= $left AND $val <= $right)
        {
            // It's in the range

            // Move point to end of range
            return $right;
        }
    }

    return $val;
}