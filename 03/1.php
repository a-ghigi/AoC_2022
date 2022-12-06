<?php

// Init vars
$input_file = 'input.txt';
$priorities = 0;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Split rucksack
        $line = trim($line);
        $count = strlen($line);
        $first = substr($line, 0, $count / 2);
        $second = substr($line, $count / 2);
        
        // Find common element
        $first_a = str_split($first);
        $second_a = str_split($second);
        $common = array_intersect($first_a, $second_a);

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