DROP VIEW IF EXISTS activity_leaderboards_this_month;
CREATE VIEW activity_leaderboards_this_month AS SELECT 
COUNT(log.id) AS count, player_id_owner, player.id AS player_id, player.name AS player_name
FROM player 
LEFT JOIN log ON player.id = log.player_id AND (
	acquired >= (curdate() - INTERVAL (DAYOFMONTH(curdate()) - 1) DAY)
	AND acquired <= curdate()
	AND log.accepted IS NOT NULL
)
GROUP BY player.id, player_id_owner
ORDER BY count DESC;

