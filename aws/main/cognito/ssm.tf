# userpool
resource "aws_ssm_parameter" "cognito_userpool_id_app" {
  value = aws_cognito_user_pool.app.id
  name = "AMAZON_USER_COGNITO_POOL_ID"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "cognito_userpool_id_admin" {
  value = aws_cognito_user_pool.admin.id
  name = "AMAZON_ADMIN_COGNITO_POOL_ID"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "cognito_region_app" {
  value = var.cognito_region
  name = "AMAZON_USER_COGNITO_REGION"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "cognito_region_admin" {
  value = var.cognito_region
  name = "AMAZON_ADMIN_COGNITO_REGION"
  type = "SecureString"
  overwrite = true
}

# user
resource "aws_ssm_parameter" "cognito_testuser_sub_app" {
  value = aws_cognito_user.test_user_app.sub
  name = "APP_TEST_USER_COGNITO_SUB"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "cognito_testuser_sub_admin" {
  value = aws_cognito_user.test_user_admin.sub
  name = "APP_TEST_ADMIN_COGNITO_SUB"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "app_test_mail" {
  value = aws_cognito_user.test_user_app.attributes.email
  name = "APP_TEST_MAIL"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "app_test_pass" {
  value = "NoEffect"
  name = "APP_TEST_PASS"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "app_test_id" {
  value = aws_cognito_user.test_user_app.username
  name = "APP_TEST_ID"
  type = "SecureString"
  overwrite = true
}