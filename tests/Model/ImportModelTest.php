<?php

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use App\Model\ImportModel;

class ImportModelTest extends TestCase {

    public function testImport() {
        $m = new ImportModel();

        $arLines = array(
            "(A) Thank Mom for the meatballs @phone",
            "(B) Schedule Goodwill pickup +GarageSale @phone",
            "Post signs around the neighborhood +GarageSale",
            "@GroceryStore Eskimo pies"
        );

        $resPriority = array("A", "B", "", "");
        $resCompletion = array(false, false, false, false);
        $resMessage = array(
            "Thank Mom for the meatballs",
            "Schedule Goodwill pickup",
            "Post signs around the neighborhood",
            "Eskimo pies"
        );
        $resProject = array(array(), array("GarageSale"), array("GarageSale"), array());
        $resTags = array(array("phone"), array("phone"), array(), array("GroceryStore"));

        $counter = 0;
        foreach ($arLines as $line) {
            $task = $m->parse($line);
            $this->assertEquals($resCompletion[$counter], $task->isCompletion());
            $this->assertEquals($resPriority[$counter], $task->getPriority());
            $counter++;
        }

    }

}

