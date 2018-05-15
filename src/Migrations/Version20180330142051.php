<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180330142051 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE task (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, priority VARCHAR(2) NOT NULL, create_date DATETIME NOT NULL, completion_date DATETIME NOT NULL, deadline DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE team (id INTEGER NOT NULL, user_id INTEGER DEFAULT NULL, leader_id INTEGER DEFAULT NULL, team_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C4E0A61FA76ED395 ON team (user_id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F73154ED4 ON team (leader_id)');
        $this->addSql('CREATE TABLE work (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_534E68808DB60186 ON work (task_id)');
        $this->addSql('CREATE INDEX IDX_534E6880A76ED395 ON work (user_id)');
        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, leader_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, create_date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE73154ED4 ON project (leader_id)');
        $this->addSql('CREATE TABLE project_user (project_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(project_id, user_id))');
        $this->addSql('CREATE INDEX IDX_B4021E51166D1F9C ON project_user (project_id)');
        $this->addSql('CREATE INDEX IDX_B4021E51A76ED395 ON project_user (user_id)');
        $this->addSql('CREATE TABLE tag (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag_task (tag_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(tag_id, task_id))');
        $this->addSql('CREATE INDEX IDX_BC716493BAD26311 ON tag_task (tag_id)');
        $this->addSql('CREATE INDEX IDX_BC7164938DB60186 ON tag_task (task_id)');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, text VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526C8DB60186 ON comment (task_id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE work');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_task');
        $this->addSql('DROP TABLE comment');
    }
}
