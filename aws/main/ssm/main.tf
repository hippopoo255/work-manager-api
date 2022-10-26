# cognito_userpool
data "aws_cognito_user_pools" "app" {
  name = "${var.pj_name_kebab}-app"
}
data "aws_cognito_user_pools" "admin" {
  name = "${var.pj_name_kebab}-admin"
}
# rds
data "aws_db_instance" "mysql" {
  db_instance_identifier = var.pj_name_kebab
}

data "template_file" "params_structure" {
  template = file("${var.json_path_prefix}/ssm.json")
  vars = {
    cognito_userpool_id_app     = data.aws_cognito_user_pools.app.ids.0
    cognito_userpool_id_admin   = data.aws_cognito_user_pools.admin.ids.0
    cognito_test_user_app_sub   = var.cognito_test_user_app.sub
    cognito_test_user_admin_sub = var.cognito_test_user_admin.sub
    test_email                  = var.cognito_test_user_app.attributes.email
    test_id                     = var.cognito_test_user_app.username
    db_user                     = var.rds_credentials.db_user
    db_pass                     = var.rds_credentials.db_pass
    db_host                     = data.aws_db_instance.mysql.address
    db_name                     = data.aws_db_instance.mysql.db_name
    db_port                     = data.aws_db_instance.mysql.port
    db_connection               = "mysql"
  }
}

locals {
  param_list = jsondecode(data.template_file.params_structure.rendered).Parameters
}

resource "aws_ssm_parameter" "default" {
  count     = length(local.param_list)
  value     = local.param_list[count.index].Value
  name      = local.param_list[count.index].Name
  type      = local.param_list[count.index].Type
  overwrite = true
}