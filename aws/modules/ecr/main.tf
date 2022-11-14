resource "aws_ecr_repository" "web" {
  for_each     = { for r in local.repos : r.name => r }
  name         = "${local.pj_name_kebab}/${data.aws_default_tags.this.tags.Env}/${each.key}"
  force_delete = true
}

resource "aws_ecr_lifecycle_policy" "web" {
  for_each   = { for r in local.repos : r.name => r }
  repository = aws_ecr_repository.web[each.key].name
  policy     = <<EOF
  {
    "rules": [
      {
        "rulePriority": 1,
        "description": "keep last 10 release tagged images",
        "selection": {
          "tagStatus": "tagged",
          "tagPrefixList": ["release"],
          "countType": "imageCountMoreThan",
          "countNumber": 10
        },
        "action": {
          "type": "expire"
        }
      }
    ]
  }
  EOF
}

resource "null_resource" "push_images" {
  for_each   = { for r in local.repos : r.name => r }

  triggers = {
    repos_url         = md5("${aws_ecr_repository.web[each.key].repository_url}:latest")
    life_cycle_policy = md5(aws_ecr_lifecycle_policy.web[each.key].policy)
  }

  provisioner "local-exec" {
    command = "${path.module}/push_image.sh"

    environment = {
      AWS_DEFAULT_REGION = local.default_region
      AWS_ACCOUNT_ID     = data.aws_caller_identity.self.account_id
      REPOSITORY_URL     = "${aws_ecr_repository.web[each.key].repository_url}:latest"
      IMAGE_DIR          = each.value.image_dir
      PJ_ROOT_PATH       = var.laravel_pj_root_path
    }
  }
}