<?php

set_time_limit(0);

// Init vars
$input_file = 'input.txt';

// Rocks' sprites
$rocks = [];
$rockWidths = [];

$rocks[0][0] = str_split('####');
$rockWidths[] = count($rocks[0][0]);

$rocks[1][] = str_split('.#.');
$rocks[1][] = str_split('###');
$rocks[1][] = str_split('.#.');
$rockWidths[] = count($rocks[1][0]);

$rocks[2][] = str_split('###');     // Watch out, it's drawn upside down
$rocks[2][] = str_split('..#');
$rocks[2][] = str_split('..#');
$rockWidths[] = count($rocks[2][0]);

$rocks[3][] = str_split('#');
$rocks[3][] = str_split('#');
$rocks[3][] = str_split('#');
$rocks[3][] = str_split('#');
$rockWidths[] = count($rocks[3][0]);

$rocks[4][] = str_split('##');
$rocks[4][] = str_split('##');
$rockWidths[] = count($rocks[4][0]);

$numRocks = count($rocks);

// Find left and right border of rocks
$xLeft = [];
$xRight = [];
foreach ($rocks as $rockIndex => $rock)
{
    // Check each row
    foreach ($rock as $rowIndex => $row)
    {
        // Find left side of row
        $xInRock = null;
        for ($columnIndex = 0; $columnIndex < $rockWidths[$rockIndex]; $columnIndex++)
        {
            if ($row[$columnIndex] == '#')
            {
                $xInRock = $columnIndex;
                break;
            }
        }
        if ($xInRock === null)
        {
            echo('Error (2)');
            die();
        }
        $xLeft[$rockIndex][$rowIndex] = $xInRock;

        // Find right side of row
        $xInRock = null;
        for ($columnIndex = $rockWidths[$rockIndex] - 1; $columnIndex >= 0; $columnIndex--)
        {
            if ($row[$columnIndex] == '#')
            {
                $xInRock = $columnIndex;
                break;
            }
        }
        if ($xInRock === null)
        {
            echo('Error (3)');
            die();
        }
        $xRight[$rockIndex][$rowIndex] = $xInRock;
    }
}

// Find bottom of rocks
$yBottom = [];
foreach ($rocks as $rockIndex => $rock)
{
    // Check if bottom of any rock column hits something
    for ($columnIndex = 0; $columnIndex < $rockWidths[$rockIndex]; $columnIndex++)
    {
        // Find rock column bottom
        $bottom = null;
        foreach ($rock as $rowIndex => $row)
        {
            if ($rock[$rowIndex][$columnIndex] == '#')
            {
                // Found bottom
                $bottom = $rowIndex;
                break;
            }
        }
        if ($bottom === null)
        {
            echo('Error (4)');
            die();
        }
        $yBottom[$rockIndex][$columnIndex] = $bottom;
    }
}

// Prepare chamber
$numColumns = 7;
for ($columnIndex = 0; $columnIndex < $numColumns; $columnIndex++)
{
    $columns[$columnIndex] = [];
}

// Load input
$moves = [];
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);

        if ($line)
        {
            $moves = str_split($line);
        }
        $moveIndex = 0;
        $numMoves = count($moves);
    }

    fclose($handle);
}

