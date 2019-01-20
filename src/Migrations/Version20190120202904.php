<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190120202904 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE app_users ADD COLUMN show BOOLEAN DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_534E68808DB60186');
        $this->addSql('DROP INDEX IDX_534E6880A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__work AS SELECT id, task_id, user_id, description, start_date, end_date FROM work');
        $this->addSql('DROP TABLE work');
        $this->addSql('CREATE TABLE work (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL COLLATE BINARY, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_534E68808DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_534E6880A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO work (id, task_id, user_id, description, start_date, end_date) SELECT id, task_id, user_id, description, start_date, end_date FROM __temp__work');
        $this->addSql('DROP TABLE __temp__work');
        $this->addSql('CREATE INDEX IDX_534E68808DB60186 ON work (task_id)');
        $this->addSql('CREATE INDEX IDX_534E6880A76ED395 ON work (user_id)');
        $this->addSql('DROP INDEX IDX_BC716493BAD26311');
        $this->addSql('DROP INDEX IDX_BC7164938DB60186');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tag_task AS SELECT tag_id, task_id FROM tag_task');
        $this->addSql('DROP TABLE tag_task');
        $this->addSql('CREATE TABLE tag_task (tag_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(tag_id, task_id), CONSTRAINT FK_BC716493BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BC7164938DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO tag_task (tag_id, task_id) SELECT tag_id, task_id FROM __temp__tag_task');
        $this->addSql('DROP TABLE __temp__tag_task');
        $this->addSql('CREATE INDEX IDX_BC716493BAD26311 ON tag_task (tag_id)');
        $this->addSql('CREATE INDEX IDX_BC7164938DB60186 ON tag_task (task_id)');
        $this->addSql('DROP INDEX IDX_57698A6AA76ED395');
        $this->addSql('DROP INDEX IDX_57698A6A296CD8AE');
        $this->addSql('CREATE TEMPORARY TABLE __temp__role AS SELECT id, team_id, user_id, type FROM role');
        $this->addSql('DROP TABLE role');
        $this->addSql('CREATE TABLE role (id INTEGER NOT NULL, team_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, type VARCHAR(6) NOT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_57698A6A296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_57698A6AA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO role (id, team_id, user_id, type) SELECT id, team_id, user_id, type FROM __temp__role');
        $this->addSql('DROP TABLE __temp__role');
        $this->addSql('CREATE INDEX IDX_57698A6AA76ED395 ON role (user_id)');
        $this->addSql('CREATE INDEX IDX_57698A6A296CD8AE ON role (team_id)');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, project_id, name, priority, create_date, completion_date, deadline FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER NOT NULL, project_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, priority VARCHAR(2) NOT NULL COLLATE BINARY, create_date DATETIME NOT NULL, completion_date DATETIME DEFAULT NULL, deadline DATE NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, project_id, name, priority, create_date, completion_date, deadline) SELECT id, project_id, name, priority, create_date, completion_date, deadline FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('DROP INDEX IDX_2FB3D0EE296CD8AE');
        $this->addSql('DROP INDEX IDX_2FB3D0EE512E9BCC');
        $this->addSql('DROP INDEX UNIQ_2FB3D0EE5E237E06');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, parent_project_id, team_id, name, description, create_date FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, parent_project_id INTEGER DEFAULT NULL, team_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, description VARCHAR(255) NOT NULL COLLATE BINARY, create_date DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2FB3D0EE512E9BCC FOREIGN KEY (parent_project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2FB3D0EE296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project (id, parent_project_id, team_id, name, description, create_date) SELECT id, parent_project_id, team_id, name, description, create_date FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE296CD8AE ON project (team_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE512E9BCC ON project (parent_project_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE5E237E06 ON project (name)');
        $this->addSql('DROP INDEX IDX_9474526C8DB60186');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, task_id, user_id, text, date FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, text VARCHAR(255) NOT NULL COLLATE BINARY, date DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_9474526C8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment (id, task_id, user_id, text, date) SELECT id, task_id, user_id, text, date FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526C8DB60186 ON comment (task_id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('DROP INDEX IDX_BF5476CAA76ED395');
        $this->addSql('DROP INDEX IDX_BF5476CAF4E25B21');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notification AS SELECT id, user_id, who_id, date, show, link, type FROM notification');
        $this->addSql('DROP TABLE notification');
        $this->addSql('CREATE TABLE notification (id INTEGER NOT NULL, user_id INTEGER NOT NULL, who_id INTEGER NOT NULL, date DATETIME NOT NULL, show BOOLEAN NOT NULL, link VARCHAR(255) NOT NULL COLLATE BINARY, type VARCHAR(9) NOT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BF5476CAF4E25B21 FOREIGN KEY (who_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO notification (id, user_id, who_id, date, show, link, type) SELECT id, user_id, who_id, date, show, link, type FROM __temp__notification');
        $this->addSql('DROP TABLE __temp__notification');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF4E25B21 ON notification (who_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_C25028245126AC48');
        $this->addSql('DROP INDEX UNIQ_C250282435C246D5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__app_users AS SELECT id, name, last_name, mail, password FROM app_users');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('CREATE TABLE app_users (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO app_users (id, name, last_name, mail, password) SELECT id, name, last_name, mail, password FROM __temp__app_users');
        $this->addSql('DROP TABLE __temp__app_users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C25028245126AC48 ON app_users (mail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C250282435C246D5 ON app_users (password)');
        $this->addSql('DROP INDEX IDX_9474526C8DB60186');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, task_id, user_id, text, date FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, text VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO comment (id, task_id, user_id, text, date) SELECT id, task_id, user_id, text, date FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526C8DB60186 ON comment (task_id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('DROP INDEX IDX_BF5476CAA76ED395');
        $this->addSql('DROP INDEX IDX_BF5476CAF4E25B21');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notification AS SELECT id, user_id, who_id, date, show, link, type FROM notification');
        $this->addSql('DROP TABLE notification');
        $this->addSql('CREATE TABLE notification (id INTEGER NOT NULL, user_id INTEGER NOT NULL, who_id INTEGER NOT NULL, date DATETIME NOT NULL, show BOOLEAN NOT NULL, link VARCHAR(255) NOT NULL, type VARCHAR(9) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO notification (id, user_id, who_id, date, show, link, type) SELECT id, user_id, who_id, date, show, link, type FROM __temp__notification');
        $this->addSql('DROP TABLE __temp__notification');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF4E25B21 ON notification (who_id)');
        $this->addSql('DROP INDEX UNIQ_2FB3D0EE5E237E06');
        $this->addSql('DROP INDEX IDX_2FB3D0EE512E9BCC');
        $this->addSql('DROP INDEX IDX_2FB3D0EE296CD8AE');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project AS SELECT id, parent_project_id, team_id, name, description, create_date FROM project');
        $this->addSql('DROP TABLE project');
        $this->addSql('CREATE TABLE project (id INTEGER NOT NULL, parent_project_id INTEGER DEFAULT NULL, team_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, create_date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO project (id, parent_project_id, team_id, name, description, create_date) SELECT id, parent_project_id, team_id, name, description, create_date FROM __temp__project');
        $this->addSql('DROP TABLE __temp__project');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE5E237E06 ON project (name)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE512E9BCC ON project (parent_project_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE296CD8AE ON project (team_id)');
        $this->addSql('DROP INDEX IDX_57698A6A296CD8AE');
        $this->addSql('DROP INDEX IDX_57698A6AA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__role AS SELECT id, team_id, user_id, type FROM role');
        $this->addSql('DROP TABLE role');
        $this->addSql('CREATE TABLE role (id INTEGER NOT NULL, team_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, type VARCHAR(6) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO role (id, team_id, user_id, type) SELECT id, team_id, user_id, type FROM __temp__role');
        $this->addSql('DROP TABLE __temp__role');
        $this->addSql('CREATE INDEX IDX_57698A6A296CD8AE ON role (team_id)');
        $this->addSql('CREATE INDEX IDX_57698A6AA76ED395 ON role (user_id)');
        $this->addSql('DROP INDEX IDX_BC716493BAD26311');
        $this->addSql('DROP INDEX IDX_BC7164938DB60186');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tag_task AS SELECT tag_id, task_id FROM tag_task');
        $this->addSql('DROP TABLE tag_task');
        $this->addSql('CREATE TABLE tag_task (tag_id INTEGER NOT NULL, task_id INTEGER NOT NULL, PRIMARY KEY(tag_id, task_id))');
        $this->addSql('INSERT INTO tag_task (tag_id, task_id) SELECT tag_id, task_id FROM __temp__tag_task');
        $this->addSql('DROP TABLE __temp__tag_task');
        $this->addSql('CREATE INDEX IDX_BC716493BAD26311 ON tag_task (tag_id)');
        $this->addSql('CREATE INDEX IDX_BC7164938DB60186 ON tag_task (task_id)');
        $this->addSql('DROP INDEX IDX_527EDB25166D1F9C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, project_id, name, priority, create_date, completion_date, deadline FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER NOT NULL, project_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, priority VARCHAR(2) NOT NULL, create_date DATETIME NOT NULL, completion_date DATETIME DEFAULT NULL, deadline DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO task (id, project_id, name, priority, create_date, completion_date, deadline) SELECT id, project_id, name, priority, create_date, completion_date, deadline FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('DROP INDEX IDX_534E68808DB60186');
        $this->addSql('DROP INDEX IDX_534E6880A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__work AS SELECT id, task_id, user_id, description, start_date, end_date FROM work');
        $this->addSql('DROP TABLE work');
        $this->addSql('CREATE TABLE work (id INTEGER NOT NULL, task_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO work (id, task_id, user_id, description, start_date, end_date) SELECT id, task_id, user_id, description, start_date, end_date FROM __temp__work');
        $this->addSql('DROP TABLE __temp__work');
        $this->addSql('CREATE INDEX IDX_534E68808DB60186 ON work (task_id)');
        $this->addSql('CREATE INDEX IDX_534E6880A76ED395 ON work (user_id)');
    }
}
