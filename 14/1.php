<?php

// Init vars
$input_file = 'input.txt';
$map = [];

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        $line = trim($line);

        if ($line)
        {
            $corners = explode(' -> ', $line);
            $start = $corners[0];
            [$startX, $startY] = explode(',', $start);
            $map[$startY][$startX] = '#';

            for ($i = 1; $i < count($corners); $i++)
            {
                $end = $corners[$i];
                [$endX, $endY] = explode(',', $end);

                if ($startX == $endX)
                {
                    // Vertical line
                    if ($startY < $endY)
                    {
                        // Move down
                        for ($y = $startY; $y <= $endY; $y++)
                        {
                            $map[$y][$startX] = '#';
                        }
                    }
                    else
                    {
                        // Move up
                        for ($y = $startY; $y >= $endY; $y--)
                        {
                            $map[$y][$startX] = '#';
                        }
                    }
                }
                elseif ($startY == $endY)
                {
                    // Horizontal line
                    if ($startX < $endX)
                    {
                        //Move right
                        for ($x = $startX; $x <= $endX; $x++)
                        {
                            $map[$startY][$x] = '#';
                        }
                    }
                    else
                    {
                        // Move left
                        for ($x = $startX; $x >= $endX; $x--)
                        {
                            $map[$startY][$x] = '#';
                        }
                    }
                }
                else
                {
                    echo('Error (1)');
                    die();
                }

                $start = $end;
                [$startX, $startY] = explode(',', $start);
            }
        }
    }

    fclose($handle);
}

$map[0][500] = '+';

// Find map limits
$minX = PHP_INT_MAX;
$maxX = 0;
$minY = 0;
$maxY = 0;
foreach ($map as $y => $row)
{
    $indexes = array_keys($row);
    $minX = min($minX, min($indexes));
    $maxX = max($maxX, max($indexes));
    $maxY = max($maxY, $y);
}

$abyss = false;
$count = 0;
do
{
    $sand = '500,0';
    [$sandX, $sandY] = explode(',', $sand);
    $blocked = false;
    do
    {
        if (free($sandX, $sandY + 1))
        {
            $sandY++;

            if ($sandY > $maxY)
            {
                $abyss = true;
            }
        }
        elseif (free($sandX - 1, $sandY + 1))
        {
            $sandX--;
            $sandY++;

            if ($sandX < $minX OR $sandY > $maxY)
            {
                $abyss = true;
            }
        }
        elseif (free($sandX + 1, $sandY + 1))
        {
            $sandX++;
            $sandY++;

            if ($sandX > $maxX OR $sandY > $maxY)
            {
                $abyss = true;
            }
        }
        else
        {
            $map[$sandY][$sandX] = 'o';
            $blocked = true;
        }
    }
    while (! $blocked AND ! $abyss);

    if ($blocked)
    {
        $count++;
    }
}
while (! $abyss);

echo($count . '<br />' . PHP_EOL);

printMap();


// ---- Functions -------------------------------------------------------------

function printMap()
{
    global $minX;
    global $maxX;
    global $maxY;
    global $minY;
    global $map;

    // Print map
    echo('<pre>' . PHP_EOL);
    for ($y = $minY; $y <= $maxY; $y++)
    {
        for ($x = $minX; $x <= $maxX; $x++)
        {
            echo($map[$y][$x] ?? '.');
        }
        echo(PHP_EOL);
    }
    echo('</pre>' . PHP_EOL);
}

function free($x, $y)
{
    global $map;

    if (isset($map[$y][$x]))
    {
        return false;
    }
    else
    {
        return true;
    }
}