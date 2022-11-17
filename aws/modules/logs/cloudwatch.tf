resource "aws_cloudwatch_log_group" "web_log" {
  name              = "${local.pj_name_snake}_web_log_${data.aws_default_tags.this.tags.Env}"
  retention_in_days = var.retention_days
}
resource "aws_cloudwatch_log_group" "app_log" {
  name              = "${local.pj_name_snake}_app_log_${data.aws_default_tags.this.tags.Env}"
  retention_in_days = var.retention_days
}
resource "aws_cloudwatch_log_group" "supervisor_log" {
  name              = "${local.pj_name_snake}_supervisor_log_${data.aws_default_tags.this.tags.Env}"
  retention_in_days = var.retention_days
}