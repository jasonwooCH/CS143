CREATE TABLE Movie (
    id INT NOT NULL, 
    title VARCHAR(100), 
    year INT, 
    rating VARCHAR(10), 
    company VARCHAR(50),
    PRIMARY KEY(id),
    CHECK (id >= 0)
    -- every movie has a unique id number
) ENGINE=InnoDB;

CREATE TABLE Actor (
	id INT NOT NULL, 
	last VARCHAR(20) NOT NULL, 
	first VARCHAR(20) NOT NULL, 
	sex VARCHAR(6), 
	dob DATE, 
	dod DATE,
	PRIMARY KEY(id),
	-- every actor has a unique id number
	CHECK (id >= 0 AND 
		   dob <= '2015-10-20 00:00:00')
	-- check that id is not negative
	-- and dob is before today
) ENGINE=InnoDB;

CREATE TABLE Director (
	id INT NOT NULL, 
	last VARCHAR(20) NOT NULL, 
	first VARCHAR(20) NOT NULL, 
	dob DATE, 
	dod DATE,
	PRIMARY KEY(id),
	-- every director has a unique id number
	CHECK(id >= 0 AND
		  dob <= '2015-10-20 00:00:00')
	-- check that id is not negative
	-- and dob is before today
) ENGINE=InnoDB;

CREATE TABLE MovieGenre (
	mid INT,
	-- mid references Movie.id as a foreign key
	genre VARCHAR(20),
	FOREIGN KEY(mid) REFERENCES Movie(id)
) ENGINE=InnoDB;

CREATE TABLE MovieDirector (
	mid INT REFERENCES Movie(id), 
	-- mid references Movie.id as a foreign key
	did INT REFERENCES Director(id)
	-- did references Director.id as a foreign key
) ENGINE=InnoDB;

CREATE TABLE MovieActor (
	mid INT REFERENCES Movie(id), 
	-- mid references Movie.id as a foreign key
	aid INT REFERENCES Actor(id), 
	-- aid references Actor.id as a foreign key
	role VARCHAR(50)
) ENGINE=InnoDB;

CREATE TABLE Review (
	name VARCHAR(20), 
	time TIMESTAMP, 
	mid INT REFERENCES Movie(id), 
	-- mid references Movie.id as a foreign key
	rating INT, 
	comment VARCHAR(500),
	CHECK (rating BETWEEN 0 AND 5)
	-- check that rating is between 0 and 5
) ENGINE=InnoDB;

CREATE TABLE MaxPersonID( id INT) ENGINE=InnoDB;

CREATE TABLE MaxMovieID( id INT) ENGINE=InnoDB;