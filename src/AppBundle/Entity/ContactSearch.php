<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactSearch
 *
 * @ORM\Table(name="contactsearch")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SearchRepository")
 */
class ContactSearch
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="searchQuery", type="string", length=255)
     */
    private $searchQuery;

    /**
     * @var string
     *
     * @ORM\Column(name="results", type="string", length=255, nullable=true)
     */
    private $results;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set searchQuery
     *
     * @param string $searchQuery
     *
     * @return Search
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }

    /**
     * Get searchQuery
     *
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    /**
     * Set results
     *
     * @param string $results
     *
     * @return Search
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Get results
     *
     * @return string
     */
    public function getResults()
    {
        return $this->results;
    }
}

