<?php
namespace AppBundle\Entity;

use Doctrine\Orm\Mapping as ORM;

class EduMat
{
    private $id;
    private $name;
    private $status;
    private $dateCreated;
    private $dateFinished;
    private $desc;
    private $recipient;

    public function __construct($name, $status, $dateCreated, $dateFinished, $desc, $recipient)
    {
        $this->name = htmlentities($name);
        $this->status = htmlentities($status);
        $this->dateCreated = htmlentities($dateCreated);
        $this->dateFinished = htmlentities($dateFinished);
        $this->desc = htmlentities($desc);
        $this->recipient = htmlentities($recipient);
    }

    public function getID()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStatus()
    {
        return $this->status;

    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getDateStarted()
    {
        return $this->dateFinished;
    }

    public function getDesc()
    {
        return $this->desc;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }
}