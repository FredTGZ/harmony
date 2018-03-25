CREATE TABLE {DB_PREFIX}_RIGHTS(
	RIGHT_ID VARCHAR(30),
	RIGHT_NAME VARCHAR(50)
)

INSERT INTO {DB_PREFIX}_RIGHTS(RIGHT_ID, RIGHT_NAME) VALUES('admin', 'Administrator');
INSERT INTO {DB_PREFIX}_RIGHTS(RIGHT_ID, RIGHT_NAME) VALUES('sec_admin', 'Section Administrator');
INSERT INTO {DB_PREFIX}_RIGHTS(RIGHT_ID, RIGHT_NAME) VALUES('writer', 'Writer');
INSERT INTO {DB_PREFIX}_RIGHTS(RIGHT_ID, RIGHT_NAME) VALUES('censuror', 'Censuror');
