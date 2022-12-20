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

// Find outside space
$outside = [];
outside(0, 0, 0);

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
                if (($outside[$x][$y][$z + 1] ?? false) OR $z == $maxZ)
                {
                    $count++;
                }
                
                // Check lower face
                if (($outside[$x][$y][$z - 1] ?? false) OR $z == $minZ)
                {
                    $count++;
                }
                
                // Check right face
                if (($outside[$x][$y + 1][$z] ?? false) OR $y == $maxY)
                {
                    $count++;
                }
                
                // Check left face
                if (($outside[$x][$y - 1][$z] ?? false) OR $y == $minY)
                {
                    $count++;
                }
                
                // Check back face
                if (($outside[$x + 1][$y][$z] ?? false) OR $x == $maxX)
                {
                    $count++;
                }
                
                // Check front face
                if (($outside[$x - 1][$y][$z] ?? false) OR $x == $minX)
                {
                    $count++;
                }
            }
        }
    }
}

echo($count);


// ---- Functions -------------------------------------------------------------

function outside($x, $y, $z)
{
    global $minX;
    global $maxX;
    global $minY;
    global $maxY;
    global $minZ;
    global $maxZ;
    global $cubes;
    global $outside;

    if (! isset($cubes[$x][$y][$z]))
    {
        $outside[$x][$y][$z] = true;

        if ($x > $minX AND ! isset($outside[$x - 1][$y][$z]))
        {
            outside($x - 1, $y, $z);
        }
        
        if ($x < $maxX AND ! isset($outside[$x + 1][$y][$z]))
        {
            outside($x + 1, $y, $z);
        }
        
        if ($y > $minY AND ! isset($outside[$x][$y - 1][$z]))
        {
            outside($x, $y - 1, $z);
        }
        
        if ($y < $maxY AND ! isset($outside[$x][$y + 1][$z]))
        {
            outside($x, $y + 1, $z);
        }
        
        if ($z > $minZ AND ! isset($outside[$x][$y][$z - 1]))
        {
            outside($x, $y, $z - 1);
        }
        
        if ($z < $maxZ AND ! isset($outside[$x][$y][$z + 1]))
        {
            outside($x, $y, $z + 1);
        }
    }
    else
    {
        $outside[$x][$y][$z] = false;
    }
}


function dump()
{
    global $minX;
    global $maxX;
    global $minY;
    global $maxY;
    global $minZ;
    global $maxZ;
    global $cubes;
    global $outside;

    echo('<pre>' . PHP_EOL);
    for ($z = $maxZ; $z >= $minZ; $z--)
    {
        for ($y = $maxY; $y >= $minY; $y--)
        {
            for ($x = $minX; $x <= $maxX; $x++)
            {
                if (isset($cubes[$x][$y][$z]))
                {
                    if (isset($cubes[$x][$y][$z - 1]))
                    {
                        if (isset($cubes[$x][$y][$z + 1]))
                        {
                            //echo('I');
                            echo('#');
                        }
                        else
                        {
                            //echo('v');  
                            echo('#');
                        }
                    }
                    else
                    {
                        if (isset($cubes[$x][$y][$z + 1]))
                        {
                            //echo('^');
                            echo('#');
                        }
                        else
                        {
                            echo('#');
                        }
                    }
                }
                else
                {
                    if (isset($outside[$x][$y][$z]))
                    {
                        echo(' ');
                    }
                    else
                    {
                        echo('.');
                    }
                }
            }
            echo(PHP_EOL);
        }
        echo(PHP_EOL);
    }
    echo('</pre>' . PHP_EOL);
}