<?php

// Init vars
$input_file = 'input.txt';
$x = 1;
$cycle = 0;
$signal_strength = [];

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);

        [$command, $amount] = explode(' ', $line);

        switch ($command)
        {
            case 'noop':
                $cycle++;
                check_signal_strength();
                break;
            case 'addx':
                $cycle++;
                check_signal_strength();

                $cycle++;
                check_signal_strength();

                $x += intval($amount);
                break;
        }
    }

    fclose($handle);
}

// Sum signal strength
$sum = 0;
foreach ($signal_strength as $value)
{
    $sum += $value;
}

echo($sum);


function check_signal_strength()
{
    global $cycle;
    global $x;
    global $signal_strength;

    if (in_array($cycle, [20, 60, 100, 140, 180, 220]))
    {
        $signal_strength[$cycle] = $cycle * $x;
    }
}