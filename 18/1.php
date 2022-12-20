<?php

set_time_limit(0);

// Init vars
$input_file = 'input.txt';

$cubes = [];
$minX = PHP_INT_MAX;
$maxX = PHP_INT_MIN;
$minY = PHP_INT_MAX;
$maxY = PHP_INT_MIN;
$minZ = PHP_INT_MAX;
$maxZ = PHP_INT_MIN;
$count = 0;

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        [$x, $y, $z] = explode(',', trim($line));
        $cubes[$x][$y][$z] = true;
        $minX = min($x, $minX);
        $maxX = max($x, $maxX);
        $minY = min($y, $minY);
        $maxY = max($y, $maxY);
        $minZ = min($z, $minZ);
        $maxZ = max($z, $maxZ);
    }

    fclose($handle);
}

// Check every cube
for ($x = $minX; $x <= $maxX; $x++)
{
    for ($y = $minY; $y <= $maxY; $y++)
    {
        for ($z = $minZ; $z <= $maxZ; $z++)
        {
            if (isset($cubes[$x][$y][$z]))
            {
                // Check upper face
                if (! isset($cubes[$x][$y][$z+1]))
                {
                    $count++;
                }
                
                // Check lower face
                if (! isset($cubes[$x][$y][$z-1]))
                {
                    $count++;
                }
                
                // Check right face
                if (! isset($cubes[$x][$y+1][$z]))
                {
                    $count++;
                }
                
                // Check left face
                if (! isset($cubes[$x][$y-1][$z]))
                {
                    $count++;
                }
                
                // Check back face
                if (! isset($cubes[$x+1][$y][$z]))
                {
                    $count++;
                }
                
                // Check front face
                if (! isset($cubes[$x-1][$y][$z]))
                {
                    $count++;
                }
            }
        }
    }
}

echo($count);