resource "aws_cloudwatch_log_group" "this" {
  for_each = toset(local.repos)

  name              = "${local.pj_name_snake}_${each.key}_log_${data.aws_default_tags.this.tags.Env}"
  retention_in_days = var.retention_days
}