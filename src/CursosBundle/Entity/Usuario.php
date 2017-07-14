<?php

namespace CursosBundle\Entity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Usuario
 */
class Usuario implements UserInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $rol;

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

    // Para la autenticación del usuario
    public function getUsername()
    {
        return $this->apodo;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return array($this->getRol());
        #return array("ROLE_ADMIN");
    }

    public function eraseCredentials()
    {
        
    }
    // Fin de la autenticación


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
     * Set rol
     *
     * @param string $rol
     *
     * @return Usuario
     */
    public function setRol($rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return string
     */
    public function getRol()
    {
        return $this->rol;
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

