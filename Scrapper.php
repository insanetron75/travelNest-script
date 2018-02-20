<?php
namespace AppBundle\Tools;

use Symfony\Component\DomCrawler\Crawler;

class Scrapper
{
    protected $link;
    protected $name;
    protected $type;
    protected $bedroomCount;
    protected $bathroomCount;
    protected $amenities = [];

    public function __construct($link)
    {
        $this->setLink($link);
        $this->buildObject();
    }

    protected function buildObject()
    {
        ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
        $html = file_get_contents($this->link);

        if (!empty($html)) {
            $crawler = new Crawler($html);
            $this->scrapeName($crawler);
            $this->scrapeType($crawler);
            $this->scrapeBedrooms($crawler);
            $this->scrapeBathroom($crawler);
            $this->scrapeAmenities($crawler);
        }
    }

    public function scrapeName(Crawler $crawler)
    {
        $name = $crawler->filter(
            '#summary > div._2h22gn > div._1kzvqab3 > div:nth-child(2) > div:nth-child(1) > div:nth-child(3) > div > div._1hpgssa1 > div:nth-child(1) > div > span > h1'
        )->text();

        $this->setName($name);
    }

    protected function scrapeType(Crawler $crawler)
    {
        $type = $crawler->filter(
            '#summary > div._2h22gn > div._1kzvqab3 > div:nth-child(2) > div:nth-child(1) > a > div > span > span > span'
        )->text();

        $this->setType($type);
    }

    protected function scrapeBedrooms(Crawler $crawler)
    {
        $bedroomString = $bedroomString = $crawler->filter(
            '#summary > div._2h22gn > div._1kzvqab3 > div:nth-child(2) > div:nth-child(1) > div:nth-child(5) > div > div:nth-child(2) > div > div:nth-child(2) > span'
        )->text();

        $array = explode(' ', trim($bedroomString));
        $this->setBedroomCount($array[0]);
    }

    protected function scrapeBathroom(Crawler $crawler)
    {
        $bathroomString = $crawler->filter(
            '#summary > div._2h22gn > div._1kzvqab3 > div:nth-child(2) > div:nth-child(1) > div:nth-child(5) > div > div:nth-child(4) > div > div:nth-child(2) > span'
        )->text();
        $array = explode(' ', trim($bathroomString));
        $this->setBathroomCount((int)$array[0]);
    }

    protected function scrapeAmenities(Crawler $crawler)
    {
        $amenityHTML = $crawler->filter(
            '#room > div > div.room__dls > div._uy08umt > div > div._2h22gn > div > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > div > div > div:nth-child(2) > div:nth-child(1) > div '
        )->html();

        $stringList = strip_tags($amenityHTML);
        $amenitiesArray = explode(PHP_EOL, $stringList);
        $this->setAmenities(array_filter($amenitiesArray));
    }

    public function getOutput()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'bedrooms' => $this->getBedroomCount(),
            'bathrooms' => $this->getBathroomCount(),
            'amenities' => $this->getAmenities()
        ];
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getType():string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getBedroomCount()
    {
        return $this->bedroomCount;
    }

    public function setBedroomCount($bedroomCount)
    {
        $this->bedroomCount = $bedroomCount;
    }

    public function getBathroomCount():int
    {
        return $this->bathroomCount;
    }

    public function setBathroomCount(int $bathroomCount)
    {
        $this->bathroomCount = $bathroomCount;
    }

    public function getAmenities():array
    {
        return $this->amenities;
    }

    public function setAmenities(array $amenities)
    {
        $this->amenities = $amenities;
    }

}