// Rocks begin to fall
$rockIndex = 0;
$countRock = 0;
$state = [];
$height = [];
$height[0] = 0;
$beforeLoop = null;
$beginLoop = null;
$endLoop = null;
do
{
    $countRock++;

    // Finds current maximum row index
    $maxRowIndex = getMaxRowIndex();

    // Rocks appears
    $x = 2;    // -1 + 2 + 1      -1 = left wall
    $y = $maxRowIndex + 3 + 1;

    $blocked = false;
    do
    {
        // Pick current move
        $move = $moves[$moveIndex];
        switch ($move)
        {
            case '<':
                $deltaX = -1;
                break;
            case '>':
                $deltaX = 1;
                break;
            default:
                echo ('Error (1)');
                die();
                break;
        }
        
        // Check if it can move horizontally
        if ($x + $deltaX >= 0 AND $x + $deltaX + $rockWidths[$rockIndex] <= $numColumns)
        {
            // It's not blocked by the walls

            if ($deltaX == -1)
            {
                // Rock moves left, check if it would hit rocks
                $blockedOnTheLeft = false;

                // Check each row
                foreach ($rocks[$rockIndex] as $rowIndex => $row)
                {
                    // Check if left side of row would hit a rock
                    if (inColumn($y + $rowIndex, $x + $xLeft[$rockIndex][$rowIndex] - 1))
                    {
                        // One point on left side hit a rock
                        $blockedOnTheLeft = true;
                        break;
                    }
                }

                if (! $blockedOnTheLeft)
                {
                    $x += $deltaX; 
                }
            }
            else
            {
                // Rock moves right, check if it would hit rocks
                $blockedOnTheRight = false;

                // Check each row
                foreach ($rocks[$rockIndex] as $rowIndex => $row)
                {
                    // Check if right side of row would hit a rock
                    if (inColumn($y + $rowIndex, $x + $xRight[$rockIndex][$rowIndex] + 1))
                    {
                        // One point on right side hit a rock
                        $blockedOnTheRight = true;
                        break;
                    }
                }
    
                if (! $blockedOnTheRight)
                {
                    $x += $deltaX; 
                }
            }
        }
        else
        {
            // It's blocked by a wall
        }

        // Check if it can move vertically
        if ($y == 0)
        {
            // It reached the floor
            $blocked = true;
        }
        else
        {
            // Check if bottom of any rock column hits something
            for ($columnIndex = 0; $columnIndex < $rockWidths[$rockIndex]; $columnIndex++)
            {
                if (inColumn($y + $yBottom[$rockIndex][$columnIndex] - 1, $x + $columnIndex))
                {
                    $blocked = true;
                }
            }
        }
        
        if ($blocked)
        {
            // Add rock to pile
            for ($columnIndex = 0; $columnIndex < $rockWidths[$rockIndex]; $columnIndex++)
            {
                for ($rowIndex = 0; $rowIndex < count($rocks[$rockIndex]); $rowIndex++)
                {
                    if ($rocks[$rockIndex][$rowIndex][$columnIndex] == '#')
                    {
                        addToColumn($y + $rowIndex, $x + $columnIndex);
                    }
                }
            }
        }
        else
        {
            // Move rock one row down;
            $y--;
        }

        // Prepare for next wind blow
        $moveIndex = ($moveIndex + 1) % $numMoves;
    }
    while (! $blocked);
    
    // Prepare for next rock
    $rockIndex = ($rockIndex + 1) % $numRocks;

    // Compute hash from configuration
    $xPos = $x + $columnIndex;
    $height[$countRock] = getMaxRowIndex() + 1;    // getMaxRowIndex works on 0-based indexes
    $rise = $height[$countRock] - $height[$countRock - 1];
    $yRelToTop = $y + $rowIndex - $height[$countRock - 1];
    $hash = $rockIndex . '-' . $moveIndex . '-' . $xPos . '-' . $yRelToTop . '-' . $rise . '-';

    // Print status for visual detection of loop
    $list = $state[$hash] ?? [];
    //echo(sprintf('%06d', $countRock) . ': (h=' . $height[$countRock]  . ') ' . implode(', ', $list) . '<br />' . PHP_EOL);
    
    // Save rock number in hash table
    $state[$hash][] = $countRock;
    
    // Detect begin of loop
    if (! $beginLoop AND count($state[$hash]) == 2)
    {
        $beforeLoop = $countRock - 1;
        $beginLoop = $countRock;
    }
    
    // Detect end of loop
    if (! $endLoop AND count($state[$hash]) == 3)
    {
        $endLoop = $countRock - 1;
    }
}
while (count($state[$hash]) <= 2);

echo('Before loop: ' . $beforeLoop . ' rounds (h=' . $height[$beforeLoop] . ')<br />' . PHP_EOL);
echo('Loop from round ' . $beginLoop . ' to round ' . $endLoop . '<br />' . PHP_EOL);
$numberOfLoops = intdiv(1000000000000 - $beforeLoop, $endLoop - $beforeLoop);
echo('Number of loops: ' . $numberOfLoops . '<br />' . PHP_EOL);
$loopHeight = $height[$endLoop] - $height[$beforeLoop];
echo('Loop height: ' . $loopHeight . '<br />' . PHP_EOL);
$roundsLeft = (1000000000000 - $beforeLoop) % ($endLoop - $beforeLoop);
$roundsLeftHeight = $height[$beforeLoop + $roundsLeft] - $height[$beforeLoop];
if ($roundsLeft)
{
    echo('Rounds left: ' . $roundsLeft . ' (h=' . $roundsLeftHeight . ')<br />' . PHP_EOL);
}
echo('<br />' . PHP_EOL);
$result = $height[$beforeLoop] + $numberOfLoops * $loopHeight + $roundsLeftHeight;
echo('Result: ' . $result);


