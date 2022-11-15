output "log_group_name_web" {
  value = aws_cloudwatch_log_group.web_log.name
}
output "log_group_name_app" {
  value = aws_cloudwatch_log_group.app_log.name
}
output "log_group_name_supervisor" {
  value = aws_cloudwatch_log_group.supervisor_log.name
}