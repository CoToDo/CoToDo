<?php

use App\Model\ToDoParser;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use App\Model\ImportModel;

class ImportModelTest extends TestCase {

    public function testImport() {
        $parser = new ToDoParser();

        $arLines = array(
            "(A) Thank Mom for the meatballs @phone",
            "(B) Schedule Goodwill pickup +GarageSale @phone",
            "Post signs around the neighborhood +GarageSale",
            "@GroceryStore Eskimo pies",
            "(A) Test tags and projects +projectX @phone @space +heavy @earth",
            "x 2018-12-12 2018-10-01 TEsting complete task @completed +X +Y +Z @cpl +x @dates"
        );

        $resPriority = array("A", "B", null, null, "A", null);
        $resCompletion = array(false, false, false, false, false, true);
        $resCreationDate = array(null, null, null, null, null, new DateTime("2018-10-01"));
        $resCompletionDate = array(null, null, null, null, null, new DateTime("2018-12-12"));
        $resMessage = array(
            "Thank Mom for the meatballs",
            "Schedule Goodwill pickup",
            "Post signs around the neighborhood",
            "Eskimo pies",
            "Test tags and projects",
            "TEsting complete task"
        );
        $resProject = array(array(), array("GarageSale"), array("GarageSale"), array(), array("projectX", "heavy"), array("X", "Y", "Z", "x"));
        $resTags = array(array("phone"), array("phone"), array(), array("GroceryStore"), array("phone", "space", "earth"), array("completed", "cpl", "dates"));

        $counter = 0;
        foreach ($arLines as $line) {
            $task = $parser->parse($line);
            $this->assertEquals($resCompletion[$counter], $task->isCompletion());
            $this->assertEquals($resPriority[$counter], $task->getPriority());
            $this->assertEquals($resCreationDate[$counter], $task->getCreateDate());
            $this->assertEquals($resCompletionDate[$counter], $task->getCompletionDate());
            $this->assertEquals($resProject[$counter], $task->getProjects());
            $this->assertEquals($resTags[$counter], $task->getTags());
            $this->assertEquals($resMessage[$counter], $task->getName());
            $counter++;
        }

    }

}

