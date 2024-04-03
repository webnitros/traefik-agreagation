#!/bin/sh

# Указываем адрес Consul сервера и токен для доступа к API http://5.183.188.62:8500/v1/kv/consul/dev-portainer?raw
CONSUL_SERVER="5.183.188.62:8500"
CONSUL_TOKEN="df4a64f5-3023-b42b-c617-6a6bc6f2e068"

# Указываем название ключа с конфигурацией в Consul KV
KEY_NAME="consul/dev-portainer"

# Получаем .env конфигурацию из Consul
env_data=$(curl -sS -H "X-Consul-Token: $CONSUL_TOKEN" "http://$CONSUL_SERVER/v1/kv/$KEY_NAME?raw")

# Проверяем, получены ли данные
if [ -z "$env_data" ]; then
  echo "Ошибка: Не удалось получить .env конфигурацию из Consul."
  exit 1
fi


export $(echo "$env_data" | xargs)


echo "TYPE_SERVICE $TYPE_SERVICE"

# Экспортируем переменные окружения
#export $(echo "$env_data" | xargs)

#echo ".env конфигурация успешно загружена и экспортирована в окружение."
