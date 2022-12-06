<?php

// Init vars
$input_file = 'input.txt';
$packet_length = 14;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);

        // Scan input from left to right
        for ($i = 0; $ $i <= strlen($line) - $packet_length; $i++)
        {
            // Extract packet
            $packet = substr($line, $i, $packet_length);

            // Check packet
            if (has_no_duplicates($packet))
            {
                // Found!
                echo($i + $packet_length . '<br />' . PHP_EOL);
                break;
            }
        }
    }

    fclose($handle);
}


function has_no_duplicates($packet)
{
    $letter = [];

    // Count letters in packet
    for ($i = 0; $i < strlen($packet); $i++)
    {
        // Pick letter
        $c = substr($packet, $i, 1);

        // Increment letter counter
        if (isset($letter[$c]))
        {
            $letter[$c]++;
        }
        else
        {
            $letter[$c] = 1;
        }
    }

    // Check for duplicates
    foreach ($letter as $count)
    {
        if ($count > 1)
        {
            // Duplicate found
            return false;
        }
    }
    // No duplicates found
    return true;
}