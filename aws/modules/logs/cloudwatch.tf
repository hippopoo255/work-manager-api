resource "aws_cloudwatch_log_group" "web_log" {
  name              = "${local.pj_name_snake}_web_log"
  retention_in_days = var.retention_days
}
resource "aws_cloudwatch_log_group" "app_log" {
  name              = "${local.pj_name_snake}_app_log"
  retention_in_days = var.retention_days
}
resource "aws_cloudwatch_log_group" "supervisor_log" {
  name              = "${local.pj_name_snake}_supervisor_log"
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