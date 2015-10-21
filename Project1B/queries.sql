-- First Query answer
SELECT CONCAT_WS(' ', first, last)
FROM Actor A, MovieActor MA, Movie M
WHERE MA.mid = M.id AND title = 'Die Another Day' AND MA.aid = A.id;


-- Second Query
SELECT COUNT(*)
FROM (SELECT aid
	  FROM MovieActor
	  GROUP BY aid
	  HAVING COUNT(*) >= 2) S;

-- Our Query
-- Give me the first and last names of directors who also acted in the movies they directed
SELECT first, last
FROM Director
WHERE id IN (SELECT MD.did
	   		 FROM MovieDirector MD, MovieActor MA
	   		 WHERE MD.did = MA.aid AND MD.mid = MA.mid);
