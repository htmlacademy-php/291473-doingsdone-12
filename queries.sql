  
-- Добавляет пользователей;
-- password_hash('Q4B4e6Ap', PASSWORD_DEFAULT); // $2y$10$feI4UmS8vwRUqjGuvBkNheLQ4eL5KI4PW8YN11SmhzRi9ELC0ehUq
-- password_hash('gQHVnixF', PASSWORD_DEFAULT); // $2y$10$7uOaL/PzYWwnhT9Ly2mz9.6tjORSkt7D2RZZ/ZO8mlozZnpLcy9Dm
INSERT INTO users (registration_date, email, user_name, password) VALUES ('2021-04-24 17:40:25', 'dkrech07@gmail.com', 'dkrech07', '$2y$10$feI4UmS8vwRUqjGuvBkNheLQ4eL5KI4PW8YN11SmhzRi9ELC0ehUq'),
                                                                         ('2021-03-20 12:30:05', 'larisa@gmail.com', 'larisa', '$2y$10$7uOaL/PzYWwnhT9Ly2mz9.6tjORSkt7D2RZZ/ZO8mlozZnpLcy9Dm');

-- Добавляет список проектов;
INSERT INTO projects (project_name, user_id) VALUES ('Входящие', 1), ('Учеба', 1), ('Работа', 1), ('Домашние дела', 1), ('Авто', 2);

-- Добавляет список задач;
INSERT INTO tasks (create_date, status, task_name, file_link, deadline, user_id, project_id) VALUES ('2021-04-19 12:30:25', 0, 'Собеседование в IT компании', '', '2021-06-24 12:30:25', 1, 3),
                                                                                                    ('2021-04-20 13:00:30', 0, 'Выполнить тестовое задание', '', '2021-06-25 13:00:30', 1, 3),
                                                                                                    ('2021-04-21 13:30:35', 1, 'Сделать задание первого раздела', '', '2021-06-26 13:30:35', 1, 2),
                                                                                                    ('2021-04-22 14:00:40', 0, 'Собеседование в IT компании', '', '2021-06-27 14:00:40', 1, 1),
                                                                                                    ('2021-04-23 14:30:45', 0, 'Купить корм для кота', '', '2021-06-28 14:30:45', 2, 1),
                                                                                                    ('2021-04-24 15:00:50', 0, 'Заказать пиццу', '', '2021-06-28 15:00:50', 2, 1);