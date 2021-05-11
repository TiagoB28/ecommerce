<?php

use Twig\TwigFunction;
use App\Models\Flash;

$message = new TwigFunction('message', function ($index) {
    echo Flash::get($index);
});

return [
    $message
];