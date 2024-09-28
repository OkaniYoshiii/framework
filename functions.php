<?php

function array_every(array $items, callable $callback)
{
    $matchedTypes = array_filter($items, fn(mixed $item) => $callback($item));

    return count($matchedTypes) === count($items);
}