<?php

class Rod
{
    const COLOR_BLUE = "[034m";
    const COLOR_RED = "[031m";
    const COLOR_GREEN = "[032m";
    const COLOR_GREY = "[030m";
    const COLOR_YELLOW = "[033m";
    const COLOR_WHITE = "[037m";

    public static $ERROR_ROD_EMPTY = "Opps rod is empty!";

    private $color;
    private $volume;                                        // Character number to write

    public function __construct($color, $volume) {
        $this->color = $color;
        $this->volume = $volume;
    }

    public function getColor () {
        return  $this->color;
    }

    public function isEmpty () {
        return $this->volume <= 0;
    }

    public function useInk () {
        $this->volume = $this->volume > 0 ? --$this->volume : 0;
    }
}

class Body
{
    private $color;
    private $material;

    public function __construct($color, $material = 'plastic')
    {
        $this->color = $color;
        $this->material = $material;
    }

    public function getColor() {
        return $this->color;
    }
}

class Pen {
    private $body;
    private $brand;
    private $rod;

    public function __construct (Body $body, Rod $rod, $brand) {
        $this->body = $body;
        $this->rod = $rod;
        $this->brand = $brand;
    }

    public function write ($text) {
        $textLength = strlen($text);
        for ($i = 0; $i < $textLength; $i++) {
            if (!$this->rod->isEmpty()) {
                if ($text[$i] !== ' ') {
                    echo chr(27) . $this->rod->getColor() . $text[$i] . chr(27) . "[0m";
                    $this->rod->useInk();
                } else {
                    echo ' ';
                }
            } else {
                echo " " . Rod::$ERROR_ROD_EMPTY . "\n" ;
                break;
            }
        }
    }
}

class PenBuilder {
    public $brand = "Bic";
    public $bodyColor = "yellow";
    public $rodColor = Rod::COLOR_BLUE;
    public $rodVolume = 1000;
    public $bodyMaterial = 'plastic';

    public function build () {
        $rod = new Rod($this->rodColor, $this->rodVolume);
        $body = new Body($this->bodyColor, $this->bodyMaterial);

        return new Pen ($body, $rod, $this->brand);
    }

    public function createBicPenRed() {
        $this->rodColor = Rod::COLOR_RED;
        $this->rodVolume = 500;
        $this->bodyColor = 'orange';
        $this->brand = 'Bic';

        return $this->build();
    }
}



$penBuilder = new PenBuilder();
$bicPenRed = $penBuilder->createBicPenRed();

$bicPenRed->write('This is simple pen writing very well');
