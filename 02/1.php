<?php

// Init vars
$input_file = 'input.txt';

// Outcome matrix (-1 elf looses, 0 draw, 1 elf wins)
$result['R']['R'] = 0;
$result['R']['P'] = -1;
$result['R']['S'] = 1;
$result['P']['R'] = 1;
$result['P']['P'] = 0;
$result['P']['S'] = -1;
$result['S']['R'] = -1;
$result['S']['P'] = 1;
$result['S']['S'] = 0;

$translate_elf['A'] = 'R';
$translate_elf['B'] = 'P';
$translate_elf['C'] = 'S';

$translate_me['X'] = 'R';
$translate_me['Y'] = 'P';
$translate_me['Z'] = 'S';

$score = 0;


// Load input
$handle = fopen($input_file, "r");
if ($handle)
{
    // Read input, line by line
    while (($line = fgets($handle)) !== false)
    {
        // Split input
        list($elf_plays, $me_plays) = explode(' ', trim($line));

        // Score for my play
        switch($translate_me[$me_plays])
        {
            case 'R':
                $score += 1;
                break;
            case 'P':
                $score += 2;
                break;
            case 'S':
                $score += 3;
                break;
            default:
                echo('Error (1)');
                exit();
                break;
        }

        // Score for round outcome
        $round = $result[$translate_elf[$elf_plays]][$translate_me[$me_plays]];
        switch ($round)
        {
            case -1:
                $score += 6; 
                break;
            case 0:
                $score += 3; 
                break;
            case 1:
                break;
            default:
                echo('Error (2)');
                exit();
                break;
        }
    }

    fclose($handle);
}

echo($score);