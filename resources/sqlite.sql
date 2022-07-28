-- #! sqlite

-- # { economy
-- # { casino
-- # { slot_configs
-- # { init
CREATE TABLE IF NOT EXISTS slot_configs
(
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    name                  TEXT    NOT NULL,
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
SET name                  = :name,
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
SET type      = :type,
    parent_id = :parent_id,
    name      = :name,
    bet       = :bet
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

-- # { gachas
-- # { init
CREATE TABLE IF NOT EXISTS gachas
(
    id    INTEGER PRIMARY KEY AUTOINCREMENT,
    name  TEXT    NOT NULL,
    price INTEGER NOT NULL
);
-- # }

-- # { create
-- #    :name string
-- #    :price int
INSERT INTO gachas (name, price)
VALUES (:name, :price);
-- # }

-- # { seq
SELECT seq
FROM sqlite_sequence
WHERE name = "gachas";
-- # }

-- # { load
SELECT * FROM gachas;
-- # }

-- # { update
-- #    :name string
-- #    :price int
-- #    :id int
UPDATE gachas
SET name      = :name,
    price = :price
WHERE id = :id;
-- # }

-- # { delete
-- #    :id int
DELETE
FROM gachas
WHERE id = :id;
-- # }

-- # { drop
DROP TABLE gachas;
-- # }
-- # }

-- # { gacha_items
-- # { init
CREATE TABLE IF NOT EXISTS gacha_items
(
    id    INTEGER PRIMARY KEY AUTOINCREMENT,
    gacha_id  INTEGER    NOT NULL,
    item_id INTEGER NOT NULL ,
    item_meta INTEGER NOT NULL,
    rand INTEGER NOT NULL,
    count INTEGER NOT NULL
);
-- # }

-- # { create
-- #    :gacha_id int
-- #    :item_id int
-- #    :item_meta int
-- #    :rand int
INSERT INTO gacha_items (gacha_id, item_id, item_meta, rand, count)
VALUES (:gacha_id, :item_id, :item_meta, :rand, :count);
-- # }

-- # { seq
SELECT seq
FROM sqlite_sequence
WHERE name = "gacha_items";
-- # }

-- # { load
SELECT * FROM gacha_items;
-- # }

-- # { update
-- #    :rand int
-- #    :count int
-- #    :id int
UPDATE gacha_items
SET rand = :rand,
    count      = :count
WHERE id = :id;
-- # }

-- # { delete
-- #    :id int
DELETE
FROM gacha_items
WHERE id = :id;
-- # }

-- # { drop
DROP TABLE gacha_items;
-- # }
-- # }
-- # }
-- # }