<?php

// Init vars
$input_file = 'input.txt';
$forest = [];
$maxScore = 0;

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
            $lineArray = str_split($line);
            $forest[] = $lineArray;
        }
    }
    
    fclose($handle);
}

// Find size of maze
$maxRowIndex = count($forest) - 1;
$maxColumnIndex = count($forest[0]) - 1;

// Check every tree inside borders
for ($rowIndex = 1; $rowIndex < $maxRowIndex; $rowIndex++)
{
    for ($columnIndex = 1; $columnIndex < $maxColumnIndex; $columnIndex++)
    {
        // Pick tree in the forest
        $tree = $forest[$rowIndex][$columnIndex];

        // Look north
        $scoreN = 0;
        $otherTreeRowIndex = $rowIndex;
        do
        {
            $scoreN++;

            $otherTreeRowIndex--;
            $otherTree = $forest[$otherTreeRowIndex][$columnIndex];
        }
        while ($otherTreeRowIndex > 0 AND $otherTree < $tree);

        // Look south
        $scoreS = 0;
        $otherTreeRowIndex = $rowIndex;
        do
        {
            $scoreS++;

            $otherTreeRowIndex++;
            $otherTree = $forest[$otherTreeRowIndex][$columnIndex];
        }
        while ($otherTreeRowIndex < $maxRowIndex AND $otherTree < $tree);

        // Look west
        $scoreW = 0;
        $otherTreeColumnIndex = $columnIndex;
        do
        {
            $scoreW++;

            $otherTreeColumnIndex--;
            $otherTree = $forest[$rowIndex][$otherTreeColumnIndex];
        }
        while ($otherTreeColumnIndex > 0 AND $otherTree < $tree);

        // Look east
        $scoreE = 0;
        $otherTreeColumnIndex = $columnIndex;
        do
        {
            $scoreE++;

            $otherTreeColumnIndex++;
            $otherTree = $forest[$rowIndex][$otherTreeColumnIndex];
        }
        while ($otherTreeColumnIndex < $maxColumnIndex AND $otherTree < $tree);

        $score = $scoreN * $scoreE * $scoreS * $scoreW;

        if ($score > $maxScore)
        {
            $maxScore = $score;
        }
    }
}

echo($maxScore);