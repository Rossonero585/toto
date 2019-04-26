DROP PROCEDURE IF EXISTS `toto`;
DELIMITER //
CREATE PROCEDURE `toto` ()
BLOCK1: BEGIN

    DECLARE my_id int(11);
    DECLARE my_r1 VARCHAR(10);
    DECLARE my_r2 VARCHAR(10);
    DECLARE my_r3 VARCHAR(10);
    DECLARE my_r4 VARCHAR(10);
    DECLARE my_r5 VARCHAR(10);
    DECLARE my_r6 VARCHAR(10);
    DECLARE my_r7 VARCHAR(10);
    DECLARE my_r8 VARCHAR(10);
    DECLARE my_r9 VARCHAR(10);
    DECLARE my_r10 VARCHAR(10);
    DECLARE my_r11 VARCHAR(10);
    DECLARE my_r12 VARCHAR(10);
    DECLARE my_r13 VARCHAR(10);
    DECLARE my_r14 VARCHAR(10);
    
	DECLARE finished1 INTEGER DEFAULT 0;
    
	DECLARE prepared_result_cursor CURSOR FOR 
    SELECT
		id,
		r1,
        r2,
        r3,
        r4,
        r5,
        r6,
        r7,
        r8,
        r9,
        r10,
        r11,
        r12,
        r13,
        r14
    FROM prepared_result LIMIT 100;
    
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished1 = 1;
	

-- 	SET @get_break_down = CONCAT('
-- 	SELECT
-- 	  (
-- 		IF(r1 = ?, 1, 0) +
-- 		IF(r2 = ?, 1, 0) +
-- 		IF(r3 = ?, 1, 0) +
-- 		IF(r4 = ?, 1, 0) +
-- 		IF(r5 = ?, 1, 0) +
-- 		IF(r6 = ?, 1, 0) +
-- 		IF(r7 = ?, 1, 0) +
-- 		IF(r8 = ?, 1, 0) +
-- 		IF(r9 = ?, 1, 0) +
-- 		IF(r10 = ?, 1, 0) +
-- 		IF(r11 = ?, 1, 0) +
-- 		IF(r12 = ?, 1, 0) +
-- 		IF(r13 = ?, 1, 0) +
-- 		IF(r14 = ?, 1, 0)
-- 	  ) AS winComb, COUNT(*), SUM(money)
-- 	FROM `pool` GROUP BY winComb');
--     
    SET @insert_break_down = CONCAT('INSERT INTO prepared_result_breakdown 
    (`prepared_result_id`, `win_comb`, `count`, `money`) VALUES (?,?,?,?)');
--     
--     prepare get_break_down FROM @get_break_down;
    prepare insert_break_down FROM @insert_break_down;-- 

    OPEN prepared_result_cursor;
    
	prepared_result: LOOP
    
		FETCH prepared_result_cursor INTO my_id, my_r1, my_r2, my_r3, my_r4, my_r5, my_r6, my_r7, my_r8, my_r9, my_r10, my_r11, my_r12, my_r13, my_r14;
        
		IF finished1 = 1 THEN 
			LEAVE prepared_result;
		END IF;
	
        
        BLOCK2: BEGIN
			DECLARE my_winners INTEGER;
            
			DECLARE my_money DECIMAL(8,2);
            
			DECLARE my_winCount INTEGER;
    
			DECLARE finished2 INTEGER DEFAULT 0;
            
			DECLARE break_down_cursor CURSOR FOR  
				SELECT
				  (
					IF(`r1` = my_r1, 1, 0) +
					IF(`r2` = my_r2, 1, 0) +
					IF(`r3` = my_r3, 1, 0) +
					IF(`r4` = my_r4, 1, 0) +
					IF(`r5` = my_r5, 1, 0) +
					IF(`r6` = my_r6, 1, 0) +
					IF(`r7` = my_r7, 1, 0) +
					IF(`r8` = my_r8, 1, 0) +
					IF(`r9` = my_r9, 1, 0) +
					IF(`r10` = my_r10, 1, 0) +
					IF(`r11` = my_r11, 1, 0) +
					IF(`r12` = my_r12, 1, 0) +
					IF(`r13` = my_r13, 1, 0) +
					IF(`r14` = my_r14, 1, 0)
				  ) AS winComb, COUNT(*), SUM(money)
				FROM `pool` GROUP BY winComb;
        
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished2 = 1;
        
			OPEN break_down_cursor;
        
			break_down: LOOP
        
				FETCH break_down_cursor INTO my_winCount, my_winners, my_money;
                
				IF finished2 = 1 THEN 
					LEAVE break_down;
                    SET finished2 = 0;
				END IF;
                
                -- SELECT my_winCount, my_winners, my_money; 
                
                IF (my_winCount >= 9) THEN
					SET @my_id = my_id;
					SET @my_winCount = my_id;
					SET @my_winners = my_winners;
					SET @my_money = my_money;
                    execute insert_break_down using @my_id, @my_winCount, @my_winners, @my_money;
                END IF;
                


			END loop break_down;
			CLOSE break_down_cursor;
        END BLOCK2;
			
        


-- 	SET @r1 = '1';
-- 	SET @r2 = '2';
-- 	SET @r3 = '1';
-- 	SET @r4 = '2';
-- 	SET @r5 = '1';
-- 	SET @r6 = 'x';
-- 	SET @r7 = '1';
-- 	SET @r8 = '2';
-- 	SET @r9 = '1';
-- 	SET @r10 = '1';
-- 	SET @r11 = '2';
-- 	SET @r12 = '1';
-- 	SET @r13 = '2';
-- 	SET @r14 = '1';

	
	end loop prepared_result;
    CLOSE prepared_result_cursor;
END BLOCK1//
DELIMITER ;
