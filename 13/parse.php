<?php

function parse($txt)
{
    $oldTxt = $txt;

    [$parsed, $list] = parseList($txt);
    $txt = substr($txt, strlen($parsed));

    if ($txt)
    {
        echo('Error: unexpected text [' . $txt . '] (1)<br />' . PHP_EOL);
    }

    return $list;
}

function parseList($txt)
{
    $oldTxt = $txt;
    $result = null;

    // Lookahead char
    $c = substr($txt, 0, 1);
    if ($c == '[')
    {
        // Start of list

        // Advance input
        $txt = substr($txt, 1);
        [$parsed, $elements] = parseElements($txt);
        if ($elements === null)
        {
            $result = [];
        }
        else
        {
            $result = $elements;
        }

        // Advance input
        $txt = substr($txt, strlen($parsed));

        // Lookahead char
        $c = substr($txt, 0, 1);
        if ($c == ']')
        {
            // End of list

            // Advance input
            $txt = substr($txt, 1);
        }
        else
        {
            echo('Error: unexpected char [' . $c . '] (2)<br />' . PHP_EOL);
        }
    }
    else
    {
        echo('Error: unexpected char [' . $c . '] (1)<br />' . PHP_EOL);
    }

    return [substr($oldTxt, 0, strlen($oldTxt) - strlen($txt)), $result];
}


function parseElements($txt)
{
    $oldTxt = $txt;
    $result = null;

    // Lookahead char
    $c = substr($txt, 0, 1);
    if ($c == ']')
    {
        // Empty elements
    }
    else
    {
        [$parsed, $element] = parseElement($txt);
        $result = [$element];

        // Advance input
        $txt = substr($txt, strlen($parsed));

        // Lookahead char
        $c = substr($txt, 0, 1);
        if ($c == ',')
        {
            // Advance input
            $txt = substr($txt, 1);

            [$parsed, $elements] = parseElements($txt);
            $result = array_merge($result, $elements);

            // Advance input
            $txt = substr($txt, strlen($parsed));
        }
    }

    return [substr($oldTxt, 0, strlen($oldTxt) - strlen($txt)), $result];
}

function parseElement($txt)
{
    $oldTxt = $txt;

    // Lookahead char
    $c = substr($txt, 0, 1);
    if (in_array($c, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']))
    {
        do
        {
            $txt = substr($txt, 1);
            $c = substr($txt, 0, 1);
        }
        while (in_array($c, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']));

        $parsed = substr($oldTxt, 0, strlen($oldTxt) - strlen($txt));

        return [$parsed, intval($parsed)];
    }
    else
    {
        [$parsed, $list] = parseList($txt);

        // Advance input
        $txt = substr($txt, strlen($parsed));
        
        return [substr($oldTxt, 0, strlen($oldTxt) - strlen($txt)), $list];
    }
}