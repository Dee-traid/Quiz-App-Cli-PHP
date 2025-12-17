Create table admins(
    id varchar(100) primary key not null,
    email varchar(100) not null,
    password varchar(5000)  
);

Create table users(

);

Create table question_bank(
    id varchar(100) primary key not null,
    course varchar(225) not null,
    subject varchar(225) not null,
    question varchar(225) not null, 
    option JSON,
    answer int
);