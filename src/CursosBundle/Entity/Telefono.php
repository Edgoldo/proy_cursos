<?php

namespace CursosBundle\Entity;

/**
 * Telefono
 */
class Telefono
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $numero;

    /**
     * @var int
     */
    private $persona;

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
     * Set numero
     *
     * @param string $numero
     *
     * @return Telefono
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set persona
     *
     * @param integer $persona
     *
     * @return Usuario
     */
    public function setPersona(\CursosBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return int
     */
    public function getPersona()
    {
        return $this->persona;
    }
}

