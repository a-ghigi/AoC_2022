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

// Normalize starts and find end
$starts = [];
$end = null;
foreach ($map as $rowIndex => $row)
{
    foreach ($row as $columnIndex => $place)
    {
        if ($map[$rowIndex][$columnIndex] == 'S')
        {
            $map[$rowIndex][$columnIndex] = 'a';
        }
        if ($place == 'E')
        {
            $end = implode(',', [$rowIndex, $columnIndex]);
        }
    }
}

d($starts, $end);

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

            if ($delta >= -1)
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

            if ($delta >= -1)
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

            if ($delta >= -1)
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

            if ($delta >= -1)
            {
                $weight[$placeCoordinates][$placeNextCoordinates] = 1;
            }
        }
    }
}

d($weight);

echo('<hr />' . PHP_EOL);

// Init Dijkstra
$visited = [];
$distanceFromEnd = [];
for ($rowIndex = 0; $rowIndex < $numRows; $rowIndex++)
{
    for ($columnIndex = 0; $columnIndex < $numColumns; $columnIndex++)
    {
        $coord = implode(',', [$rowIndex, $columnIndex]);
        $distanceFromEnd[$coord] = PHP_INT_MAX;
    }
}
$visited[$end] = 0;
$distanceFromEnd[$end] = 0;

// Loop
do
{
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
                    $oldDistance = $distanceFromEnd[$adjacentCoord];
                    $newDistance = $distanceFromEnd[$visitedCoord] + $weight[$visitedCoord][$adjacentCoord];
                    $distanceFromEnd[$adjacentCoord] = min($oldDistance, $newDistance);

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
            if ($distanceFromEnd[$adjacentCoord] < $min)
            {
                $min = $distanceFromEnd[$adjacentCoord];
                $closestCoord = $adjacentCoord;
            }
        }
    
        // Add closest unvisited to visited
        $visited[$closestCoord] = $distanceFromEnd[$closestCoord];
        //d($end, $closestCoord);

        [$closestRow, $closestCol] = explode(',', $closestCoord);
        if ($map[$closestRow][$closestCol] == 'a')
        {
            break;
        }
    }
}
while (count($unvisitedAdjacent));

echo($distanceFromEnd[$visitedCoord]);


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