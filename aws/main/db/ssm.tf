resource "aws_ssm_parameter" "db_database" {
  value = aws_db_instance.default.db_name
  name = "DB_DATABASE"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "db_host" {
  value = aws_db_instance.default.address
  name = "DB_HOST"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "db_password" {
  value = var.db_pass
  name = "DB_PASSWORD"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "db_port" {
  value = var.db_port
  name = "DB_PORT"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "db_user" {
  value = var.db_user
  name = "DB_USERNAME"
  type = "SecureString"
  overwrite = true
}

resource "aws_ssm_parameter" "db_connection" {
  value = var.db_connection
  name = "DB_CONNECTION"
  type = "SecureString"
  overwrite = true
}