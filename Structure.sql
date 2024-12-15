CREATE DATABASE ChintanTrivia;

USE ChintanTrivia;

CREATE TABLE User (
    UserID INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR(255) DEFAULT NULL,
    Email VARCHAR(255) DEFAULT NULL,
    Password VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (UserID)
);

CREATE TABLE QuizGroups (
    GroupID INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR(255) DEFAULT NULL,
    Description VARCHAR(255) DEFAULT NULL,
    GroupCode VARCHAR(255) UNIQUE DEFAULT NULL,
    PRIMARY KEY (GroupID)
);

CREATE TABLE GroupMembers (
    UserID INT DEFAULT NULL,
    GroupID INT DEFAULT NULL,
    IsAdmin TINYINT(1) DEFAULT NULL
);

CREATE TABLE Quiz (
    QuizID INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR(255) DEFAULT NULL,
    Description VARCHAR(255) DEFAULT NULL,
    TimeLimit INT DEFAULT NULL,
    QuizCode VARCHAR(255) UNIQUE DEFAULT NULL,
    AdminID INT DEFAULT NULL,
    GroupID INT DEFAULT NULL,
    IsActive TINYINT(1) DEFAULT NULL,
    ShowAnswer TINYINT(1) DEFAULT NULL,
    StartDate DATE DEFAULT CURDATE(),
    StartTime TIME DEFAULT '00:00:00',
    DueDate DATE DEFAULT DATE_ADD(CURDATE(), INTERVAL 1 WEEK),
    DueTime TIME DEFAULT '23:59:59',
    PRIMARY KEY (QuizID)
);

CREATE TABLE Questions (
    QuestionID INT NOT NULL AUTO_INCREMENT,
    QuizID INT DEFAULT NULL,
    Name VARCHAR(255) DEFAULT NULL,
    Marks INT DEFAULT NULL,
    Negative_Marks INT DEFAULT 0,
    Time INT DEFAULT NULL,
    Type VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (QuestionID)
);

CREATE TABLE Options_MCQ (
    OptionID INT NOT NULL AUTO_INCREMENT,
    QuestionID INT DEFAULT NULL,
    Name VARCHAR(255) DEFAULT NULL,
    IsCorrect TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (OptionID)
);

CREATE TABLE Options_Fill (
    OptionID INT NOT NULL AUTO_INCREMENT,
    QuestionID INT DEFAULT NULL,
    Answer VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (OptionID)
);

INSERT INTO
    User (Name, Email, Password)
VALUES
    (
        'Amit Sharma',
        'amit.sharma@example.com',
        'password123'
    ),
    (
        'Priya Verma',
        'priya.verma@example.com',
        'password456'
    ),
    (
        'Rahul Singh',
        'rahul.singh@example.com',
        'password789'
    ),
    (
        'Anita Desai',
        'anita.desai@example.com',
        'passwordabc'
    );

INSERT INTO
    QuizGroups (Name, Description, GroupCode)
VALUES
    (
        'Indian History',
        'A group focused on Indian history',
        'IH001'
    ),
    (
        'General Knowledge India',
        'Trivia on Indian culture and general knowledge',
        'GKI002'
    );

INSERT INTO
    GroupMembers (UserID, GroupID, IsAdmin)
VALUES
    (1, 1, 1),
    (2, 1, 0),
    (3, 2, 1),
    (4, 2, 0);

INSERT INTO
    Quiz (
        Name,
        Description,
        TimeLimit,
        QuizCode,
        AdminID,
        GroupID,
        IsActive,
        ShowAnswer
    )
VALUES
    (
        'Indian Independence Quiz',
        'Quiz on Indian Independence history',
        30,
        'IIQ001',
        1,
        1,
        1,
        1
    ),
    (
        'Indian Culture Quiz',
        'Test your knowledge on Indian culture',
        40,
        'ICQ002',
        3,
        2,
        1,
        0
    );

INSERT INTO
    Questions (QuizID, Name, Marks, Negative_Marks, Time, Type)
VALUES
    (
        1,
        'Who was the first Prime Minister of India?',
        10,
        -2,
        30,
        'multiple-choice-single'
    ),
    (
        1,
        'Which year did India gain independence?',
        10,
        -2,
        30,
        'multiple-choice-single'
    ),
    (
        1,
        'Which of these leaders participated in the Salt March?',
        5,
        -1,
        45,
        'multiple-choice-multiple'
    ),
    (
        2,
        'What is the national animal of India?',
        5,
        -1,
        30,
        'multiple-choice-single'
    ),
    (
        2,
        'Name the festival known as the festival of lights.',
        5,
        -1,
        30,
        'fill-in-the-blank'
    );

INSERT INTO
    Options_MCQ (QuestionID, Name, IsCorrect)
VALUES
    (1, 'Jawaharlal Nehru', 1),
    (1, 'Mahatma Gandhi', 0),
    (1, 'Sardar Patel', 0),
    (1, 'Subhash Chandra Bose', 0),
    (2, '1947', 1),
    (2, '1950', 0),
    (2, '1930', 0),
    (2, '1945', 0),
    (3, 'Mahatma Gandhi', 1),
    (3, 'Jawaharlal Nehru', 0),
    (3, 'Sarojini Naidu', 1),
    (3, 'Bhimrao Ambedkar', 0),
    (4, 'Tiger', 1),
    (4, 'Elephant', 0),
    (4, 'Lion', 0),
    (4, 'Peacock', 0);

INSERT INTO
    Options_Fill (QuestionID, Answer)
VALUES
    (5, 'Diwali');

CREATE TABLE Results (
    QuizCode VARCHAR(10),
    UserID VARCHAR(255),
    Score FLOAT,
    TimeSpent INT,
    Submission DATETIME,
    GroupID INT,
    AttemptID INT AUTO_INCREMENT,
    PRIMARY KEY (AttemptID)
);

CREATE TABLE Attempts (
    AttemptID INT NOT NULL AUTO_INCREMENT,
    QuestionID INT,
    UserAnswer VARCHAR(255),
    CorrectAnswer VARCHAR(255),
    IsCorrect TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (AttemptID),
    FOREIGN KEY (QuestionID) REFERENCES Questions(QuestionID) ON DELETE CASCADE,
    FOREIGN KEY (AttemptID) REFERENCES Results(AttemptID) ON DELETE CASCADE
);