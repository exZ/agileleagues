DROP VIEW IF EXISTS player_activity_coins; 
CREATE VIEW player_activity_coins AS 
select 
`log`.`player_id` AS `player_id`,
`player`.`name` AS `player_name`,
count(0) AS `coins`,
`log`.`activity_id` AS `activity_id`,
`log`.`activity_code` AS `activity_code`,
`log`.`reviewed` AS `log_reviewed`,
`activity`.`name` AS `activity_name`,
`activity`.`description` AS `activity_description`,
`domain`.`id` AS `domain_id`,
`domain`.`name` AS `domain_name`,
`domain`.`abbr` AS `domain_abbr`,
`domain`.`color` AS `domain_color` 

from (((`log` 
	join `activity` on((`activity`.`id` = `log`.`activity_id`))) 
	join `player` on((`player`.`id` = `log`.`player_id`))) 
	join `domain` on((`domain`.`id` = `activity`.`domain_id`))) 
group by `log`.`activity_id`,`log`.`player_id` 

order by `log`.`player_id`,`activity`.`code`