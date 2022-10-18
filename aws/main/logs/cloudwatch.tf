resource "aws_cloudwatch_log_group" "web_log" {
  name = "${var.name_prefix}_web_log"
  # name = var.web_log_name
  retention_in_days = var.retention_days
}
resource "aws_cloudwatch_log_group" "app_log" {
  # name = "re_app_log"
  name              = "${var.name_prefix}_app_log"
  retention_in_days = var.retention_days
}
resource "aws_cloudwatch_log_group" "supervisor_log" {
  # name = var.supervisor_log_name
  name              = "${var.name_prefix}_supervisor_log"
  retention_in_days = var.retention_days
}

output "log_group_name_web" {
  value = aws_cloudwatch_log_group.web_log.name
}
output "log_group_name_app" {
  value = aws_cloudwatch_log_group.app_log.name
}
output "log_group_name_supervisor" {
  value = aws_cloudwatch_log_group.supervisor_log.name
}