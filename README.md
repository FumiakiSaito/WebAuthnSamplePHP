# WebAuthnSamplePHP


```
docker-compose up -d
open http://localhost:8000/
open http://localhost:4000/index.php?lang=ja
docker-compose down

# コンテナ表示
docker ps
# コンテナに入る
docker exec -it {コンテナID} bash

# 滅びの呪文 (全削除)
docker-compose down --rmi all --volumes
```