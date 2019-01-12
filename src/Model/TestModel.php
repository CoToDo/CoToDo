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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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

    /**
     * @throws \Exception
     */
    private function addLannisters() {
        $userJaime = $this->buildUser('Jaime', $this::LANNISTER, $this::PASSWORD, 'jaime@co.todo');
        $userTyrion = $this->buildUser('Tyrion', $this::LANNISTER, $this::PASSWORD, 'tyrion@co.todo');
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
        $dateTimeOne = new \DateTime('now');
        $dateTimeOne->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $dateTimeOne = $dateTimeOne->add(new \DateInterval('PT4H'));
        $workDragons = $this->buildWorkWithDates($userCersei, $taskDragons, $dateTimeOne);
        $workDragonsNext = $this->buildWorkWithDates($userCersei, $taskDragons, null, $dateTimeOne);
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
        $this->em->persist($userTyrion);
        $this->em->persist($teamLannisters);
        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    private function addGreyjoys() {
        $userTheon = $this->buildUser('Theon', 'Greyjoy', $this::PASSWORD, 'theon@co.todo');
        $userAsa = $this->buildUser('Asa', 'Greyjoy', $this::PASSWORD, 'asa@co.todo');
        $userVictarion = $this->buildUser('Victarion', $this::GREYJOY, $this::PASSWORD, 'victarion@co.todo');
        $userBalon = $this->buildUser('Balon', $this::GREYJOY, $this::PASSWORD, 'balon@co.todo');
        $userEuron = $this->buildUser('Euron', $this::GREYJOY, $this::PASSWORD, 'euron@co.todo');
        $teamGrayjoys = $this->buildTeam($this::GREYJOY);
        $roleEuron = $this->buildRole($teamGrayjoys, Constants::LEADER, $userEuron);
        $roleTheon = $this->buildRole($teamGrayjoys, Constants::USER, $userTheon);
        $roleBalon = $this->buildRole($teamGrayjoys, Constants::USER, $userBalon);
        $roleVictarion = $this->buildRole($teamGrayjoys, Constants::ADMIN, $userVictarion);
        $projectSaltBurning = $this->buildProject($teamGrayjoys, 'Salt & Burning', '');
        $projectT = $this->buildProject($teamGrayjoys, 'T', 'T');
        $taskDenny = $this->buildTask($projectSaltBurning, 'Find Denny', 'A');
        $taskShields = $this->buildTask($projectSaltBurning, 'Take the Shields', 'B');
        $taskShields->setCompletionDate($this->getDateTimeWithInterval('PT4H'));
        $taskBalon = $this->buildTask($projectSaltBurning, 'Kill Balon', 'B');
        $taskFleet = $this->buildTask($projectSaltBurning, 'Fleet', 'A');
        $taskAsa = $this->buildTask($projectT, 'Save Asa', 'A');
        $workAsa = $this->buildWork($userTheon, $taskAsa);
        $workBalon = $this->buildWork($userEuron, $taskBalon);
        $workDenny = $this->buildWorkWithDates($userVictarion, $taskDenny, $this->getDateTimeWithInterval('P3D'), $this->dateTime);
        $workShields = $this->buildWorkWithDates($userEuron, $taskShields, $this->getDateTimeWithInterval('PT1H'), $this->dateTime);
        $workShieldsOne = $this->buildWorkWithDates($userEuron, $taskShields, $this->getDateTimeWithInterval('PT2H'), $this->getDateTimeWithInterval('PT1H'));
        $workShieldsTwo = $this->buildWorkWithDates($userEuron, $taskShields, $this->getDateTimeWithInterval('PT4H'), $this->getDateTimeWithInterval('PT2H'));
        $workDennyE = $this->buildWorkWithDates($userEuron, $taskDenny, $this->getDateTimeWithInterval('P2D'), $this->dateTime);
        $this->em->persist($workBalon);
        $this->em->persist($workShields);
        $this->em->persist($workDenny);
        $this->em->persist($workDennyE);
        $this->em->persist($workShieldsOne);
        $this->em->persist($workShieldsTwo);
        $this->em->persist($workAsa);
        $this->em->persist($taskAsa);
        $this->em->persist($projectT);
        $this->em->persist($roleEuron);
        $this->em->persist($roleTheon);
        $this->em->persist($roleBalon);
        $this->em->persist($roleVictarion);
        $this->em->persist($taskDenny);
        $this->em->persist($taskShields);
        $this->em->persist($taskBalon);
        $this->em->persist($taskFleet);
        $this->em->persist($projectSaltBurning);
        $this->em->persist($userTheon);
        $this->em->persist($userVictarion);
        $this->em->persist($userEuron);
        $this->em->persist($userBalon);
        $this->em->persist($userAsa);
        $this->em->persist($teamGrayjoys);
        $this->em->flush();
    }

    public function addTestingData() {
        try {
            $this->addLannisters();
            $this->addGreyjoys();
            return 'Testing Data loaded!';
        } catch (UniqueConstraintViolationException $e) {
            return 'Testing Data already loaded!';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function getDateTimeWithInterval(String $dateInterval) {
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return $dateTime->add(new \DateInterval($dateInterval));
    }

    /**
     * @param User $user
     * @param Task $task
     * @param DateTimeInterface|null $endDate
     * @return Work
     */
    private function buildWorkWithDates(User $user, Task $task, \DateTimeInterface $endDate = null, \DateTimeInterface $startDate = null) {
        if ($startDate == null) {
            $startDate = $this->dateTime;
        }
        $work = new Work();
        $work->setUser($user);
        $work->setTask($task);
        $work->setStartDate($startDate);
        $work->setDescription('');
        if ($endDate != null) {
            $work->setEndDate($endDate);
        }
        return $work;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return Work
     */
    private function buildWork(User $user, Task $task) {
        $work = new Work();
        $work->setUser($user);
        $work->setTask($task);
        $work->setDescription('');
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