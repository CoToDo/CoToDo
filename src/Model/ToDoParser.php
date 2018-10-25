<?php

namespace App\Model;


use App\Exception\WrongLineFormatException;
use Symfony\Component\Dotenv\Exception\FormatException;

class ToDoParser {

    /** @var string regular expression for date in format YYYY-MM-DD */
    private const REG_DATE_TIME = "/^[1-9][0-9]{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";

    /**
     * Parse line and set data to TaskTO object
     * @param string $line
     * @return TaskTO
     * @throws WrongLineFormatException
     */
    public function parse(string $line) {
        $task = new TaskTO();
        $arLine = explode(" ", $line);

        $this->getCompletion($arLine, $task);
        $this->getPriority($arLine, $task);
        $this->getDates($arLine, $task);
        $this->getProjects($arLine, $task);
        $this->getTags($arLine, $task);
        $this->getMessage($arLine, $task);
        if (!$task->isPrioritySet()) {
            $this->getPriorityFromSpecial($arLine, $task);
        }
        $this->getDeadline($arLine, $task);
        return $task;
    }

    /**
     * Get completion from string and set it to TaskTO object
     * @param array $outArLine
     * @param TaskTO $task
     * @throws WrongLineFormatException
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
            throw new WrongLineFormatException();
        }
    }

    /**
     * get Priority from string and set it to TaskTO object
     * @param array $outArLine
     * @param TaskTO $task
     * @throws WrongLineFormatException
     */
    private function getPriority(&$outArLine, $task) {
        if (isset($outArLine[0])) {
            if (preg_match("/^\([A-Z]\)$/", $outArLine[0])) {
                $task->setPriority($outArLine[0][1]);
                array_shift($outArLine);
            }
        } else {
            throw new WrongLineFormatException();
        }
    }

    /**
     * Get Dates from string and set it to TaskTO object
     * @param array $outArLine
     * @param TaskTO $task
     * @throws WrongLineFormatException
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
                    throw new WrongLineFormatException();
                }
            }
        } else {
            throw new WrongLineFormatException();
        }
    }

    /**
     * Get projects from string and set it to TaskTO object
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
     * Get tags from string and set it to TaskTO object
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
     * Get message from string and set it to TaskTo object as name
     * @param array $outArLine
     * @param TaskTO $task
     * @throws WrongLineFormatException
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

        $strMessage = trim($strMessage);
        if (empty($strMessage)) {
            throw new WrongLineFormatException();
        }
        array_slice($outArLine, $counter);
        $task->setName($strMessage);
    }

    /**
     * Get priority as special value (pri:value)
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getPriorityFromSpecial(&$outArLine, $task) {
        $counter = 0;
        foreach ($outArLine as $item) {
            if ($this->isSpecialPriority($item)) {
                $task->setPriority(substr($item, 4));
                array_splice($outArLine, $counter, 1);
                break;
            }
        }
    }

    /**
     * Get deadline as special value (due:value)
     * @param array $outArLine
     * @param TaskTO $task
     * @throws WrongLineFormatException
     */
    private function getDeadline(&$outArLine, $task) {
        $counter = 0;
        foreach ($outArLine as $item) {
            if ($this->isSpecialDeadline($item)) {
                $deadline = substr($item, 4);
                if (!preg_match(self::REG_DATE_TIME, $deadline)) {
                    throw new WrongLineFormatException();
                }
                $task->setDeadline(new \DateTime($deadline));
                array_splice($outArLine, $counter, 1);
                break;
            }
        }
    }

    /**
     * Is message?
     * @param string $strItem
     * @return bool
     */
    private function isMessage($strItem) {
        return !$this->isProjectTagOrSpecial($strItem);
    }

    /**
     * Is Project? (Starts with '+')
     * @param string $strItem
     * @return bool
     */
    private function isProject($strItem) {
        if (isset($strItem[0]) && $strItem[0] == '+') {
            return true;
        }
        return false;
    }

    /**
     * Is Tag? (Starts with '@')
     * @param string $strItem
     * @return bool
     */
    private function isTag($strItem) {
        if (isset($strItem[0]) && $strItem[0] == '@') {
            return true;
        }
        return false;
    }

    /**
     * Is special? (key:vaue)
     * @param string $strItem
     * @return bool
     */
    private function isSpecial($strItem) {
        if (strpos($strItem, ':') !== false) {
            return true;
        }
        return false;
    }

    /** Is special priority? (pri:value)
     * @param string $strItem
     * @return bool
     */
    private function isSpecialPriority($strItem) {
        if (substr($strItem, 0, 4) == "pri:") {
            return true;
        }
        return false;
    }

    /**
     * Is Deadline? (due:value)
     * @param string $strItem
     * @return bool
     */
    private function isSpecialDeadline($strItem) {
        if (substr($strItem, 0, 4) == "due:") {
            return true;
        }
        return false;
    }
    /**
     * Is Project | Tag | Special?
     * @param string $strItem
     * @return bool
     */
    private function isProjectTagOrSpecial($strItem) {
        return $this->isProject($strItem) || $this->isTag($strItem) || $this->isSpecial($strItem);
    }

}
