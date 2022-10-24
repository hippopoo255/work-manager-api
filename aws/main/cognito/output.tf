output "userpool_arn_app" {
  value = aws_cognito_user_pool.app.arn
}
output "userpool_arn_admin" {
  value = aws_cognito_user_pool.admin.arn
}