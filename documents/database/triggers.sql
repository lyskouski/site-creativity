-- save all historical changes for the main table `content` into `content_history`

DROP TRIGGER IF EXISTS content_save;

delimiter //
CREATE TRIGGER content_save
BEFORE UPDATE ON content
FOR EACH ROW
BEGIN
    INSERT INTO content_history (id, content_id, pattern, language, type, access, updated_at, author_id, auditor_id, content, search)
    VALUES (OLD.id, OLD.content_id, OLD.pattern, OLD.language, OLD.type, OLD.access, OLD.updated_at, OLD.author_id, OLD.auditor_id, OLD.content, OLD.search);

    INSERT INTO content_new (content_id, pattern, language, type, access, updated_at, author_id, auditor_id, content)
    SELECT id AS content_id, pattern, language, type, access, updated_at, NULL AS author_id, NULL AS auditor_id, content
    FROM content
    WHERE `content_id` = NEW.id AND `type` = OLD.type;
END;//
delimiter ;

-- count how many pages/topics contain current page inside  - table `content_views`

DROP TRIGGER IF EXISTS content_views_update;

delimiter //
CREATE TRIGGER content_views_update
AFTER INSERT ON content
FOR EACH ROW
BEGIN
	DECLARE newID BIGINT DEFAULT 0;
	DECLARE sPattern VARCHAR(255) DEFAULT '';
	DECLARE nID BIGINT DEFAULT 0;
	DECLARE i INT DEFAULT 1;
	DECLARE sDelim CHAR(1) DEFAULT '/';

	IF (NEW.type = 'og:title' AND NEW.pattern NOT LIKE '%/search/%') THEN
		SET newID = (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='content') - 1;

		fCicle: LOOP
			SET sPattern = SUBSTRING_INDEX(NEW.pattern, sDelim, i);
			SET i = i + 1;
			IF (NEW.pattern = sPattern) THEN
				LEAVE fCicle;
			END IF;
			SET nID = (SELECT `id` FROM `content`
				WHERE
					`pattern` = sPattern
					AND `language` = NEW.`language`
					AND `type` = NEW.`type`
				LIMIT 1);
			IF (nID IS NOT NULL) THEN
				INSERT INTO `content_views` (`content_id`, `content_count`, `visitors`,`votes_up`, `votes_down`)
				VALUES (nID, 1, 1, 1, 0)
				ON DUPLICATE KEY UPDATE
					`content_count` = `content_count` + 1;
			END IF;
		END LOOP fCicle;

		INSERT INTO `content_views` (`content_id`, `content_count`, `visitors`,`votes_up`, `votes_down`)
		VALUES (newID, 1, 1, 1, 0)
		ON DUPLICATE KEY UPDATE `content_count` = `content_count` + 1;
	END IF;

END;//
delimiter ;
