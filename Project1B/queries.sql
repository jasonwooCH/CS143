-- first attempt
-- SELECT first, last
-- FROM Actor
-- WHERE id IN
-- (	SELECT aid id
-- 		FROM Movie M, MovieActor MA
--		WHERE title='Die Another Day' AND
--		  	  M.id = MA.mid	);

-- First Q answer
SELECT first, last
FROM Actor A, MovieActor MA, Movie M
WHERE MA.mid = M.id AND title = 'Die Another Day' AND MA.aid = A.id;


-- Second Q
SELECT COUNT(*)
FROM ( SELECT aid
	   FROM MovieActor
	   GROUP BY aid
	   HAVING COUNT(*) >= 2) S;