<?php

// Init vars
$input_file = 'input.txt';

$stack[1] = 'RGHQSBTN';
$stack[2] = 'HSFDPZJ';
$stack[3] = 'ZHV';
$stack[4] = 'MZJFGH';
$stack[5] = 'TZCDLMSR';
$stack[6] = 'MTWVHZJ';
$stack[7] = 'TFPLZ';
$stack[8] = 'QVWS';
$stack[9] = 'WHLMTDNC';

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

            // Pop and push all elements at once
            $moved = substr($stack[$from], strlen($stack[$from]) - $qty);
            $stack[$from] = substr($stack[$from], 0, strlen($stack[$from]) - $qty);
            $stack[$to] = $stack[$to] . $moved;
        }
    }

    fclose($handle);
}

// Print result
for ($i = 1; $i <= count($stack); $i++)
{
    echo(substr($stack[$i], strlen($stack[$i]) - 1));
}