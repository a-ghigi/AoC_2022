<?php

// Init vars
$input_file = 'input.txt';
$forest = [];
$count = 0;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);

        if ($line)
        {
            $lineArray = str_split($line);
            $forest[] = $lineArray;
        }
    }
    
    fclose($handle);
}

// Find size of maze
$maxRowIndex = count($forest) - 1;
$maxColumnIndex = count($forest[0]) - 1;

// Check every tree inside borders
for ($rowIndex = 1; $rowIndex < $maxRowIndex; $rowIndex++)
{
    for ($columnIndex = 1; $columnIndex < $maxColumnIndex; $columnIndex++)
    {
        // Pick tree in the forest
        $tree = $forest[$rowIndex][$columnIndex];

        // From north
        if ($forest[0][$columnIndex] < $tree)
        {
            // It's taller than the border

            if ($rowIndex == 1)
            {
                // It's next to the border
                $count++;
                continue;
            }
            else
            {
                if ($tree > max(getColumnSegment($columnIndex, 1, $rowIndex - 1)))
                {
                    // It's the tallest from the border
                    $count++;
                    continue;
                }
            }
        }

        // From south
        if ($forest[$maxRowIndex][$columnIndex] < $tree)
        {
            if ($rowIndex == $maxRowIndex - 1)
            {
                $count++;
                continue;
            }
            else
            {
                if ($tree > max(getColumnSegment($columnIndex, $rowIndex + 1, $maxRowIndex - 1)))
                {
                    $count++;
                    continue;
                }
            }
        }

        // From west
        if ($forest[$rowIndex][0] < $tree)
        {
            if ($columnIndex == 1)
            {
                $count++;
                continue;
            }
            else
            {
                if ($tree > max(getRowSegment($rowIndex, 1, $columnIndex - 1)))
                {
                    $count++;
                    continue;
                }
            }
        }

        // From east
        if ($forest[$rowIndex][$maxColumnIndex] < $tree)
        {
            if ($columnIndex == $maxColumnIndex - 1)
            {
                $count++;
                continue;
            }
            else
            {
                if ($tree > max(getRowSegment($rowIndex, $columnIndex + 1, $maxColumnIndex - 1)))
                {
                    $count++;
                    continue;
                }
            }
        }
    }
}

// Add border trees
$count += ($maxRowIndex + $maxColumnIndex) * 2;

echo($count);


function getRowSegment($rowIndex, $fromColumn, $toColumn)
{
    global $forest;

    $result = array_slice($forest[$rowIndex], $fromColumn, $toColumn - $fromColumn + 1);

    return $result;
}

function getColumnSegment($columnIndex, $fromRow, $toRow)
{
    global $forest;

    $result = [];

    for ($rowIndex = $fromRow; $rowIndex <= $toRow; $rowIndex++)
    {
        $result[$rowIndex] = $forest[$rowIndex][$columnIndex];
    }

    return $result;
}