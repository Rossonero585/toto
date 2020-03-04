ALTER TABLE events ADD COLUMN p1_copy FLOAT(7,5) DEFAULT NULL;
ALTER TABLE events ADD COLUMN px_copy FLOAT(7,5) DEFAULT NULL;
ALTER TABLE events ADD COLUMN p2_copy FLOAT(7,5) DEFAULT NULL;

UPDATE events SET
  p1_copy = p1,
  px_copy = px,
  p2_copy = p2,
  p1 = s1,
  px = sx,
  p2 = s2
WHERE 1 = 1;

UPDATE events SET
  s1 = p1_copy,
  sx = px_copy,
  s2 = p2_copy
WHERE 1 = 1;

ALTER TABLE events DROP COLUMN p1_copy;
ALTER TABLE events DROP COLUMN px_copy;
ALTER TABLE events DROP COLUMN p2_copy;
