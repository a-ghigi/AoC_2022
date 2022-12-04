<?php

// Init vars
$input_file = 'input.txt';

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    $count = 0;

    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Get ranges
        list($range1, $range2) = explode(',', trim($line));
        list($min1, $max1) = explode('-', $range1);
        $min1 = intval($min1);
        $max1 = intval($max1);
        list($min2, $max2) = explode('-', $range2);
        $min2 = intval($min2);
        $max2 = intval($max2);
        
        // Test inclusion
        if ($min1 <= $min2 AND $max1 >= $max2)
        {
            $count++;
        } 
        elseif ($min1 >= $min2 AND $max1 <= $max2)
        {
            $count++;
        } 
    }

    fclose($handle);
}

echo($count);