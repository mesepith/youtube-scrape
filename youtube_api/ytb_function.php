<?php
/**
 * Convert ISO 8601 values like P2DT15M33S
 * to a total value of seconds.
 *
 * @param string $ISO8601
 */

/*
 *Return ytb time in seconds
 * */
function ISO8601ToSeconds($ISO8601){
    $interval = new \DateInterval($ISO8601);

    return ($interval->d * 24 * 60 * 60) +
        ($interval->h * 60 * 60) +
        ($interval->i * 60) +
        $interval->s;
}



?>
