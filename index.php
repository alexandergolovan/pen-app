<?php

class Rod
{
    const COLOR_BLUE   = "[034m";
    const COLOR_RED    = "[031m";
    const COLOR_GREEN  = "[032m";
    const COLOR_GREY   = "[030m";
    const COLOR_YELLOW = "[033m";
    const COLOR_WHITE  = "[037m";

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

    public function getVolume () {
        return $this->volume;
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
    public static $ERROR_PEN_CLOSE = 'Pen is closed!';

    protected $body;
    protected $brand;
    protected $rod;
    protected $isOpen = true;

    public function __construct (Body $body, Rod $rod, $brand) {
        $this->body = $body;
        $this->rod = $rod;
        $this->brand = $brand;
    }

    public function isOpened() {
        return true;
    }

    protected function writeCharacter ($symbol) {
        echo chr(27) . $this->rod->getColor() . $symbol . chr(27) . "[0m";
    }

    public function write ($text) {
        $textLength = strlen($text);
        for ($i = 0; $i < $textLength; $i++) {
            if (!$this->rod->isEmpty()) {
                if ($this->isOpened()) {
                    if ($text[$i] !== ' ') {
                        $this->rod->useInk();
                        $this->writeCharacter($text[$i]);
                    } else {
                        echo ' ';
                    }
                } else {
                    echo " ". self::$ERROR_PEN_CLOSE . "\n";
                    break;
                }
            } else {
                echo " " . Rod::$ERROR_ROD_EMPTY . "\n" ;
                break;
            }
        }
    }
}

class AutomaticPen extends Pen {
    public function __construct(Body $body, Rod $rod, $brand)
    {
        parent::__construct($body, $rod, $brand);
        $this->isOpen = false;
    }

    public function click () {
        $this->isOpen = !$this->isOpen;
    }

    public function isOpened () {
        return (bool)$this->isOpen;
    }
}

class MechanicPencil extends AutomaticPen {
    public static $ERROR_FULL_PUSHED = 'All rod is pushed out!!!';

    const PUSH_OUT_LENGTH = 10;

    private $openedVolume;

    public function __construct(Body $body, Rod $rod, $brand)
    {
        parent::__construct($body, $rod, $brand);
    }

    public function isOpened()
    {
        return $this->openedVolume > 0;
    }

    public function click () {
        if (($this->rod->getVolume() - $this->openedVolume) !== 0) {
            $this->isOpen = true;
            $this->openedVolume += min ([
                self::PUSH_OUT_LENGTH, ($this->rod->getVolume() - $this->openedVolume)
                ]);
        } else {
            echo " " . self::$ERROR_FULL_PUSHED . "\n";
        }
    }

    protected function writeCharacter($symbol) {
        $this->openedVolume--;
        if (!$this->openedVolume) {
            $this->isOpen = false;
        }

        parent::writeCharacter($symbol);

        return $this->isOpened();
    }
}

class MultiRodPen extends AutomaticPen {
    
}

class PenBuilder {
    public $brand = "Bic";
    public $bodyColor = "yellow";
    public $rodColor = Rod::COLOR_BLUE;
    public $rodVolume = 1000;
    public $bodyMaterial = 'plastic';

    public function build () {
        $rod = $this->createRod();
        $body = $this->createBody();

        return $this->createPen ($body, $rod);
    }

    protected function createPen ($body, $rod) {
        return new Pen ($body, $rod, $this->brand);
    }

    protected function createBody () {
        return new Body($this->bodyColor, $this->bodyMaterial);
    }

    protected function createRod () {
        return new Rod($this->rodColor, $this->rodVolume);
    }
}

class AutomaticPenBuilder extends PenBuilder {
    protected function createPen ($body, $rod) {
        return new AutomaticPen ($body, $rod, $this->brand);
    }
}

class MechanicPencilBuilder extends AutomaticPenBuilder {
    protected function createPen ($body, $rod) {
        return new MechanicPencil ($body, $rod, $this->brand);
    }
}

class PenFactory {
    public function createBicPenRed() {
        $penBuilder = new PenBuilder();

        $penBuilder->rodColor = Rod::COLOR_RED;
        $penBuilder->rodVolume = 500;
        $penBuilder->bodyColor = 'orange';
        $penBuilder->brand = 'Bic';

        return $penBuilder->build();
    }

    public function createAutomaticBicPenRed() {
        $automaticPenBuilder = new AutomaticPenBuilder();
        $automaticPenBuilder->rodColor = Rod::COLOR_RED;
        $automaticPenBuilder->rodVolume = 500;
        $automaticPenBuilder->bodyColor = 'orange';
        $automaticPenBuilder->brand = 'Bic';

        return $automaticPenBuilder->build();
    }

    public function createSimpleMechanicPencil() {
        $mechanicPencilBuilder = new MechanicPencilBuilder();
        $mechanicPencilBuilder->rodColor = Rod::COLOR_GREY;
        $mechanicPencilBuilder->rodVolume = 50;
        $mechanicPencilBuilder->bodyColor = 'yellow';
        $mechanicPencilBuilder->brand = 'Bic';

        return $mechanicPencilBuilder->build();
    }
}

$penFactory = new PenFactory();
$bicPenRed = $penFactory->createBicPenRed();

$bicPenRed->write('This is simple pen writing very well');

$automaticBicPenRed = $penFactory->createAutomaticBicPenRed();

$automaticBicPenRed->write('This is automatic is closed');
$automaticBicPenRed->click();
$automaticBicPenRed->write('This is automatic is opened');

$simpleMechanicPencil = $penFactory->createSimpleMechanicPencil();
$simpleMechanicPencil->write("Mechanic pencil doesn't write");
$simpleMechanicPencil->click();
$simpleMechanicPencil->write("Mechanic pencil writes not long massages");

$simpleMechanicPencil->click();
$simpleMechanicPencil->click();
$simpleMechanicPencil->click();
$simpleMechanicPencil->click();
$simpleMechanicPencil->click();
$simpleMechanicPencil->write("Mechanic pencil writes long message");




