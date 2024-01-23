<?php

define("connectionString", "host=127.0.0.1 port=5432 dbname=postgres user=postgres password=eip238ps");
define("tableName", "\"Tasks\"");
$db = pg_connect(connectionString);

// Создаем таблицу, если она не существует
pg_query($db, 'CREATE TABLE IF NOT EXISTS "Tasks" (id SERIAL PRIMARY KEY, task_text TEXT)');
?>
