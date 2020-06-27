CREATE OR REPLACE VIEW results AS SELECT
    t.id AS toto_id,
    t.start_date AS start_date,
    t.pot AS pot,
    t.pool_deviation AS pool_deviation,
    SUM(bi.money) AS bet,
    SUM(bi.income) AS income,
    AVG(bi.ev) AS ev,
    AVG(bi.probability) AS probability,
    MAX(bi.count_match) AS max_match,
    bets.is_test AS is_test
FROM bets
    LEFT JOIN bet_items bi on bets.id = bi.bet_id
    LEFT JOIN toto t on bets.toto_id = t.id
WHERE
    bi.count_match IS NOT NULL
GROUP BY bets.id;


