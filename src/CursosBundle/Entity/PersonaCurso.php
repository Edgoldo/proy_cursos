<?php

namespace CursosBundle\Entity;

/**
 * PersonaCurso
 */
class PersonaCurso
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $persona;

    /**
     * @var int
     */
    private $curso;


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
     * Set persona
     *
     * @param integer $persona
     *
     * @return PersonaCurso
     */
    public function setpersona(\CursosBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return int
     */
    public function getpersona()
    {
        return $this->persona;
    }

    /**
     * Set curso
     *
     * @param integer $curso
     *
     * @return PersonaCurso
     */
    public function setCurso(\CursosBundle\Entity\Curso $curso = null)
    {
        $this->curso = $curso;

        return $this;
    }

    /**
     * Get curso
     *
     * @return int
     */
    public function getCurso()
    {
        return $this->curso;
    }
}

