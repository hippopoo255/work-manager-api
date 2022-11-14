locals {
  tables = [
    {
      name = "sequences",
      patition_key = {
        name = "table_name",
        type = "S"
      }
    },
    {
      name = "tags",
      patition_key = {
        name = "id",
        type = "S"
      }
    },
    {
      name = "blogs",
      patition_key = {
        name = "id",
        type = "S"
      }
    },
  ]
}