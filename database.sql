CREATE TABLE users(
	id int(255) AUTO_INCREMENT NOT null,
    role varchar(20) NOT null,
    name varchar(255) NOT null,
    surname varchar(255) NOT null,
    password varchar(255) NOT null,
    created_at datetime DEFAULT null,
    updated_at datetime DEFAULT null,
    remember_token varchar(255),
    CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=INNODB