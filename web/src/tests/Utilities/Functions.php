<?php

/**
 * Create factory
 *
 * @param  mixed $class
 * @param  mixed $attributes
 * @param  mixed $times
 * @return mixed
 */
function create($class, $attributes = [], $times = 1)
{
    if ($times > 1) {
        return $class::factory()->count($times)->create($attributes);
    }

    return $class::factory()->create($attributes);
}
