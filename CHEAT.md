```
# コンテナ表示
docker ps

# コンテナに入る
docker exec -it {コンテナID} bash

# 停止&削除
docker-compose down

# 滅びの呪文 (全削除)
docker-compose down --rmi all --volumes
```