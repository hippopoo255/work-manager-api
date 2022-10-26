variable "domain_name" {
}
variable "pj_name_kana" {
}
variable "pj_name_kebab" {
}
variable "json_path_prefix" {
}
variable "cognito_test_user_app" {
  default = {
    attributes = {
      email            = ""
      email_verified   = ""
      family_name      = ""
      family_name_kana = ""
      given_name       = ""
      given_name_kana  = ""
      login_id         = ""
      sub              = ""
    }
    client_metadata          = null
    creation_date            = ""
    desired_delivery_mediums = null
    enabled                  = false
    force_alias_creation     = null
    id                       = ""
    last_modified_date       = ""
    message_action           = ""
    mfa_setting_list         = []
    password                 = null
    preferred_mfa_setting    = ""
    status                   = ""
    sub                      = ""
    temporary_password       = null
    user_pool_id             = ""
    username                 = ""
    validation_data          = null
  }
}
variable "cognito_test_user_admin" {
  default = {
    attributes = {
      email            = ""
      email_verified   = ""
      family_name      = ""
      family_name_kana = ""
      given_name       = ""
      given_name_kana  = ""
      login_id         = ""
      sub              = ""
    }
    client_metadata          = null
    creation_date            = ""
    desired_delivery_mediums = null
    enabled                  = false
    force_alias_creation     = null
    id                       = ""
    last_modified_date       = ""
    message_action           = ""
    mfa_setting_list         = []
    password                 = null
    preferred_mfa_setting    = ""
    status                   = ""
    sub                      = ""
    temporary_password       = null
    user_pool_id             = ""
    username                 = ""
    validation_data          = null
  }
}
variable "rds_credentials" {
  default = {
    db_user = ""
    db_pass = ""
  }
}