<?php

// Init vars
$input_file = 'input.txt';

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    $matches = [];

    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Check input line type
        if (preg_match('/move (\d+) from (\d+) to (\d+)/', trim($line), $matches) === 1)
        {
            // It's a move
            $qty = $matches[1];
            $from = $matches[2];
            $to = $matches[3];

            // Pop and push every element
            for ($i = 1; $i <= $qty; $i++)
            {
                $stack[$to] = $stack[$to] . substr($stack[$from],strlen($stack[$from]) - 1);
                $stack[$from] = substr($stack[$from], 0, strlen($stack[$from]) - 1);
            }
        }
        else
        {
            // It's not a move

            if (strlen($line) > 1)
            {
                // It's a configuration line

                // For every stack
                for ($i = 1; $i <= (strlen($line) / 4); $i++)
                {
                    // Pick possible element
                    $c = substr($line, $i * 4 - 3, 1);

                    // Check if it's a valid element
                    if (preg_match('/[A-Z]/', $c, $matches) === 1)
                    {
                        // Load element on stack (in reverse order)
                        $stack[$i] = $stack[$i] . $c;
                    }
                }
            }
            else
            {
                // Configuration completed

                // For every stack
                for ($i = 1; $i <= count($stack); $i++)
                {
                    // Reverse stack order
                    $stack[$i] = strrev($stack[$i]);
                }
            }
        }
     }

    fclose($handle);
}

// Print result
for ($i = 1; $i <= count($stack); $i++)
{
    echo(substr($stack[$i], strlen($stack[$i]) - 1));
}