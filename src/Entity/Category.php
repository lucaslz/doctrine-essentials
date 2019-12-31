<?php

namespace App\Entity;

/**
 * @Entity
 * @Table(name="categories")
 */
class Category {

    /**
     * @id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=100)
     */
    private $name;
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param [string] $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}