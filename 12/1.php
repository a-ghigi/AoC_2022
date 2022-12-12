<?php

set_time_limit(0);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../_libs/kint.phar';
include '../../_libs/kint.php';

// Init vars
$input_file = 'input.txt';
$map = [];
$weight = [];

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    $rowIndex = 0;
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);

        // Add row to map
        $map[$rowIndex] = str_split($line);

        // Prepare for next row
        $rowIndex++;
    }

    fclose($handle);
}

d($map);

// Find size of map
$numRows = count($map);
$numColumns = count($map[0]);
d($numRows, $numColumns);

// Find start and end
$start = null;
$foundStart = false;
$end = null;
$foundEnd = false;
foreach ($map as $rowIndex => $row)
{
    foreach ($row as $columnIndex => $place)
    {
        if (!$foundStart AND $place == 'S')
        {
            $start = implode(',', [$rowIndex, $columnIndex]);
            $foundStart = true;
        }
        if (!$foundEnd AND $place == 'E')
        {
            $end = implode(',', [$rowIndex, $columnIndex]);
            $foundEnd = true;
        }
        if ($foundStart AND $foundEnd)
        {
            break 2;
        }
    }
}

d($start, $end);

// Build weight graph
foreach ($map as $rowIndex => $row)
{
    foreach ($row as $columnIndex => $place)
    {
        $placeCoordinates = implode(',', [$rowIndex, $columnIndex]);
        $placeHeight = height($rowIndex, $columnIndex);

        // Up
        if ($rowIndex > 0)
        {
            // It's not on the upper border

            $placeNextCoordinates = implode(',', [$rowIndex - 1, $columnIndex]);
            $placeNextHeight = height($rowIndex - 1, $columnIndex);
            $delta = $placeNextHeight - $placeHeight;
            //d('UP', $delta, $placeHeight, $placeNextHeight);

            if ($delta <= 1)
            {
                $weight[$placeCoordinates][$placeNextCoordinates] = 1;
            }
        }

        // Down
        if ($rowIndex < $numRows - 1)
        {
            // It's not on the upper border

            $placeNextCoordinates = implode(',', [$rowIndex + 1, $columnIndex]);
            $placeNextHeight = height($rowIndex + 1, $columnIndex);
            $delta = $placeNextHeight - $placeHeight;
            //d('DOWN', $delta, $placeHeight, $placeNextHeight);

            if ($delta <= 1)
            {
                $weight[$placeCoordinates][$placeNextCoordinates] = 1;
            }
        }

        // Right
        if ($columnIndex > 0)
        {
            // It's not on the upper border

            $placeNextCoordinates = implode(',', [$rowIndex, $columnIndex - 1]);
            $placeNextHeight = height($rowIndex, $columnIndex - 1);
            $delta = $placeNextHeight - $placeHeight;
            //d('RIGHT', $delta, $placeHeight, $placeNextHeight);

            if ($delta <= 1)
           {
                $weight[$placeCoordinates][$placeNextCoordinates] = 1;
            }
        }

        // Left
        if ($columnIndex < $numColumns - 1)
        {
            // It's not on the upper border

            $placeNextCoordinates = implode(',', [$rowIndex, $columnIndex + 1]);
            $placeNextHeight = height($rowIndex, $columnIndex + 1);
            $delta = $placeNextHeight - $placeHeight;
            //d('LEFT', $delta, $placeHeight, $placeNextHeight);

            if ($delta <= 1)
            {
                $weight[$placeCoordinates][$placeNextCoordinates] = 1;
            }
        }
    }
}

d($weight);

// Init Dijkstra
$visited = [];
$distanceFromStart = [];
for ($rowIndex = 0; $rowIndex < $numRows; $rowIndex++)
{
    for ($columnIndex = 0; $columnIndex < $numColumns; $columnIndex++)
    {
        $coord = implode(',', [$rowIndex, $columnIndex]);
        $distanceFromStart[$coord] = PHP_INT_MAX;
    }
}
$visited[$start] = 0;
$distanceFromStart[$start] = 0;

// Loop
//for ($i = 0; $i < 4; $i++)
do
{
    echo('<hr />' . PHP_EOL);

    // Find unvisited adjacent to all visited
    $unvisitedAdjacent = [];
    foreach ($visited as $visitedCoord => $dummy)
    {
        // Find unvisited nodes adjacent to this node
        if (array_key_exists($visitedCoord, $weight))
        {            
            // There are adjacent nodes
            foreach ($weight[$visitedCoord] as $adjacentCoord => $dummy2)
            {
                // Test if adjacent node is unvisited
                if (! array_key_exists($adjacentCoord, $visited))
                {
                    // It's unvisited, update distance 
                    $oldDistance = $distanceFromStart[$adjacentCoord];
                    $newDistance = $distanceFromStart[$visitedCoord] + $weight[$visitedCoord][$adjacentCoord];
                    $distanceFromStart[$adjacentCoord] = min($oldDistance, $newDistance);
    
                    // Add to list of unvisited adjacent nodes
                    $unvisitedAdjacent[$adjacentCoord] = true;
                }
            }
        }
    }

    if (count($unvisitedAdjacent))
    {
        // Pick closest unvisited adjacent node
        $closestCoord = null;
        $min = PHP_INT_MAX;
        foreach($unvisitedAdjacent as $adjacentCoord => $dummy3)
        {
            if ($distanceFromStart[$adjacentCoord] < $min)
            {
                $min = $distanceFromStart[$adjacentCoord];
                $closestCoord = $adjacentCoord;
            }
        }
    
        // Add closest unvisited to visited
        $visited[$closestCoord] = $distanceFromStart[$closestCoord];
        d($closestCoord);
    }
}
while (count($unvisitedAdjacent));

d($distanceFromStart[$end]);

function height($rowIndex, $columnIndex)
{
    global $map;

    $place = $map[$rowIndex][$columnIndex];

    if ($place == 'S')
    {
        $height = 0;
    }
    elseif ($place >= 'a' AND $place <= 'z')
    {
        $height = ord($place) - ord('a') + 1;
    }
    elseif ($place == 'E')
    {
        $height = ord('z') - ord('a') + 2;
    }
    else
    {
        echo('Errore (1');
        die();
    }

    return $height;
}