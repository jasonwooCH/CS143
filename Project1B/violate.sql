-- Movie primary key constraint
-- movie with id = 2 already exists in the table
INSERT INTO Movie 
VALUES (2, 'test', 2015, 'PG-13', 'Warner Bros.');
-- Output: Duplicate entry '2' for key 'PRIMARY'

-- Movie Check constraint
-- id cannot be negative
INSERT INTO Movie 
VALUES (-2, 'test', 2015, 'PG-13', 'Warner Bros.');

-- Actor primary key constraint
-- actor with id = 1 already exists
INSERT INTO Actor 
VALUES (1, 'test', 'name', 'Female', '1993-06-16 00:00:00', NULL);
-- Output: Duplicate entry '1' for key 'PRIMARY'

-- actor check constraints
-- id cannot be negative
INSERT INTO Actor 
VALUES (-11, 'test', 'name', 'Female', '1993-06-16 00:00:00', NULL);
-- dob cannot be future
INSERT INTO Actor 
VALUES (2, 'test', 'name', 'Female', '2018-06-16 00:00:00', NULL);

--Director primary key constraint
--  Director id 76 already exists in the table
INSERT INTO Director 
VALUES (76, 'test', 'John', '1993-06-16 00:00:00', NULL);
-- Output: Duplicate entry '76' for key 'PRIMARY'

-- Director check constraints
-- id cannot be negative
INSERT INTO Director 
VALUES (-11, 'test', 'name', '1993-06-16 00:00:00', NULL);
-- dob cannot be future
INSERT INTO Director 
VALUES (2, 'test', 'name','2018-06-16 00:00:00', NULL);

-- MovieGenre foreign key constraint
-- Movie with id 7 does not exist in Movie table
UPDATE MovieGenre
SET mid = 7;
-- Output: Cannot add or update a child row: a foreign key constraint
--	fails ('TEST'.'MovieGenre'. CONSTRAINT 'MovieGenre_ibfk_1'
--	FOREIGN KEY ('mid') REFERENCES 'Movie' ('id'))

-- MovieDirector foreign key constraint
-- Movie with id 7 does not exist in Movie table
UPDATE MovieDirector
SET mid = 7;
-- Output: Cannot add or update a child row: a foreign key constraint
--	fails ('TEST'.'MovieDirector'. CONSTRAINT 'MovieDirector_ibfk_1'
--	FOREIGN KEY ('mid') REFERENCES 'Movie' ('id'))

-- Director with id 7 does not exist in Director table
UPDATE MovieDirector
SET did = 7;
-- Output: Cannot add or update a child row: a foreign key constraint
--	fails ('TEST'.'MovieDirector'. CONSTRAINT 'MovieDirector_ibfk_2'
--	FOREIGN KEY ('did') REFERENCES 'Director' ('id'))

-- MovieActor foreign key constraint
-- Movie with id 7 does not exist in Movie table
UPDATE MovieActor
SET mid = 7;
-- Output: Cannot add or update a child row: a foreign key constraint
--	fails ('TEST'.'MovieActor'. CONSTRAINT 'MovieActor_ibfk_1'
--	FOREIGN KEY ('mid') REFERENCES 'Movie' ('id'))

-- Actor with id 2 does not exist in Actor table
UPDATE MovieActor
SET aid = 2;
-- Output: Cannot add or update a child row: a foreign key constraint
--	fails ('TEST'.'MovieActor'. CONSTRAINT 'MovieActor_ibfk_2'
--	FOREIGN KEY ('aid') REFERENCES 'Actor' ('id'))

-- Review table foreign key constraint
-- Movie with id 7 does not exist in Movie table
INSERT INTO Review
VALUES ('review', '2015-06-16 00:00:00', 7, 3, NULL);
-- Output: Cannot add or update a child row: a foreign key constraint
--	fails ('TEST'.'Review'. CONSTRAINT 'Review_ibfk_1'
--	FOREIGN KEY ('mid') REFERENCES 'Movie' ('id'))

-- Review check constraints
-- Rating cannot be outside the range 0-5
INSERT INTO Review 
VALUES ('test', '2015-06-16 00:00:00', 2, -4, NULL);