<?php

// Init vars
$input_file = 'input.txt';
$count = 0;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
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
        
        // Convert ranges to arrays
        $range1a = range($min1, $max1);
        $range2a = range($min2, $max2);

        // Test intersection
        $common = array_intersect($range1a, $range2a);

        if (count($common))
        {
            $count++;
        }
    }

    fclose($handle);
}

echo($count);