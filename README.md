# Тестовое задание
## Описание проекта
____
Данный проект представляет собой небольшой API сервис для имитации оплаты с банковских карт. 
Состоит из back-части написанной на PHP и небольшой обёртки на фронте (ReactJs) для более лёгкого тестирования.
В данной системе присутствует два метода: 
- /register - Регистрация платежа(запись в БД)
- /getData - Получение всех данных из БД за переданный период
При успешной регистрации платежа создаётся id платёжной сессии с временем жизни 30 минут(доступ к реквизитам платежа по данной сесии будет доступен в течении этого времени).
Метод "/getData" получит данные вне зависимости от сессий.
## Установка проекта
____
Файлы проекта(собранный фронт и бэк часть с бд) находятся в папке ~~PaymentSystem~~.
Для успешного запуска необходимо:
- дать доступ к папке "PaymentSystem" вашему локальному серверу (OpenServer/xamp/...) как к новому сайту
- загрузить файл базы данных (PaymentSystem/datebase/payment_system.sql) в вашу локальную бд.
- в файле PaymentSystem/datebase/db.connection.php указать данные для подключения к БД(имя пользователя и пароль).
На этом API проекта будет работать. Так же можно воспользоваться фронтовой частью для облегченного тестирования