// ---- Functions -------------------------------------------------------------

function getMaxRowIndex()
{
    global $numColumns;

    // Check every column
    $maxRowIndex = -1;
    for ($columnIndex = 0; $columnIndex < $numColumns; $columnIndex++)
    {
        $columnMaxRowIndex = getColumnMaxRowIndex($columnIndex);

        if ($columnMaxRowIndex > $maxRowIndex)
        {
            $maxRowIndex = $columnMaxRowIndex;
        }
    }

    return $maxRowIndex;
}


function getColumnMaxRowIndex($columnIndex)
{
    global $columns;

    $maxRowIndex = -1;

    // Check if there are ranges in column
    $columnRangesInColumn = count($columns[$columnIndex]);
    if ($columnRangesInColumn)
    {
        // There are, pick last one
        $lastRange = $columns[$columnIndex][$columnRangesInColumn - 1];
        [$begin, $end] = $lastRange;

        $maxRowIndex = $end;
    }

    return $maxRowIndex;
}


function inColumn($val, $columnIndex)
{
    global $columns;

    $columnRanges = $columns[$columnIndex];

    // Check every ranges
    foreach ($columnRanges as $range)
    {
        [$begin, $end] = $range;

        // Check if it's in this range
        if ($val >= $begin AND $val <= $end)
        {
            // It's in this range
            return true;
        }

        // This and all following ranges will be greater, skip them
        if ($val < $begin)
        {
            return false;
        }
    }

    // It isn't in any range
    return false;
}


function addToColumn($val, $columnIndex)
{
    global $columns;

    $columnRanges = $columns[$columnIndex];

    // Check if there are ranges
    if (count($columnRanges))
    {
        // There are ranges, check them
        $added = false;
        foreach ($columnRanges as $rangeIndex => $range)
        {
            [$begin, $end] = $range;

            // Check if val is in range or just above
            if ($val >= $begin AND $val <= $end + 1)
            {
                // Yes, extend range
                $newRange = [$begin, max($val, $end)];
                $columns[$columnIndex][$rangeIndex] = $newRange;
                [$begin, $end] = $newRange;    // Must update end if changed
                $added = true;
            }

            if ($added)
            {
                // Check if this range isn't the last one
                if ($rangeIndex <= count($columnRanges) - 2)
                {
                    // Yes, pick next range
                    $nextRangeIndex = $rangeIndex + 1;
                    $nextRange = $columnRanges[$nextRangeIndex];
    
                    [$nextBegin, $nextEnd] = $nextRange;
                    
                    // Check if next range is just above this range
                    if ($end == $nextBegin - 1)
                    {
                        // Yes, join them
                        $columns[$columnIndex][$rangeIndex] = [$begin, $nextEnd];
    
                        // Discard old next range
                        unset($columns[$columnIndex][$nextRangeIndex]);
    
                        // Force sequential indexes
                        sort($columns[$columnIndex]);
                    }
                }
            }

            break;
        }

        if (! $added)
        {
            // val wasn't added to existing range, create new one
            $columns[$columnIndex][] = [$val, $val];
            sort($columns[$columnIndex]);
        }
    }
    else
    {
        // There are no ranges, add a new one
        $columns[$columnIndex][] = [$val, $val];
    }
}


function dump()
{
    global $numColumns;

    echo('<pre>' . PHP_EOL);

    // Print every row
    $maxRowIndex = getMaxRowIndex();
    for ($rowIndex = $maxRowIndex; $rowIndex >= 0; $rowIndex--)
    {
        // Print row
        echo('|');
        for ($columnIndex = 0; $columnIndex < $numColumns; $columnIndex++)
        {
            if (inColumn($rowIndex, $columnIndex))
            {
                echo('#');
            }
            else
            {
                echo('.');
            }
        }
        echo('|' . PHP_EOL);
    }

    // Print floor
    echo('+-------+' . PHP_EOL);

    echo('<pre>' . PHP_EOL);
}