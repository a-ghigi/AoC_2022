<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../_libs/kint.phar';
include '../../_libs/kint.php';

// Init vars
$input_file = 'input.txt';
$count = 0;

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

        $result = in_order($left, $right);

        if ($result)
        {
            $count += $couple;
        }

        d($couple, $leftTxt, $rightTxt, $left, $right, $result, $count);
    }

    fclose($handle);
}

echo($count);


// ---- Functions -------------------------------------------------------------

function parse($txt)
{
    $c = substr($txt, 0, 1);
    if ($c == '[')
    {
        // Begins array
        [$result, $count] = parseArray(substr($txt, 1));
    }

    return $result;
}

function parseArray($txt)
{
    $result = [];
    $parsed = 0;
    while (($c = substr($txt, 0, 1)) !== '')
    {
        if (in_array($c, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']))
        {
            [$element, $count] = parseNumber($txt);
            $result[] = $element;
            $txt = substr($txt, $count);
            $parsed += $count;
        }
        elseif ($c == '[')
        {
            [$element, $count] = parseArray(substr($txt, 1));
            $result[] = $element;
            $txt = substr($txt, $count + 1);
            $parsed += $count + 1;
        }
        elseif ($c == ',')
        {
            $txt = substr($txt, 1);
            $parsed += 1;
        }
        elseif ($c == ']')
        {
            $txt = substr($txt, 1);
            $parsed += 1;
            return [$result, $parsed];
        }
    }
}

function parseNumber($txt)
{
    $numberStr = '';
    while (in_array(substr($txt, 0, 1), ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']))
    {
        $numberStr .= substr($txt, 0, 1);
        $txt = substr($txt, 1);
    }

    return [intval($numberStr), strlen($numberStr)];
}


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