<?php


abstract class Vacuno {
    protected string $tipo;
    protected string $caravana;
    protected string $raza;
    protected int $edad;
    protected float $peso;
    protected string $historial;

    public function __construct($tipo, $caravana, $raza, $edad, $peso) {
        $this->tipo = $tipo;
        $this->caravana = $caravana;
        $this->raza = $raza;
        $this->edad = $edad;
        $this->peso = $peso;
        $this->historial = "";

    }

    public function agregarHistorial(string $anotacion){
        $this->historial .= "- " . $anotacion . PHP_EOL;
    }
    public function getTipo() {
        return $this->tipo;
    }

    public function getCaravana() {
        return $this->caravana;
    }
    public function getRaza() {
        return $this->raza;
    }

    public function getEdad() {
        return $this->edad;
    }

    public function getPeso() {
        return $this->peso;
    }
}