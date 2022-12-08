locals {
  tables = [
    {
      name = "sequences",
      partition_key = {
        name = "table_name",
        type = "S"
      }
    },
    {
      name = "tags",
      partition_key = {
        name = "id",
        type = "S"
      }
    },
    {
      name = "blogs",
      partition_key = {
        name = "id",
        type = "S"
      }
    },
  ]
}