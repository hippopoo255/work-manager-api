output "log_groups" {
  value = [
    for k, v in aws_cloudwatch_log_group.this : v.name
  ]

  depends_on = [aws_cloudwatch_log_group.this]
}