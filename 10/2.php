<?php

// Init vars
$input_file = 'input.txt';
$x = 1;
$cycle = 0;
$crt = [];

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
                check_pixel();
                break;
            case 'addx':
                $cycle++;
                check_pixel();

                $cycle++;
                check_pixel();
                $x += intval($amount);
        }
    }

    fclose($handle);
}

// Print screen
echo('<pre>');
for ($i = 0; $i < count($crt); $i++)
{
    echo($crt[$i]);
    if (($i + 1) % 40 == 0)
    {
        echo(PHP_EOL);
    }
}
echo('<pre/>');


function check_pixel()
{
    global $cycle;
    global $x;
    global $crt;

    $pixel_position = ($cycle - 1) % 40;
    $sprite_center = (($x - 1) % 40) + 1;

    if ($pixel_position >= $sprite_center - 1 AND $pixel_position <= $sprite_center + 1)
    {
        $crt[$cycle - 1] = '#';
    }
    else
    {
        $crt[$cycle - 1] = '.';
    }
}