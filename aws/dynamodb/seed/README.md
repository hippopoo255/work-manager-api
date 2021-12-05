### DynamoDBにデータを流し込む

- テーブル作成

```bash:
aws dynamodb create-table --table-name 'blogs' \
--key-schema '
[
  {
    "AttributeName": "id",
    "KeyType": "HASH"
  }
]
' --attribute-definitions '
[
  {
    "AttributeName":"id",
    "AttributeType": "N"
  }
]
' --provisioned-throughput '
{
  "ReadCapacityUnits": 5,
  "WriteCapacityUnits": 5
}
'
```

同じ要領でtagsテーブルも作る

- シーディング

```bash:
aws dynamodb batch-write-item --request-items file://users.json
```

- テーブルの削除

```bash:
aws dynamodb delete-table --table-name users
```