<?php

// Init vars
$input_file = 'input.txt';
$rowHead = 0;
$colHead = 0;
$rowTail = 0;
$colTail = 0;
$posTail = [];
$posTail['0,0'] = ($posTail['0,0'] ?? 0) + 1;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);
        $key = '';

        // Get direction and steps
        [$direction, $steps] = explode(' ', $line);

        // Move head, one step at a time
        for ($i = 0; $i < $steps; $i++)
        {
            // Move head one step
            switch ($direction)
            {
                case 'U':
                    $rowHead += -1;
                    break;
                case 'D':
                    $rowHead += +1;
                    break;
                case 'L':
                    $colHead += -1;
                    break;
                case 'R':
                    $colHead += +1;
                    break;
            }

            // Find head-tail distance
            $rowDistance = $rowHead - $rowTail;
            $colDistance = $colHead - $colTail;

            // Update tail position (if needed)
            if (abs($rowDistance) == 2)
            {
                $rowTail += $rowDistance / 2; 
                $colTail += $colDistance;
                $key = implode(',', [$rowTail, $colTail]);
                $posTail[$key] = ($posTail[$key] ?? 0) + 1; 
            }
            if (abs($colDistance) == 2)
            {
                $rowTail += $rowDistance;
                $colTail += $colDistance / 2;
                $key = implode(',', [$rowTail, $colTail]);
                $posTail[$key] = ($posTail[$key] ?? 0) + 1; 
            }
        }
    }

    fclose($handle);
}

echo(count($posTail));