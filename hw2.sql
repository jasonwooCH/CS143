--1
(
	SELECT DISTINCT company-name
	FROM Company
)
EXCEPT 
(
	SELECT company-name FROM Work W
	WHERE salary <= 100000
);

--2a
SELECT person-name
FROM 
(
	SELECT person-name, SUM(salary) TotalSalary
	FROM Work W
	GROUP BY person-name
) T
WHERE TotalSalary > ALL
(
	SELECT SUM(salary) TotalSalary
	FROM Work W, Employee E
	WHERE E.city = 'Los Angeles' AND W.person-name = E.person-name
	GROUP BY W.person-name
);

--2b
SELECT manager-name
FROM
(
	SELECT manager-name, SUM(salary) TotalSalary
	FROM Manager M, Work W
	WHERE M.manager-name = W.person-name
	GROUP BY W.person-name
) T
WHERE TotalSalary > SOME
(
	SELECT SUM(salary) TotalSalary
	FROM Manager M, Work W
	WHERE M.person-name = W.person-name
	GROUP BY M.person-name
);

---------------------------------------------------------
--3ai
(
	SELECT name, address
	FROM MovieStar
	WHERE gender = 'F'
)
INTERSECT
(
	SELECT name, address
	FROM MovieExec
	WHERE netWorth > 1000000
);

--3aii
SELECT name, address
FROM MovieStar MS, MovieExec ME
WHERE MS.name = ME.name AND gender = 'F' AND netWorth > 1000000;

--3bi
(
	SELECT name
	FROM MovieStar
)
EXCEPT
(
	SELECT name
	FROM MovieExec
);

--3bii
SELECT name
FROM MovieStar
WHERE name NOT IN
(
	SELECT name
	FROM MovieExec
);

---------------------------------------------------------

--4a
SELECT AVG(speed)
FROM Desktop;

--4b
SELECT AVG(price)
FROM Laptop L, ComputerProduct CP
WHERE weight < 2 AND L.model = CP.model;

--4c
SELECT AVG(price)
FROM
(
	(
		SELECT price
		FROM Laptop L, ComputerProduct CP
		WHERE L.model = CP.model AND manufacturer = 'Dell'
	)
	UNION
	(
		SELECT price
		FROM Desktop D, ComputerProduct CP
		WHERE D.model = CP.model AND manufacturer = 'Dell'
	)
) T;

--4d
SELECT AVG(price)
FROM Laptop
GROUP BY speed;

--4e

SELECT manufacturer
FROM ComputerProduct
GROUP BY manufacturer
HAVING COUNT(*) >= 3;

---------------------------------------------------------
--5a
INSERT INTO ComputerProduct
VALUES ('HP', 1200, 1000);
INSERT INTO Desktop
VALUES (1200, 1.2, 256, 80);

--5b
DELETE FROM ComputerProduct
WHERE manufacturer = 'IBM' AND price <= 1000;
DELETE FROM Desktop
WHERE model NOT IN 
(
	SELECT model
	FROM ComputerProduct
);

--5c
UPDATE Laptop
SET weight = weight + 1
WHERE model in
(
	SELECT model
	FROM Laptop L, ComputerProduct CP
	WHERE L.model = CP.model AND manufacturer = 'Gateway'
);
