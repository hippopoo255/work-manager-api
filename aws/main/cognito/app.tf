resource "aws_cognito_user_pool" "app" {
  name = "${var.pj_name_kebab}-app"

  auto_verified_attributes = [
    "email",
  ]
  alias_attributes = [
    "email"
  ]

  # email_verification_subject = "【${var.pj_name_kana}】検証コード"
  mfa_configuration = "OFF"
  admin_create_user_config {
    # ユーザーに自己サインアップを許可する。
    allow_admin_create_user_only = false
    invite_message_template {
      email_message = " ユーザー名は {username}、仮パスワードは {####} です。"
      email_subject = " 仮パスワード"
      sms_message   = " ユーザー名は {username}、仮パスワードは {####} です。"
    }
  }

  email_configuration {
    # Cognito or SES
    # Default: Cognito
    email_sending_account = "COGNITO_DEFAULT"
  }

  # パスワードのルール
  password_policy {
    minimum_length                   = 8
    require_lowercase                = true  # 小文字
    require_numbers                  = true  # 数字
    require_symbols                  = false # 記号
    require_uppercase                = true  # 大文字
    temporary_password_validity_days = 7     # 初期登録時の一時的なパスワードの有効期限
  }

  schema {
    attribute_data_type = "String"
    name                = "email"
    required            = true
  }

  # custom_attribute: login_id
  schema {
    attribute_data_type      = "String"
    developer_only_attribute = false
    mutable                  = true
    name                     = "login_id"
    required                 = false

    string_attribute_constraints {
      max_length = "255"
      min_length = "1"
    }
  }

  # custom_attribute: family_name_kana
  schema {
    attribute_data_type      = "String"
    developer_only_attribute = false
    mutable                  = true
    name                     = "family_name_kana"
    required                 = false

    string_attribute_constraints {
      max_length = "255"
      min_length = "1"
    }
  }

  # custom_attribute: given_name_kana
  schema {
    attribute_data_type      = "String"
    developer_only_attribute = false
    mutable                  = true
    name                     = "given_name_kana"
    required                 = false

    string_attribute_constraints {
      max_length = "255"
      min_length = "1"
    }
  }

  username_configuration {
    # ユーザー名で大文字小文字を区別しない。
    case_sensitive = false
  }

  # Eメール検証メッセージのカスタマイズ
  verification_message_template {
    # CONFIRM_WITH_CODE or CONFIRM_WITH_LINK
    default_email_option = "CONFIRM_WITH_CODE"
    email_message        = " 検証コードは {####} です。"
    email_subject        = "【${var.pj_name_kana}】検証コード"
  }

  lifecycle {
    # plan時に変更をかけていなくても差分として表示されるため除外
    ignore_changes = [
      password_policy,
      schema
    ]
  }
}

resource "aws_cognito_user_pool_client" "app" {
  name         = "${var.pj_name_camel}ClientApp"
  user_pool_id = aws_cognito_user_pool.app.id

  # 「クライアントシークレットを生成」のチェックボックスを外す
  generate_secret = false

  prevent_user_existence_errors = "ENABLED"

  # 認証フローの設定
  explicit_auth_flows = [
    "ALLOW_ADMIN_USER_PASSWORD_AUTH",
    "ALLOW_CUSTOM_AUTH",
    "ALLOW_USER_SRP_AUTH",
    "ALLOW_REFRESH_TOKEN_AUTH"
  ]

  # 属性の読み取り有無設定。
  read_attributes = [
    "birthdate",
    "email",
    "email_verified",
    "family_name",
    "gender",
    "given_name",
    "locale",
    "middle_name",
    "name",
    "nickname",
    "phone_number",
    "picture",
    "preferred_username",
    "profile",
    "zoneinfo",
    "updated_at",
    "website",
    "custom:family_name_kana",
    "custom:given_name_kana",
  ]

  # 属性の書き込み有無設定。
  write_attributes = [
    "address",
    "birthdate",
    "email",
    "family_name",
    "gender",
    "given_name",
    "locale",
    "middle_name",
    "name",
    "nickname",
    "phone_number",
    "picture",
    "preferred_username",
    "profile",
    "zoneinfo",
    "updated_at",
    "website",
    "custom:family_name_kana",
    "custom:given_name_kana",
    "custom:login_id",
  ]
}

resource "aws_cognito_identity_pool" "app" {
  identity_pool_name               = "${var.pj_name_kebab}-userpool"
  allow_unauthenticated_identities = false

  cognito_identity_providers {
    client_id               = aws_cognito_user_pool_client.app.id
    provider_name           = "cognito-idp.ap-northeast-1.amazonaws.com/${aws_cognito_user_pool.app.id}"
    server_side_token_check = false
  }
}

resource "aws_cognito_user" "test_user_app" {
  user_pool_id = aws_cognito_user_pool.app.id
  username     = var.test_username

  attributes = {
    email            = var.test_email
    email_verified   = true
    family_name      = "テスト"
    given_name       = "太郎"
    family_name_kana = "テスト"
    given_name_kana  = "タロウ"
    login_id         = var.test_username
  }

  message_action = "SUPPRESS"
}

resource "null_resource" "test_user_app_confirmed" {
  triggers = {
    endpoint = "${aws_cognito_user.test_user_app.sub}"
  }

  provisioner "local-exec" {
    command = "$PWD/${var.sh_path_prefix}confirmed_cognito_user.sh"

    environment = {
      COGNITO_USERPOOL_ID = aws_cognito_user_pool.app.id
      COGNITO_USERNAME    = aws_cognito_user.test_user_app.username
      COGNITO_PASSWORD    = var.test_userpassword
    }
  }
}