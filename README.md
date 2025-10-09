Рассчитано на Linux! Если нужна совместимость с другими ОС, форки/PR принимаются -))
# Настройка
1) Создать файл .env
```
cp .env.example .env
```
2) В Github сгенерировать новый access-токен (начинается с ```ghp_```), в файл ```.env``` положить в переменную ```GITHUB_TOKEN```.
3) В переменной ```GITHUB_REPOSITORIES``` прописать через запятую список репозиториев в формате ```владелец/репозиторий```. Например:
```
GITHUB_REPOSITORIES=dimanzver/pr-checker,dimanzver/font_compression
```
4) Если надо, в ```EXCLUDED_PULL_REQUEST_IDS``` можно положить id PR-ов, которые надо исключить из мониторинга:
```
EXCLUDED_PULL_REQUEST_IDS=7037,22666,23189,23282
```
5) Добавить скрипт в crontab, например, так:
```
0,30 * * * * php /home/user/pr-checker/index.php
```
6) Можно протестить на каком-нибудь PR с конфликтами. На всякий лучше дождаться выполнения по крону, с ним что-то в теории может что-то не работать. У меня сразу [не работало](https://github.com/dimanzver/pr-checker/commit/8f2fa413a35e9c010fde03dd19cac430cefc346b)
