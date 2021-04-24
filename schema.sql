CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(128) NOT NULL,
    user_name VARCHAR(128) NOT NULL,
    password CHAR(64) NOT NULL,
    UNIQUE INDEX email(email),
    UNIQUE INDEX user_name(user_name)
);

CREATE TABLE projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    project_name VARCHAR(128) NOT NULL,
    user_name INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_name) REFERENCES users(id),
    UNIQUE INDEX project_name(project_name)
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 0,
    task_name VARCHAR(128) NOT NULL,
    file_link VARCHAR(128),
    deadline DATETIME NOT NULL,
    user_name INT UNSIGNED NOT NULL,
    project_name INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_name) REFERENCES users(id),
    FOREIGN KEY (project_name) REFERENCES projects(id)
);