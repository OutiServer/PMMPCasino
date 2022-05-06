-- #! sqlite

-- # { economy
-- # { casino
-- # { slot_configs
-- # { init
CREATE TABLE IF NOT EXISTS slot_configs
(
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    jp                    INTEGER NOT NULL,
    latest_jp_player_xuid TEXT    NOT NULL DEFAULT 'なし',
    latest_jp             INTEGER NOT NULL DEFAULT 0
);
-- # }

-- # { create
-- #    :jp int
INSERT INTO slot_configs (jp)
VALUES (:jp);
-- # }

-- # { load
SELECT *
FROM slot_configs;
-- # }

-- # { update
-- #    :jp int
-- #    :latest_jp_player_xuid string
-- #    :latest_jp int
-- #    :id int
UPDATE slot_configs
SET jp                    = :jp,
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
    parent_id  INTEGER NOT NULL,
    name       TEXT    NOT NULL,
    world_name TEXT    NOT NULL,
    x          INTEGER NOT NULL,
    y          INTEGER NOT NULL,
    z          INTEGER NOT NULL,
    bet        INTEGER NOT NULL,
    sideline   INTEGER NOT NULL
);
-- # }

-- # { create
-- #    :parent_id int
-- #    :name string
-- #    :world_name string
-- #    :x int
-- #    :y int
-- #    :z int
-- #    :bet int
-- #    :sideline int
INSERT INTO slots (parent_id, name, world_name, x, y, z, bet, sideline)
VALUES (:parent_id, :name, :world_name, :x, :y, :z, :bet, :sideline);
-- # }

-- # { load
SELECT *
FROM slots;
-- # }

-- # { update
-- #    :parent_id int
-- #    :name string
-- #    :world_name string
-- #    :x int
-- #    :y int
-- #    :z int
-- #    :bet int
-- #    :sideline int
-- #    :id int
UPDATE slots
SET parent_id  = :parent_id,
    name       = :name,
    world_name = :world_name,
    x          = :x,
    y          = :y,
    z          = :z,
    bet        = :bet,
    sideline   = :sideline
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