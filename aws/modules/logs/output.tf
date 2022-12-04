output "log_groups" {
  value = aws_cloudwatch_log_group.this

  depends_on = [aws_cloudwatch_log_group.this]
}