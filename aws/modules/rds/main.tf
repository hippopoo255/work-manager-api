resource "aws_db_parameter_group" "this" {
  name   = "${local.pj_name_kebab}-${data.aws_default_tags.this.tags.Env}"
  family = "mysql5.7"


  parameter {
    name  = "character_set_client"
    value = "utf8mb4"
  }

  parameter {
    name  = "character_set_connection"
    value = "utf8mb4"
  }

  parameter {
    name  = "character_set_database"
    value = "utf8mb4"
  }

  parameter {
    name  = "character_set_results"
    value = "utf8mb4"
  }

  parameter {
    name  = "character_set_server"
    value = "utf8mb4"
  }

  parameter {
    name  = "collation_connection"
    value = "utf8mb4_unicode_ci"
  }

  parameter {
    name  = "collation_server"
    value = "utf8mb4_unicode_ci"
  }
}

resource "aws_db_option_group" "this" {
  name                 = "${local.pj_name_kebab}-option-${data.aws_default_tags.this.tags.Env}"
  engine_name          = "mysql"
  major_engine_version = "5.7"

  option {
    option_name = "MARIADB_AUDIT_PLUGIN"
  }
}

module "mysql_sg" {
  source      = "../security_group"
  name        = "${local.pj_name_snake}_${local.db_connection}_sg_${data.aws_default_tags.this.tags.Env}"
  vpc_id      = data.terraform_remote_state.init.outputs.vpc_id
  from_port   = local.db_port
  to_port     = local.db_port
  cidr_blocks = [data.terraform_remote_state.init.outputs.vpc_cidr_block]
}

resource "aws_db_subnet_group" "this" {
  name       = "${local.pj_name_kebab}-${data.aws_default_tags.this.tags.Env}_${data.aws_default_tags.this.tags.Env}"
  subnet_ids = data.terraform_remote_state.init.outputs.subnet_ids
}

resource "aws_db_instance" "this" {
  identifier            = "${local.pj_name_kebab}-${data.aws_default_tags.this.tags.Env}"
  engine                = local.db_connection
  engine_version        = "5.7.39"
  instance_class        = "db.t2.micro"
  allocated_storage     = 20
  max_allocated_storage = 50
  storage_type          = "gp2"
  db_name               = local.pj_name_snake
  username              = var.db_user
  password              = var.db_pass
  # multi_az = true
  publicly_accessible        = false
  backup_window              = "03:10-03:40"
  backup_retention_period    = 7
  maintenance_window         = "mon:10:10-mon:10:40"
  auto_minor_version_upgrade = false
  # 本番では削除保護をtrueにする↓
  deletion_protection = false
  # destroy時は一旦trueにしてapply
  skip_final_snapshot    = true
  port                   = local.db_port
  apply_immediately      = false
  vpc_security_group_ids = [module.mysql_sg.security_group_id]
  parameter_group_name   = aws_db_parameter_group.this.name
  option_group_name      = aws_db_option_group.this.name
  db_subnet_group_name   = aws_db_subnet_group.this.name

  lifecycle {
    ignore_changes = [password]
  }

}