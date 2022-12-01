<?php

// Init vars
$input_file = 'input.txt';
$elf_calories = [];

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    $index = 0;
    $calories = 0;

    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Check if line content is a number or a separator
        if (trim($line))
        {
            // It's a number
            $calories += intval($line);
        }
        else
        {
            // It's a separator
            $elf_calories[$index] = $calories;

            $index++;
            $calories = 0;
        }
    }

    fclose($handle);
}

echo max($elf_calories);