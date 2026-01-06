CREATE TABLE admins(
    id VARCHAR(100) PRIMARY KEY NOT NULL,
    fullname VARCHAR(225) NOT NULL,
    password VARCHAR(225) NOT NULL,
    email VARCHAR(225) NOT NULL
);

CREATE TABLE users(
    id VARCHAR(100) PRIMARY KEY NOT NULL,
    fullname VARCHAR(225) NOT NULL,
    password VARCHAR(225) NOT NULL,
    email VARCHAR(225) NOT NULL,
    regNo  VARCHAR (10)
);


CREATE TABLE question_bank(
    id VARCHAR(100) PRIMARY KEY NOT NULL,
    course VARCHAR(225) NOT NULL,
    subject VARCHAR(225) NOT NULL,
    question VARCHAR(500) NOT NULL,
    options JSON,
    answer INT
);

CREATE TABLE  quiz_results(
    id VARCHAR(100) PRIMARY KEY NOT NULL,
    user_id VARCHAR(100) NOT NULL,
    subject VARCHAR(225) NOT NULL,
    score INT,
    quiz_taken_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES users(id)
);
