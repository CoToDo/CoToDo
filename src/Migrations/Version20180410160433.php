<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180410160433 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE role (id INTEGER NOT NULL, team_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, type VARCHAR(6) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_57698A6A296CD8AE ON role (team_id)');
        $this->addSql('CREATE INDEX IDX_57698A6AA76ED395 ON role (user_id)');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP INDEX IDX_BC7164938DB60186');
        $this->addSql('DROP INDEX IDX_BC716493BAD26311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tag_task AS SELECT tag_id, task_id FROM tag_task');
        $this->addSql('DROP TABLE tag_task');
        $this->addSql('CREATE TABLE tag_task (tag_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(tag_id, task_id), CONSTRAINT FK_BC716493BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BC7164938DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO tag_task (tag_id, task_id) SELECT tag_id, task_id FROM __temp__tag_task');
        $this->addSql('DROP TABLE __temp__tag_task');
        $this->addSql('CREATE INDEX IDX_BC7164938DB60186 ON tag_task (task_id)');
        $this->addSql('CREATE INDEX IDX_BC716493BAD26311 ON tag_task (tag_id)');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395');
        $this->addSql('DROP INDEX IDX_9474526C8DB60186');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, task_id, user_id, text, date FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, text VARCHAR(255) NOT NULL COLLATE BINARY, date DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_9474526C8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment (id, task_id, user_id, text, date) SELECT id, task_id, user_id, text, date FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE INDEX IDX_9474526C8DB60186 ON comment (task_id)');
        $this->addSql('DROP INDEX IDX_534E6880A76ED395');
        $this->addSql('DROP INDEX IDX_534E68808DB60186');
        $this->addSql('CREATE TEMPORARY TABLE __temp__work AS SELECT id, task_id, user_id, description, start_date, end_date FROM work');
        $this->addSql('DROP TABLE work');
        $this->addSql('CREATE TABLE work (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL COLLATE BINARY, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_534E68808DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_534E6880A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO work (id, task_id, user_id, description, start_date, end_date) SELECT id, task_id, user_id, description, start_date, end_date FROM __temp__work');
        $this->addSql('DROP TABLE __temp__work');
        $this->addSql('CREATE INDEX IDX_534E6880A76ED395 ON work (user_id)');
        $this->addSql('CREATE INDEX IDX_534E68808DB60186 ON work (task_id)');
        $this->addSql('DROP INDEX IDX_C4E0A61F73154ED4');
        $this->addSql('DROP INDEX IDX_C4E0A61FA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__team AS SELECT id, name FROM team');
        $this->addSql('DROP TABLE team');
        $this->addSql('CREATE TABLE team (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO team (id, name) SELECT id, name FROM __temp__team');
        $this->addSql('DROP TABLE __temp__team');
        $this->addSql('DROP INDEX IDX_2FB3D0EE512E9BCC');
        $this->addSql('DROP INDEX IDX_2FB3D0EE73154ED4');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, parent_project_id, name, description, create_date FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, parent_project_id INTEGER DEFAULT NULL, team_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, description VARCHAR(255) NOT NULL COLLATE BINARY, create_date DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2FB3D0EE512E9BCC FOREIGN KEY (parent_project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2FB3D0EE296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project (id, parent_project_id, name, description, create_date) SELECT id, parent_project_id, name, description, create_date FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE512E9BCC ON project (parent_project_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE296CD8AE ON project (team_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE project_user (project_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(project_id, user_id))');
        $this->addSql('CREATE INDEX IDX_B4021E51A76ED395 ON project_user (user_id)');
        $this->addSql('CREATE INDEX IDX_B4021E51166D1F9C ON project_user (project_id)');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP INDEX IDX_9474526C8DB60186');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, task_id, user_id, text, date FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, text VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO comment (id, task_id, user_id, text, date) SELECT id, task_id, user_id, text, date FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526C8DB60186 ON comment (task_id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('DROP INDEX IDX_2FB3D0EE512E9BCC');
        $this->addSql('DROP INDEX IDX_2FB3D0EE296CD8AE');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, parent_project_id, name, description, create_date FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, parent_project_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, create_date DATETIME NOT NULL, leader_id INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO project (id, parent_project_id, name, description, create_date) SELECT id, parent_project_id, name, description, create_date FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE512E9BCC ON project (parent_project_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE73154ED4 ON project (leader_id)');
        $this->addSql('DROP INDEX IDX_BC716493BAD26311');
        $this->addSql('DROP INDEX IDX_BC7164938DB60186');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tag_task AS SELECT tag_id, task_id FROM tag_task');
        $this->addSql('DROP TABLE tag_task');
        $this->addSql('CREATE TABLE tag_task (tag_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(tag_id, task_id))');
        $this->addSql('INSERT INTO tag_task (tag_id, task_id) SELECT tag_id, task_id FROM __temp__tag_task');
        $this->addSql('DROP TABLE __temp__tag_task');
        $this->addSql('CREATE INDEX IDX_BC716493BAD26311 ON tag_task (tag_id)');
        $this->addSql('CREATE INDEX IDX_BC7164938DB60186 ON tag_task (task_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__team AS SELECT id, name FROM team');
        $this->addSql('DROP TABLE team');
        $this->addSql('CREATE TABLE team (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, user_id INTEGER DEFAULT NULL, leader_id INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO team (id, name) SELECT id, name FROM __temp__team');
        $this->addSql('DROP TABLE __temp__team');
        $this->addSql('CREATE INDEX IDX_C4E0A61F73154ED4 ON team (leader_id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61FA76ED395 ON team (user_id)');
        $this->addSql('DROP INDEX IDX_534E68808DB60186');
        $this->addSql('DROP INDEX IDX_534E6880A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__work AS SELECT id, task_id, user_id, description, start_date, end_date FROM work');
        $this->addSql('DROP TABLE work');
        $this->addSql('CREATE TABLE work (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO work (id, task_id, user_id, description, start_date, end_date) SELECT id, task_id, user_id, description, start_date, end_date FROM __temp__work');
        $this->addSql('DROP TABLE __temp__work');
        $this->addSql('CREATE INDEX IDX_534E68808DB60186 ON work (task_id)');
        $this->addSql('CREATE INDEX IDX_534E6880A76ED395 ON work (user_id)');
    }
}
