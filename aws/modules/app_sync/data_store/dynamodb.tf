resource "aws_dynamodb_table" "this" {
  for_each = { for t in local.tables : t.name => t }

  name           = each.key
  billing_mode   = "PROVISIONED"
  read_capacity  = 10
  write_capacity = 10
  hash_key       = each.value.patition_key.name

  attribute {
    name = each.value.patition_key.name
    type = each.value.patition_key.type
  }
}

resource "null_resource" "batch_items" {
  for_each = { for t in local.tables : t.name => t }

  triggers = {
    batch_item_file = md5(file("${path.module}/seed/${each.key}.json"))
  }

  provisioner "local-exec" {
    command = "${path.module}/batch_write_item.sh"

    environment = {
      MODULE_PATH = "${path.module}/seed/${each.key}"
    }
  }

  depends_on = [aws_dynamodb_table.this]
}
