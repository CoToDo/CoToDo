<?php

namespace App\Model;

use App\Constants;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Task;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Work;
use DateTimeInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestModel {

    const LANNISTER = 'Lannister';
    const GREYJOY = 'Greyjoy';
    const PASSWORD = '1234';

    private $em;
    private $passWordEncoder;
    private $dateTime;

    /**
     * TestModel constructor.
     * @param $em
     * @param $passWordEncoder
     */
    public function __construct(ObjectManager $em, UserPasswordEncoderInterface $passWordEncoder) {
        $this->em = $em;
        $this->passWordEncoder = $passWordEncoder;
        $this->dateTime = new \DateTime('now');
        $this->dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
    }

    public function addTestingData() {
        try {
        $userJaime = $this->buildUser('Jaime', $this::LANNISTER, $this::PASSWORD, 'jaime@co.todo');
        $userCersei = $this->buildUser('Cercei', $this::LANNISTER, $this::PASSWORD, 'cersei@co.todo');
        $userTywin = $this->buildUser('Tywin', $this::LANNISTER, $this::PASSWORD, 'tywin@co.todo');
        $teamLannisters = $this->buildTeam($this::LANNISTER);
        $roleJaime = $this->buildRole($teamLannisters, Constants::ADMIN, $userJaime);
        $roleCersei = $this->buildRole($teamLannisters, Constants::ADMIN, $userCersei);
        $roleTywin = $this->buildRole($teamLannisters, Constants::LEADER, $userTywin);
        $projectToSaveSevenKingdoms = $this->buildProject($teamLannisters, 'Save The Westeros', 'Restore peace an prosperity to The Westeros');
        $taskDragons = $this->buildTask($projectToSaveSevenKingdoms, 'Kill dragons', 'A');
        $taskFindBlackFish = $this->buildTask($projectToSaveSevenKingdoms, 'Find Black Fish', 'C');
        $workBlackFish = $this->buildWork($userJaime, $taskFindBlackFish);
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $dateTime = $dateTime->add(new \DateInterval('PT4H'));
        $workDragons = $this->buildWork($userCersei, $taskDragons, $dateTime);
        $workDragonsNext = $this->buildWork($userCersei, $taskDragons, null, $dateTime);

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return;
        }


        $userTheon = $this->buildUser('Theon', 'Greyjoy', $this::PASSWORD, 'theon@co.todo');
        $userVictarion = $this->buildUser('Victarion', $this::GREYJOY, $this::PASSWORD, 'victarion@co.todo');
        $userBalon = $this->buildUser('Balon', $this::GREYJOY, $this::PASSWORD, 'balon@co.todo');
        $userEuron = $this->buildUser('Euron', $this::GREYJOY, $this::PASSWORD, 'euron@co.todo');
        $teamGrayjoys = $this->buildTeam($this::GREYJOY);
        $roleEuron = $this->buildRole($teamGrayjoys, Constants::LEADER, $userEuron);
        $roleTheon = $this->buildRole($teamGrayjoys, Constants::USER, $userTheon);
        $roleBalon = $this->buildRole($teamGrayjoys, Constants::USER, $userBalon);
        $roleVictarion = $this->buildRole($teamGrayjoys, Constants::ADMIN, $userVictarion);


        $this->em->persist($userTheon);
        $this->em->persist($userVictarion);
        $this->em->persist($userEuron);
        $this->em->persist($userBalon);
        $this->em->persist($teamGrayjoys);

        $this->em->persist($workDragonsNext);
        $this->em->persist($taskDragons);
        $this->em->persist($workDragons);
        $this->em->persist($taskFindBlackFish);
        $this->em->persist($workBlackFish);
        $this->em->persist($projectToSaveSevenKingdoms);
        $this->em->persist($roleJaime);
        $this->em->persist($roleCersei);
        $this->em->persist($roleTywin);
        $this->em->persist($userJaime);
        $this->em->persist($userCersei);
        $this->em->persist($userTywin);
        $this->em->persist($teamLannisters);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @param Task $task
     * @param DateTimeInterface|null $endDate
     * @return Work
     */
    private function buildWork(User $user, Task $task, \DateTimeInterface $endDate = null, \DateTimeInterface $startDate = null) {
        if ($startDate == null) {
            $startDate = $this->dateTime;
        }
        $work = new Work();
        $work->setUser($user);
        $work->setTask($task);
        $work->setStartDate($startDate);
        $work->setDescription('');
        if($endDate != null) {
            $work->setEndDate($endDate);
        }
        return $work;
    }

    /**
     * @param Project $project
     * @param String $name
     * @param String $priority
     * @return Task
     * @throws \Exception
     */
    private function buildTask(Project $project, String $name, String $priority) {
        $task = new Task();
        $task->setName($name);
        $task->setPriority($priority);
        $task->setProject($project);
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $dateTime = $dateTime->add(new \DateInterval('P2D'));
        $task->setDeadline($dateTime);
        $task->setCreateDate($dateTime);
        return $task;
    }
    /**
     * @param Team $team
     * @param String $name
     * @param string $text description
     * @return Project
     */
    private function buildProject(Team $team, String $name, $text = '') {
        $project = new Project();
        $project->setTeam($team);
        $project->setName($name);
        $project->setCreateDate($this->dateTime);
        $project->setDescription($text);
        return $project;
    }
    /**
     * @param Team $team
     * @param String $type
     * @param User $user
     * @return Role
     */
    private function buildRole(Team $team, String $type, User $user) {
        $role = new Role();
        $role->setTeam($team);
        $role->setType($type);
        $role->setUser($user);
        return $role;
    }

    /**
     * @param String $name
     * @param String $lastName
     * @param String $password
     * @param String $mail
     * @return User
     */
    private function buildUser(String $name, String $lastName, String $password, String $mail) {
        $user = new User();
        $user->setName($name);
        $user->setLastName($lastName);
        $user->setPlainPassword($password);
        $user->setMail($mail);
        $password = $this->passWordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        return $user;
    }

    /**
     * @param String $name
     * @return Team
     */
    private function buildTeam(String $name) {
        $team = new Team();
        $team->setName($name);
        return $team;
    }


}