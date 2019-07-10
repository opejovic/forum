<?php


namespace App\Inspections;


use Exception;

class KeyHeldDown
{
    /**
     * Detect if the body contains key held down characters.
     *
     * @param $body
     *
     * @throws \Exception
     */
    public function detect($body)
    {
        if (preg_match('/(.)\\1{4,}/', $body, $matches)) {
            throw new Exception('Your reply contains spam.');
        }
    }
}
