<?php

namespace OkaniYoshiii\Framework;

function array_every(array $items, callable $callback) : bool
{
    $matchedTypes = array_filter($items, fn(mixed $item) => $callback($item));

    return count($matchedTypes) === count($items);
}