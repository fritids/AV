CREATE OR REPLACE 
ALGORITHM = UNDEFINED
VIEW  `mv_orders` AS 
SELECT * 
FROM  `av_orders` 
WHERE  `current_state` <>  ''