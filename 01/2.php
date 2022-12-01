<?php

// ---- Same as 1.php ---------------------------------------------------------
// Init vars
$input_file = 'input.txt';
$elf_calories = [];

// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    $index = 0;
    $calories = 0;

    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Check if line content is a number or a separator
        if (trim($line))
        {
            // It's a number, add value
            $calories += intval($line);
        }
        else
        {
            // It's a separator, save elf's value
            $elf_calories[$index] = $calories;

            // Iterate
            $index++;
            $calories = 0;
        }
    }

    fclose($handle);
}
// ----------------------------------------------------------------------------

// Find calories of top 3 elves
$top_calories = 0;
$count_top = 3;

for ($i = 0; $i < $count_top; $i++)
{
    // Find max
    $max = max($elf_calories);

    // Find max's index
    $max_index = array_search($max, $elf_calories);

    // Add and iterate
    $top_calories += $max;
    unset($elf_calories[$max_index]);
}

echo $top_calories;