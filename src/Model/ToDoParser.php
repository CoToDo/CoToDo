<?php

namespace App\Model;


class ToDoParser {

    private const REG_DATE_TIME = "/^[1-9][0-9]{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";
//    private const REG_DATE_TIME = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/";

    public function parse(string $line) {
        $task = new TaskTO();
        $arLine = explode(" ", $line);


        $this->getCompletion($arLine, $task);
        $this->getPriority($arLine, $task);
        $this->getDates($arLine, $task);
        $this->getProjects($arLine, $task);
        $this->getTags($arLine, $task);
        $this->getMessage($arLine, $task);

        return $task;
    }

    /**
     * Get completion from string and set it to TaskTO object
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getCompletion(&$outArLine, $task) {
        if (isset($outArLine[0])) {
            if ($outArLine[0] == "x") {
                $task->setCompletion(true);
                array_shift($outArLine);
            } else {
                $task->setCompletion(false);
            }
        } else {
            // TODO wrong line
        }
    }

    /**
     * get Priority from string and set it to TaskTO object
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getPriority(&$outArLine, $task) {
        if (isset($outArLine[0])) {
            if (preg_match("/^\([A-Z]\)$/", $outArLine[0])) {
                $task->setPriority($outArLine[0][1]);
                array_shift($outArLine);
            }
        } else {
            // TODO wrong line
        }
    }

    /**
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getDates(&$outArLine, $task) {
        if (isset($outArLine[0])) {
            if (preg_match(self::REG_DATE_TIME, $outArLine[0])) {
                if ($task->isCompletion()) {
                    $task->setCompletionDate(new \DateTime($outArLine[0]));
                } else {
                    $task->setCreateDate(new \DateTime($outArLine[0]));
                }
                array_shift($outArLine);
                if (isset($outArLine[0])) {
                    if (preg_match(self::REG_DATE_TIME, $outArLine[0])) {
                        $task->setCreateDate(new \DateTime($outArLine[0]));
                        array_shift($outArLine);
                    }
                } else {
                    // TODO wrong line
                }
            }
        } else {
            // TODO wrong line
        }
    }

    /**
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getProjects(&$outArLine, $task) {
        $projects = array();
        $counter = 0;
        foreach ($outArLine as $item) {
            if ($this->isProject($item)) {
                $projects[] = substr($item, 1);
                array_splice($outArLine, $counter, 1);
                $counter--;
            }
            $counter++;
        }
        $task->setProjects($projects);
    }

    /**
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getTags(&$outArLine, $task) {
        $tags = array();
        $counter = 0;
        foreach ($outArLine as $item) {
            if ($this->isTag($item)) {
                $tags[] = substr($item, 1);
                array_splice($outArLine, $counter, 1);
                $counter--;
            }
            $counter++;
        }
        $task->setTags($tags);
    }

    /**
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getMessage(&$outArLine, $task) {
        $strMessage = "";
        $counter = 0;
        foreach ($outArLine as $item) {
            if ($this->isMessage($item)) {
                $strMessage .= $item . " ";
            } else {
                break;
            }
            $counter++;
        }
        $strMessage = substr($strMessage, 0, -1);
        array_slice($outArLine, $counter);
        $task->setName($strMessage);
    }

    private function isMessage($strItem) {
        return !$this->isProjectTagOrSpecial($strItem);
    }

    private function isProject($strItem) {
        if (isset($strItem[0]) && $strItem[0] == '+') {
            return true;
        }
        return false;
    }

    private function isTag($strItem) {
        if (isset($strItem[0]) && $strItem[0] == '@') {
            return true;
        }
        return false;
    }

    private function isSpecial($strItem) {
        if (strpos($strItem, ':') !== false) {
            return true;
        }
        return false;
    }

    private function isProjectTagOrSpecial($strItem) {
        return $this->isProject($strItem) || $this->isTag($strItem) || $this->isSpecial($strItem);
    }

}