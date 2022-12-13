<?php

include "parse.php";

// Init vars
$input_file = 'input.txt';
$result = 0;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    $couple = 0;

    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Left packet
        $leftTxt = trim($line);

        // Read right packet
        $line = fgets($handle);
        $rightTxt = trim($line);

        // Read separator
        fgets($handle);

        // Increase couple counter
        $couple++;

        // Parse packets
        $left = parse($leftTxt);
        $right = parse($rightTxt);

        if (in_order($left, $right))
        {
            $result += $couple;
        }
    }

    fclose($handle);
}

echo($result);


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
            echo('Errore (1)');
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

        // Same as above
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