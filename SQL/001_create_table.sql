CREATE DATABASE team1;

USE team1;

CREATE TABLE informations (
	cat_no		INT			PRIMARY KEY AUTO_INCREMENT
	,PROFILE		VARCHAR(50)	NOT NULL		
	,NAME			VARCHAR(50)	NOT NULL
	,birth_at	DATE			NOT NULL
	,gender		CHAR(1)		NOT NULL		COMMENT '0 : 남자, 1: 여자'
	,weight		INT         NOT NULL		COMMENT '단위 kg'
	,adopt_at	DATE 			NOT NULL 
	,created_at	DATETIME		NOT NULL		DEFAULT CURRENT_TIMESTAMP()
	,deleted_at	DATETIME		NULL
);

CREATE TABLE todos (
	list_no		INT			PRIMARY KEY AUTO_INCREMENT
	,cat_no		INT			NOT NULL
	,todo_date	DATE			NOT NULL
	,content		VARCHAR(50)	NOT NULL 
	,checked		CHAR(1)		NOT NULL		COMMENT '0 : 미완료, 1: 완료'
	,created_at	DATETIME		NOT NULL		DEFAULT CURRENT_TIMESTAMP()
	,updated_at	DATETIME		NOT NULL		DEFAULT CURRENT_TIMESTAMP()
	,deleted_at	DATETIME		NULL 
);

ALTER TABLE todos ADD CONSTRAINT fk_todos_cat_no FOREIGN KEY (cat_no) REFERENCES informations(cat_no);