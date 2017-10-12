<?php
namespace src\AppBundle\Entity;
/**
 * Class1 short summary.
 *
 * Class1 description.
 *
 * @version 1.0
 * @author cst206
 */
class OOPs
{
    private $OOPsID;

    private $binSerial;

    private $problemType;

    private $status;

    private $description;

    private $image;

    function __construct( $binSerial, $problemType, $status = 'not started', $description='', $image='' )
    {
        $this->binSerial = htmlentities($binSerial);
        $this->problemType = htmlentities($problemType);
        $this->status = htmlentities($status);
        $this->description = htmlentities($description);
        $this->image = $image;
    }

}