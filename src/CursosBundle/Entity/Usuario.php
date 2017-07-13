<?php

namespace CursosBundle\Entity;

/**
 * Usuario
 */
class Usuario
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $apodo;

    /**
     * @var string
     */
    private $correo;

    /**
     * @var string
     */
    private $password;

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
     * Set apodo
     *
     * @param string $apodo
     *
     * @return Usuario
     */
    public function setApodo($apodo)
    {
        $this->apodo = $apodo;

        return $this;
    }

    /**
     * Get apodo
     *
     * @return string
     */
    public function getApodo()
    {
        return $this->apodo;
    }

    /**
     * Set correo
     *
     * @param string $correo
     *
     * @return Usuario
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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

