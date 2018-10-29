<?php

namespace App\Tests\Model;

use App\Entity\Project;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Work;
use App\Model\ImportModel;
use App\Model\ImportSync;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TeamRepository;
use App\Repository\WorkRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class ImportModelTest extends TestCase {

    /**
     * Not testing saving to db
     */
    public function testImportModel() {
        $em = $this->createMock(ObjectManager::class);
        $projectRepository = $this->createMock(ProjectRepository::class);
        $taskRepository = $this->createMock(TaskRepository::class);
        $teamRepository = $this->createMock(TeamRepository::class);
        $workRepository = $this->createMock(WorkRepository::class);
        $tagRepository = $this->createMock(Tag::class);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($projectRepository, $taskRepository, $teamRepository, $workRepository, $tagRepository);

        $user = new User();
        $user->setMail("kokos1@gmai.com");
        $user->setLastName("k");

        $strLine = "Hello World!" . PHP_EOL . "(A) composer dump-autoload" . PHP_EOL . "x Sníh +ZimaJeTady @Hurá";
        $strLineNext = "orld!" . PHP_EOL . "(B) composer install" . PHP_EOL . "+ZimaJeTady @Hurá";

        $import = new ImportModel($em, $user);
        $this->assertEquals(array(), $import->importFromString($strLine));
        $this->assertEquals(array("+ZimaJeTady @Hurá"), $import->importFromString($strLineNext));
    }

}