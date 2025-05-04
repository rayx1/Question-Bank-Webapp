-- install.sql content:

DROP TABLE IF EXISTS exam_history;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS admins;

CREATE TABLE `questions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `subject` VARCHAR(255) NOT NULL,
  `year` VARCHAR(4) NOT NULL,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `file_path` VARCHAR(255) DEFAULT NULL,
  `probability` FLOAT DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Insert a default admin user.
-- The password is stored as MD5 hash for demonstration purposes.
INSERT INTO admins (username, password) VALUES ('admin', MD5('admin123'));

CREATE TABLE `exam_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `exam_date` DATE NOT NULL,
  `question_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`question_id`) REFERENCES questions(`id`)
) ENGINE=InnoDB;

-- Add a full-text index for the question column.
ALTER TABLE questions ADD FULLTEXT(question);
