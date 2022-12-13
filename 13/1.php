<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../_libs/kint.phar';
include '../../_libs/kint.php';

// Init vars
$input_file = 'input-test.txt';
$count = 0;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
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

        // Parse packets
        $left = parse($leftTxt);
        $right = parse($rightTxt);

        d($leftTxt, $rightTxt, $left, $right);
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
    while (($c = substr($txt, 0, 1)))
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