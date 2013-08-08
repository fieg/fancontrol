<?php

namespace Fieg\FanControl\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class TempReading
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected $temp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $datetimeReading;

    /**
     * @param \DateTime $datetimeReading
     */
    public function setDatetimeReading($datetimeReading)
    {
        $this->datetimeReading = $datetimeReading;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeReading()
    {
        return $this->datetimeReading;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param float $temp
     */
    public function setTemp($temp)
    {
        $this->temp = $temp;
    }

    /**
     * @return float
     */
    public function getTemp()
    {
        return $this->temp;
    }
}


