<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        li {
            list-style-type: none;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php 
        include 'db.php';

        // Обработка добавления
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['addTask'])) {
            $taskName = $_POST['taskName'];
            if (!empty($taskName)) {
                $result = pg_query($db, "INSERT INTO " . tableName . " (task_text) VALUES ('$taskName')");
            }
        }
        
        // Обработка удаления
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteTask'])) {
            $taskId = isset($_POST['deleteTask']) ? $_POST['deleteTask'] : null;
            if (!empty($taskId)) {
                pg_query($db, "DELETE FROM " . tableName . " WHERE id = '$taskId'");
            }
        }

        // Обработка редактирования
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editTask'])) {
            $taskId = isset($_POST['editTask']) ? $_POST['editTask'] : null;
            $editedTaskName = isset($_POST['editedTaskName']) ? $_POST['editedTaskName'] : null;
            if (!empty($taskId) && !empty($editedTaskName)) {
                pg_query($db, "UPDATE " . tableName . " SET task_text = '$editedTaskName' WHERE id = '$taskId'");
            }
        }
    ?>

    <h2>Checklist</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="taskName">Новая задача: </label>
        <input type="text" name="taskName" id="taskName">
        <input type="submit" name="addTask" value="Добавить">
    </form>

    <ul>
        <?php
            $result = pg_query($db, "SELECT * FROM " . tableName);
            while ($row = pg_fetch_assoc($result)) {
                $taskId = $row['id'];
                $taskName = $row['task_text'];
        ?>
                <li data-task-id="<?php echo $taskId; ?>">
                    <span class="task-text"><?php echo $taskName; ?></span>
                    <button class="editBtn">Редактировать</button>
                    <button class="deleteBtn">Удалить</button>
                </li>
        <?php
            }
        ?>
    </ul>

    <script>
        const list = document.querySelector('ul');

        list.addEventListener('click', function(e) {
            const target = e.target;
            const li = target.closest('li');

            if (!li) return;

            const taskId = li.getAttribute('data-task-id');
            const taskTextElement = li.querySelector('.task-text');

            if (target.classList.contains('editBtn')) {
                const newTaskName = prompt('Введите новое название задачи:', taskTextElement.textContent);

                if (newTaskName !== null) {
                    fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `editTask=${taskId}&editedTaskName=${encodeURIComponent(newTaskName)}`,
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                        taskTextElement.textContent = newTaskName;
                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                    });
                }
            } else if (target.classList.contains('deleteBtn')) {
                fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `deleteTask=${taskId}`,
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    li.remove();
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                });
            }
        });
    </script>
</body>
</html>
