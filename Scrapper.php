<?php

class Scrapper
{
    protected $link;
    protected $name;
    protected $type;
    protected $bedroomCount;
    protected $bathroomCount;
    protected $amenities;

    public function __construct($link)
    {
        $this->setLink($link);
        $this->buildObject();
    }

    protected function buildObject()
    {
        ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0)');
        $html = file_get_contents($this->link);

        if (!empty($html)) {
            $doc = new DOMDocument();
            libxml_use_internal_errors(true); //disable libxml errors
            $doc->loadHTML($html);
            $this->scrapeName($doc);
            $this->scrapeType($doc);
            $this->scrapeBedrooms($doc);
            $this->scrapeBathroom($doc);
            $this->scrapeAmenities($doc);
        }
    }

    public function scrapeName(DOMDocument $doc)
    {
        $this->setName(
            $this->retrieveValue(
                $doc,
                '//*[@id="summary"]/div[2]/div[1]/div[2]/div[1]/div[1]/div/div[1]/div[1]/div/span/h1'
            )
        );

    }

    protected function scrapeType(DOMDocument $doc)
    {
        $this->setType(
            $this->retrieveValue(
                $doc,
                '//*[@id="summary"]/div[2]/div[1]/div[2]/div[1]/a/div/span/span/span'
            )
        );
    }

    protected function scrapeBedrooms(DOMDocument $doc)
    {

        $this->setBedroomCount(
            $this->retrieveValue(
                $doc,
                '//*[@id="summary"]/div[2]/div[1]/div[2]/div[1]/div[3]/div[2]/div[2]/div/div[2]'
            )
        );
    }

    protected function scrapeBathroom(DOMDocument $doc)
    {
        $this->setBathroomCount(
            $this->retrieveValue(
                $doc,
                '//*[@id="summary"]/div[2]/div[1]/div[2]/div[1]/div[3]/div[2]/div[3]/div/div[2]'
            )
        );
    }

    protected function scrapeAmenities(DOMDocument $doc)
    {
        $amenities = $this->retrieveValue(
            $doc,
            '//*[@class="amenities"]/div[1]/div[2]/div[1]'
        );

        if (!strpos($amenities, ',')) {
            // For some reason the results are inconsistent.  There are occasions where it returns half of the
            // amenities as a single string rather than a comma delimited string.
            // If that happens try again and it should work
            $this->setAmenities('Error getting Amenities');
        } else {
            $this->setAmenities($amenities);
        }
    }

    protected function retrieveValue(DOMDocument $doc, string $xpath)
    {
        $xpathObject = new DOMXPath($doc);

        $nodeList = $xpathObject->query($xpath);
        if ($nodeList->length) {
            foreach ($nodeList as $row) {
                if ($row->childNodes->length) {
                    return $this->getChildNodeValues($row);
                } else {
                    return $row->nodeValue;
                }
            }
        }

        return 'Unable To Retrieve Value';
    }

    // If the returned node had child nodes, recursively go through children and
    // return them as a comma delimited string
    protected function getChildNodeValues($node) {
        $returnArray = [];
        foreach ($node->childNodes as $childNode) {
            if ($childNode->childNodes && $childNode->childNodes->length > 1) {
                $returnArray[] = $this->getChildNodeValues($childNode);
            } else {
                $returnArray[] = $childNode->nodeValue;
            }
        }
        return implode(',', $returnArray);
    }
    public function getOutput()
    {
        return [
            'name'      => $this->getName(),
            'type'      => $this->getType(),
            'bedrooms'  => $this->getBedroomCount(),
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getBedroomCount():string
    {
        return $this->bedroomCount;
    }

    public function setBedroomCount(string $bedroomCount)
    {
        $this->bedroomCount = $bedroomCount;
    }

    public function getBathroomCount():string
    {
        return $this->bathroomCount;
    }

    public function setBathroomCount(string $bathroomCount)
    {
        $this->bathroomCount = $bathroomCount;
    }

    public function getAmenities():string
    {
        return $this->amenities;
    }

    public function setAmenities(string $amenities)
    {
        $this->amenities = $amenities;
    }

}
