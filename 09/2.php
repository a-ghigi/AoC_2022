<?php

// Init vars
$input_file = 'input.txt';
$rowKnot = [];
$colKnot = [];
$countKnots = 10;

// Init knots position
for ($i = 0; $i < $countKnots; $i++)
{
    $rowKnot[$i] = 0;
    $colKnot[$i] = 0;
}
$tailIndex = $countKnots - 1;

// Init tail position log
$posTail = [];
$key = implode(',', [$rowKnot[$tailIndex], $colKnot[$tailIndex]]);
$posTail[$key] = ($posTail[$key] ?? 0) + 1; 

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);
        // Get direction and steps
        [$direction, $steps] = explode(' ', $line);

        // Move head, one step at a time
        for ($step = 1; $step <= $steps; $step++)
        {
            // Move head one step
            switch ($direction)
            {
                case 'U':
                    $rowKnot[0]--;
                    break;
                case 'D':
                    $rowKnot[0]++;
                    break;
                case 'L':
                    $colKnot[0]--;
                    break;
                case 'R':
                    $colKnot[0]++;
                    break;
            }

            // Propagate move down the rope
            for ($knotIndex = 0; $knotIndex < $countKnots - 1; $knotIndex++)
            {
                // Find segment distance
                $rowDistance = $rowKnot[$knotIndex] - $rowKnot[$knotIndex + 1];
                $colDistance = $colKnot[$knotIndex] - $colKnot[$knotIndex + 1];

                // Update next knot position (if needed)
                $stretched = false;
                if (abs($rowDistance) == 2)
                {
                    // Horizontally stretched
                    $stretched = true;

                    // Move next knot up/down
                    $rowKnot[$knotIndex + 1] += $rowDistance / 2;

                    // Move next knot left/right, if needed
                    switch($colDistance)
                    {
                        case -2:
                        case -1:
                            $colKnot[$knotIndex + 1]--;
                            break;
                        case 0:
                            // Nothing
                            break;
                        case 1:
                        case 2:
                            $colKnot[$knotIndex + 1]++;
                            break;
                    }
                }
                elseif (abs($colDistance) == 2)
                {
                    // Vertically stretched
                    $stretched = true;

                    // Move next knot up/down, if needed
                    switch($rowDistance)
                    {
                        case -2:
                        case -1:
                            $rowKnot[$knotIndex + 1]--;
                            break;
                        case 0:
                            // Nothing
                            break;
                        case 1:
                        case 2:
                            $rowKnot[$knotIndex + 1]++;
                            break;
                    }

                    // Move next knot left/right, if needed
                    $colKnot[$knotIndex + 1] += $colDistance / 2;
                }

                // Next knot didn't move, no need to update remaining knots
                if (! $stretched)
                {
                    break;
                }
            }

            // Update tail log
            $key = implode(',', [$rowKnot[$tailIndex], $colKnot[$tailIndex]]);
            $posTail[$key] = ($posTail[$key] ?? 0) + 1; 
        }
    }

    fclose($handle);
}

echo(count($posTail));


function plot()
{
    global $rowKnot;
    global $colKnot;

    $size = 5;
    $minRow = min(min($rowKnot), -($size)) - 1;
    $maxRow = max(max($rowKnot), $size) + 1;
    $minCol = min(min($colKnot), -($size)) - 1;
    $maxCol = max(max($colKnot), $size) + 1;

    // Background
    for ($i = $minRow; $i <= $maxRow; $i++)
    {
        for ($j = $minCol; $j <= $maxCol; $j++)
        {
            if ($i == 0 AND $j == 0)
            {
                $plot[$i][$j] = '.';
            }
            elseif ($i == 0)
            {
                $plot[$i][$j] = '-';
            }
            elseif ($j == 0)
            {
                $plot[$i][$j] = '|';
            }
            else
            {
                $plot[$i][$j] = '.';
            }
        }
    }

    // Add knots
    //for ($index = count($rowKnot) - 1; $index >= 0; $index--)
    for ($index = 0; $index < count($rowKnot); $index++)
    {
        $plot[$rowKnot[$index]][$colKnot[$index]] = $index;
    }

    // Convert to "image"
    $result = '';
    foreach ($plot as $row)
    {
        $result .= implode('', $row) . PHP_EOL;
    }

    return $result;
}