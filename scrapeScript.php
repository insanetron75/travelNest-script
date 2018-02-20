<?php
require_once('Scrapper.php');

$properties = [
    "https://www.airbnb.co.uk/rooms/14531512?s=51",
    "https://www.airbnb.co.uk/rooms/19278160?s=51",
    "https://www.airbnb.co.uk/rooms/19292873?s=51"
];

foreach ($properties as $property) {
    $scrapper = new Scrapper($property);
    echo PHP_EOL . json_encode($scrapper->getOutput()) . PHP_EOL;
}
