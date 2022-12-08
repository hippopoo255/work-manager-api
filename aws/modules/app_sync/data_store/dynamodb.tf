resource "aws_dynamodb_table" "this" {
  for_each = { for t in local.tables : t.name => t }

  name           = each.key
  billing_mode   = "PROVISIONED"
  read_capacity  = 5
  write_capacity = 5
  hash_key       = each.value.partition_key.name

  attribute {
    name = each.value.partition_key.name
    type = each.value.partition_key.type
  }
}

resource "aws_appautoscaling_target" "table_read_capacity" {
  for_each = { for t in local.tables : t.name => t }

  max_capacity       = 10
  min_capacity       = 1
  resource_id        = "table/${each.key}"
  scalable_dimension = "dynamodb:table:ReadCapacityUnits"
  service_namespace  = "dynamodb"
}

resource "aws_appautoscaling_target" "table_write_capacity" {
  for_each = { for t in local.tables : t.name => t }

  max_capacity       = 10
  min_capacity       = 1
  resource_id        = "table/${each.key}"
  scalable_dimension = "dynamodb:table:WriteCapacityUnits"
  service_namespace  = "dynamodb"
}

resource "aws_appautoscaling_policy" "dynamodb_table_read_policy" {
  for_each = { for t in local.tables : t.name => t }
  name               = "DynamoDBReadCapacityUtilization:${aws_appautoscaling_target.table_read_capacity[each.key].resource_id}"
  policy_type        = "TargetTrackingScaling"
  resource_id        = aws_appautoscaling_target.table_read_capacity[each.key].resource_id
  scalable_dimension = aws_appautoscaling_target.table_read_capacity[each.key].scalable_dimension
  service_namespace  = aws_appautoscaling_target.table_read_capacity[each.key].service_namespace

  target_tracking_scaling_policy_configuration {
    predefined_metric_specification {
      predefined_metric_type = "DynamoDBReadCapacityUtilization"
    }

    target_value = 70
  }
}

resource "aws_appautoscaling_policy" "dynamodb_table_write_policy" {
  for_each = { for t in local.tables : t.name => t }
  name               = "DynamoDBWriteCapacityUtilization:${aws_appautoscaling_target.table_write_capacity[each.key].resource_id}"
  policy_type        = "TargetTrackingScaling"
  resource_id        = aws_appautoscaling_target.table_write_capacity[each.key].resource_id
  scalable_dimension = aws_appautoscaling_target.table_write_capacity[each.key].scalable_dimension
  service_namespace  = aws_appautoscaling_target.table_write_capacity[each.key].service_namespace

  target_tracking_scaling_policy_configuration {
    predefined_metric_specification {
      predefined_metric_type = "DynamoDBWriteCapacityUtilization"
    }

    target_value = 70
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