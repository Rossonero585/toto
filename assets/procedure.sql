CREATE PROCEDURE `new_procedure` ()
BEGIN
	declare r1 enum('1', 'x', '2');
	declare r2 enum('1', 'x', '2');
	declare r3 enum('1', 'x', '2');
	declare r4 enum('1', 'x', '2');
	declare r5 enum('1', 'x', '2');
	declare r6 enum('1', 'x', '2');
	declare r7 enum('1', 'x', '2');
	declare r8 enum('1', 'x', '2');
	declare r9 enum('1', 'x', '2');
	declare r10 enum('1', 'x', '2');
	declare r12 enum('1', 'x', '2');
	declare r13 enum('1', 'x', '2');
	declare r14 enum('1', 'x', '2');

	PREPARE get_break_down FROM '
	SELECT
	  (
		IF(r1 = ?, 1, 0) +
		IF(r2 = ?, 1, 0) +
		IF(r3 = ?, 1, 0) +
		IF(r4 = ?, 1, 0) +
		IF(r5 = ?, 1, 0) +
		IF(r6 = ?, 1, 0) +
		IF(r7 = ?, 1, 0) +
		IF(r8 = ?, 1, 0) +
		IF(r9 = ?, 1, 0) +
		IF(r10 = ?, 1, 0) +
		IF(r11 = ?, 1, 0) +
		IF(r12 = ?, 1, 0) +
		IF(r13 = ?, 1, 0) +
		IF(r14 = ?, 1, 0)
	  ) AS winComb, COUNT(*), SUM(money)
	FROM `pool` GROUP BY winComb';

	SET r1 = '1';
	SET r2 = '2';
	SET r3 = '1';
	SET r4 = '2';
	SET r5 = '1';
	SET r6 = 'x';
	SET r7 = '1';
	SET r8 = '2';
	SET r9 = '1';
	SET r10 = '1';
	SET r11 = '2';
	SET r12 = '1';
	SET r13 = '2';
	SET r14 = '1';

	execute get_break_down using @r1, @r2, @r3, @r4, @r5, @r6, @r7, @r8, @r9, @r10, @r11, @r12, @r13, @r14;

END
