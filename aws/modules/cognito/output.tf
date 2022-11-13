# api_gatewayのオーソライザーに設定する
output "userpool_arn_app" {
  value = aws_cognito_user_pool.app.arn
}
output "userpool_arn_admin" {
  value = aws_cognito_user_pool.admin.arn
}

# Laravelのテストユーザ用
output "test_user_app" {
  value = aws_cognito_user.test_user_app
}
output "test_user_admin" {
  value = aws_cognito_user.test_user_admin
}