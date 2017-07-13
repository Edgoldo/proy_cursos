<?php

namespace CursosBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Curso
 */
class Curso
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $titulo;

    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var int
     */
    private $duracion;

    /**
     * @var int
     */
    private $participantes;

    // Atributos para realizar las relaciones

    protected $personaCurso;

    public function __construct(){
        $this->personaCurso = new ArrayCollection();
    }

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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Curso
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Curso
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set duracion
     *
     * @param integer $duracion
     *
     * @return Curso
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * Get duracion
     *
     * @return int
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Set participantes
     *
     * @param integer $participantes
     *
     * @return Curso
     */
    public function setParticipantes($participantes)
    {
        $this->participantes = $participantes;

        return $this;
    }

    /**
     * Get participantes
     *
     * @return int
     */
    public function getParticipantes()
    {
        return $this->participantes;
    }

    /**
     * Get personaCurso
     *
     * @return int
     */
    public function getPersonaCurso()
    {
        return $this->personaCurso;
    }
}

