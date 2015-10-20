-- Movie primary key constraint
-- movie with id = 2 already exists in the table
INSERT INTO Movie 
VALUES (2, 'test', 2015, 'PG-13', 'Warner Bros.');

-- Movie Check constraint
-- id cannot be negative
INSERT INTO Movie 
VALUES (-2, 'test', 2015, 'PG-13', 'Warner Bros.');

-- Actor primary key constraint
-- actor with id = 1 already exists
INSERT INTO Actor 
VALUES (1, 'test', 'name', 'Female', '1993-06-16 00:00:00', NULL);

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

-- Director check constraints
-- id cannot be negative
INSERT INTO Director 
VALUES (-11, 'test', 'name', '1993-06-16 00:00:00', NULL);
-- dob cannot be future
INSERT INTO Director 
VALUES (2, 'test', 'name','2018-06-16 00:00:00', NULL);

-- MovieGenre foreign key constraint
-- by default, update is not allowed if foreign key constraint is violated
UPDATE MovieGenre
SET mid = 7;

-- MovieDirector foreign key constraint
-- by default, update is not allowed if foreign key constraint is violated
UPDATE MovieDirector
SET mid = 7, did = 69;

-- MovieActor foreign key constraint
-- by default, update is not allowed if foreign key constraint is violated
UPDATE MovieActor
SET mid = 93, aid = 69;

-- Review table foreign key constraint
-- by default, update is not allowed if foreign key constraint is violated
UPDATE Review
SET mid = 93;

-- Review check constraints
-- Rating cannot be outside the range 0-5
INSERT INTO Review 
VALUES ('test', '1993-06-16 00:00:00', 2, -4, NULL);