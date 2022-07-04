-- #! sqlite

-- # { economy
-- # { casino
-- # { slot_configs
-- # { init
CREATE TABLE IF NOT EXISTS slot_configs
(
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL ,
    jp                    INTEGER NOT NULL,
    latest_jp_player_xuid TEXT    NOT NULL DEFAULT 'なし',
    latest_jp             INTEGER NOT NULL DEFAULT 0
);
-- # }

-- # { create
-- #    :name string
-- #    :jp int
INSERT INTO slot_configs (name, jp)
VALUES (:name, :jp);
-- # }

-- # { seq
SELECT seq
FROM sqlite_sequence
WHERE name = "slot_configs";
-- # }

-- # { load
SELECT *
FROM slot_configs;
-- # }

-- # { update
-- #    :name string
-- #    :jp int
-- #    :latest_jp_player_xuid string
-- #    :latest_jp int
-- #    :id int
UPDATE slot_configs
SET name = :name,
    jp                    = :jp,
    latest_jp_player_xuid = :latest_jp_player_xuid,
    latest_jp             = :latest_jp
WHERE id = :id;
-- # }

-- # { delete
-- #    :id int
DELETE
FROM slot_configs
WHERE id = :id;
-- # }

-- # { drop
DROP TABLE IF EXISTS slot_configs;
-- # }
-- # }

-- # { slots
-- # { init
CREATE TABLE IF NOT EXISTS slots
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    type       INTEGER NOT NULL,
    parent_id  INTEGER NOT NULL,
    name       TEXT    NOT NULL,
    world_name TEXT    NOT NULL,
    x          INTEGER NOT NULL,
    y          INTEGER NOT NULL,
    z          INTEGER NOT NULL,
    bet        INTEGER NOT NULL
);
-- # }

-- # { create
-- #    :type int
-- #    :parent_id int
-- #    :name string
-- #    :world_name string
-- #    :x int
-- #    :y int
-- #    :z int
-- #    :bet int
INSERT INTO slots (type, parent_id, name, world_name, x, y, z, bet)
VALUES (:type, :parent_id, :name, :world_name, :x, :y, :z, :bet);
-- # }

-- # { seq
SELECT seq
FROM sqlite_sequence
WHERE name = "slots";
-- # }

-- # { load
SELECT *
FROM slots;
-- # }

-- # { update
-- #    :type int
-- #    :parent_id int
-- #    :name string
-- #    :bet int
-- #    :id int
UPDATE slots
SET type       = :type,
    parent_id  = :parent_id,
    name       = :name,
    bet        = :bet
WHERE id = :id;
-- # }

-- # { delete
-- #    :id int
DELETE
FROM slots
WHERE id = :id;
-- # }

-- # { drop
DROP TABLE IF EXISTS slots;
-- # }
-- # }
-- # }
-- # }