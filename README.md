# WebAuthnSamplePHP

```
# コンテナ起動
docker-compose up -d

# コンテナ情報表示
docker ps

# PHPモジュールインストール
docker exec -it {コンテナ名} /bin/bash -c "cd /var/www/html && composer install"

open http://localhost:8000/
open http://localhost:4000/index.php?lang=ja
```