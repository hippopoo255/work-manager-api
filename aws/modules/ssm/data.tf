data "aws_default_tags" "this" {}

# cognito_userpool
data "aws_cognito_user_pools" "app" {
  name = "${local.pj_name_kebab}-app-${data.aws_default_tags.this.tags.Env}"
}
data "aws_cognito_user_pools" "admin" {
  name = "${local.pj_name_kebab}-admin-${data.aws_default_tags.this.tags.Env}"
}
# rds
data "aws_db_instance" "mysql" {
  db_instance_identifier = "${local.pj_name_kebab}-${data.aws_default_tags.this.tags.Env}"
}

data "template_file" "list_access_keys" {
  template = file("${path.module}/s3-access-key-list.json")

  depends_on = [null_resource.list_access_key]
}

data "template_file" "s3_user_credentials" {
  template = file("${path.module}/s3-access-user-credentials.json")

  depends_on = [null_resource.create_access_key]
}

data "template_file" "params_structure" {
  template = file("${path.module}/ssm.json")
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
    host_name                   = local.env_types[data.aws_default_tags.this.tags.Env].hosts.app
    admin_host_name             = local.env_types[data.aws_default_tags.this.tags.Env].hosts.admin
    domain_name                 = local.domain_name
    pj_name_kana                = local.pj_name_kana
    namespace                   = data.aws_default_tags.this.tags.Env
    s3_access_key_id = jsondecode(data.template_file.s3_user_credentials.rendered).AccessKey.AccessKeyId
    s3_secret_access_key = jsondecode(data.template_file.s3_user_credentials.rendered).AccessKey.SecretAccessKey
  }
}