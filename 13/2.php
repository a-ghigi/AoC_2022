<?php

include "parse.php";

// Init vars
$input_file = 'input.txt';

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    $packets = [];

    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);

        if ($line)
        {
            $packetTxt = $line;
            $packetArray = parse($packetTxt);

            // Add packet (as text and array) to packets
            $packets[] = [$packetTxt, $packetArray];
        }
    }

    fclose($handle);

    // Add separator packets
    $packets[] = ['[[2]]', [[2]]];
    $packets[] = ['[[6]]', [[6]]];

    // Sort packets comparing on array version
    usort($packets, 'comparePackets');
}

$posFirstDivider = 0;
$posSecondDivider = 0;

// Dump sorted packets
foreach($packets as $index => $packet)
{
    $styleTag = '';

    // Check for divider
    if ($packet[0] == '[[2]]')
    {
        $posFirstDivider = $index + 1;
        $styleTag = ' style="background: yellow"';
    }
    if ($packet[0] == '[[6]]')
    {
        $posSecondDivider = $index + 1;
        $styleTag = ' style="background: yellow"';
    }

    echo('<span' . $styleTag . '>' . sprintf('%03d', $index + 1) . ': ' . $packet[0] . '</span><br />' . PHP_EOL);
}
echo('<br />' . PHP_EOL);

// Print result
echo ($posFirstDivider * $posSecondDivider);

// ---- Functions -------------------------------------------------------------

function in_order($left, $right)
{
    $compare = compareArrays($left, $right);

    $result = null;
    switch ($compare)
    {
        case -1:
            $result = true;
            break;
        case 0:
            echo('Error (1)');
            die();
            break;
        case 1:
            $result = false;
            break;
    }

    return $result;
}


function compareArrays($left, $right)
{
    if (count($left) == 0 AND count($right) > 0)
    {
        return -1;
    }
    elseif (count($left) > 0 AND count($right) == 0)
    {
        return 1; 
    }
    elseif (count($left) == 0 AND count($right) == 0)
    {
        return 0;
    }

    $headLeft = $left[0];
    $headRight = $right[0];

    if (gettype($headLeft) == 'integer' AND gettype($headRight) == 'integer')
    {
        // Heads are both integers
        if ($headLeft < $headRight)
        {
            return -1;
        }
        elseif ($headLeft > $headRight)
        {
            return 1;
        }
        else
        {
            return compareArrays(array_slice($left, 1), array_slice($right, 1));
        }
    }
    elseif (gettype($headLeft) == 'array' AND gettype($headRight) == 'array')
    {
        // Heads are both arrays
        $compare = compareArrays($headLeft, $headRight);

        if ($compare == -1)
        {
            return -1;
        }
        elseif ($compare == 1)
        {
            return 1;
        }
        else
        {
            return compareArrays(array_slice($left, 1), array_slice($right, 1));
        }
    }
    else
    {
        // Heads of different type
        if (gettype($headLeft) == 'integer')
        {
            $headLeftArray = [$headLeft];
            $headRightArray = $headRight;
        }
        else
        {
            $headLeftArray = $headLeft;
            $headRightArray = [$headRight];
        }

        $compare = compareArrays($headLeftArray, $headRightArray);

        if ($compare == -1)
        {
            return -1;
        }
        elseif ($compare == 1)
        {
            return 1;
        }
        else
        {
            return compareArrays(array_slice($left, 1), array_slice($right, 1));
        }
    }
}


// Compare packets on array version
function comparePackets($left, $right)
{
    return compareArrays($left[1], $right[1]);
}