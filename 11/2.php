<?php

// Init vars
$input_file = 'input.txt';
$monkeyIndex = null;
$items = [];
$examined = [];
$operator = [];
$operand = [];
$divisible_by = [];
$if_true = [];
$if_false = [];

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {        
        $line = trim($line);
        $match = [];

        // Parse input
        if (preg_match('/Monkey (\d+):/', $line, $match) === 1)
        {
            $monkeyIndex = intval($match[1]);
        }
        elseif (preg_match('/Starting items: (.+)/', $line, $match) === 1)
        {
            $items[$monkeyIndex] = explode(', ', $match[1]);
            foreach($items[$monkeyIndex] as $index => $item)
            {
                $items[$monkeyIndex][$index] = intval($item);
            }
        }
        elseif (preg_match('/Operation: new = old (\S) (\S+)/', $line, $match) === 1)
        {
            $operator[$monkeyIndex] = $match[1];
            $operand[$monkeyIndex] = $match[2];
        }
        elseif (preg_match('/Test: divisible by (\d+)/', $line, $match) === 1)
        {
            $divisible_by[$monkeyIndex] = intval($match[1]);
        }
        elseif (preg_match('/If true: throw to monkey (\d+)/', $line, $match) === 1)
        {
            $if_true[$monkeyIndex] = intval($match[1]);
        }
        elseif (preg_match('/If false: throw to monkey (\d+)/', $line, $match) === 1)
        {
            $if_false[$monkeyIndex] = intval($match[1]);
        }
    }

    fclose($handle);
}

// Number of monkeys
$monkeys = $monkeyIndex + 1;

// Find modulus, used to keep worry levels under control
$modulus = 1;
foreach ($divisible_by as $num)
{
    $modulus = $modulus * $num;
}

// Init examined array
for ($monkeyIndex = 0; $monkeyIndex < $monkeys; $monkeyIndex++)
{
    $examined[$monkeyIndex] = 0;
}

for ($round = 1; $round <= 10000; $round++)
{
    for ($monkeyIndex = 0; $monkeyIndex < $monkeys; $monkeyIndex++)
    {
        while (count($items[$monkeyIndex]))
        {
            // Pick item
            $item = $items[$monkeyIndex][0];
            $items[$monkeyIndex] = array_slice($items[$monkeyIndex], 1);

            // Find operand value
            if ($operand[$monkeyIndex] == 'old')
            {
                $value = $item;
            }
            else{
                $value = intval($operand[$monkeyIndex]);
            }

            // Compute worry level
            switch ($operator[$monkeyIndex])
            {
                case '+':
                    $item += $value;
                    break;
                case '*':
                    $item *= $value;
                    break;
            }

            // Keep worry level under control
            $item = $item % $modulus;

            // Throw item
            if ($item % $divisible_by[$monkeyIndex] == 0)
            {
                $items[$if_true[$monkeyIndex]][] = $item;
            }
            else
            {
                $items[$if_false[$monkeyIndex]][] = $item;
            }

            // This monkey has examined one more item
            $examined[$monkeyIndex]++;
        }
    }
}

// Sort examined array in reverse order
arsort($examined);

// Compute monkey situation by multiplying first two elements
$result = 1;
$count = 0;
foreach ($examined as $value)
{
    $count++;
    $result *= $value;

    if ($count == 2)
    {
        break;
    }
}

echo($result);