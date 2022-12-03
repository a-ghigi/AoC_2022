<?php

// Init vars
$input_file = 'input.txt';

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    $priorities = 0;

    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Load three rucksacks
        $first = trim($line);
        $second = trim(fgets($handle));
        $third = trim(fgets($handle));
        
        // Find common element
        $first_a = str_split($first);
        $second_a = str_split($second);
        $third_a = str_split($third);
        $first_second_a = array_intersect($first_a, $second_a);
        $common = array_intersect($first_second_a, $third_a);

        $priorities += priority($common);
    }

    fclose($handle);
}

echo($priorities);


function priority($common)
{
    foreach($common as $element)
    {
        if (ord($element) >= ord('a') AND ord($element) <= ord('z'))
        {
            return ord($element) - ord('a') + 1;
        }
        if (ord($element) >= ord('A') AND ord($element) <= ord('Z'))
        {
            return ord($element) - ord('A') + 27;
        }
        echo('Error (1)');
        die();
    }
}