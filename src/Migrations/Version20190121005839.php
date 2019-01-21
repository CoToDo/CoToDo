<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190121005839 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE role (id INTEGER NOT NULL, team_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, type VARCHAR(6) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_57698A6A296CD8AE ON role (team_id)');
        $this->addSql('CREATE INDEX IDX_57698A6AA76ED395 ON role (user_id)');
        $this->addSql('CREATE TABLE work (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_534E68808DB60186 ON work (task_id)');
        $this->addSql('CREATE INDEX IDX_534E6880A76ED395 ON work (user_id)');
        $this->addSql('CREATE TABLE notification (id INTEGER NOT NULL, user_id INTEGER NOT NULL, who_id INTEGER NOT NULL, date DATETIME NOT NULL, show BOOLEAN NOT NULL, link VARCHAR(255) NOT NULL, type VARCHAR(9) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF4E25B21 ON notification (who_id)');
        $this->addSql('CREATE TABLE app_users (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, auto_sync BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C25028245126AC48 ON app_users (mail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C250282435C246D5 ON app_users (password)');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, text VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526C8DB60186 ON comment (task_id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, parent_project_id INTEGER DEFAULT NULL, team_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, create_date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE5E237E06 ON project (name)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE512E9BCC ON project (parent_project_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE296CD8AE ON project (team_id)');
        $this->addSql('CREATE TABLE team (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE task (id INTEGER NOT NULL, project_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, priority VARCHAR(2) NOT NULL, create_date DATETIME NOT NULL, completion_date DATETIME DEFAULT NULL, deadline DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('CREATE TABLE tag (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag_task (tag_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(tag_id, task_id))');
        $this->addSql('CREATE INDEX IDX_BC716493BAD26311 ON tag_task (tag_id)');
        $this->addSql('CREATE INDEX IDX_BC7164938DB60186 ON tag_task (task_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE work');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_task');
    }
}
