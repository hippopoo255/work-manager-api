variable "repos" {
  default = {
    1 = {
      name      = "web"
      image_dir = "nginx"
    },
    2 = {
      name      = "app"
      image_dir = "php"
    },
    3 = {
      name      = "supervisor"
      image_dir = "supervisor"
    }
  }
}

resource "aws_ecr_repository" "web" {
  for_each = var.repos
  name     = "${var.repo_name_prefix}/${lookup(each.value, "name")}"
}

resource "aws_ecr_lifecycle_policy" "web" {
  for_each   = var.repos
  repository = "${var.repo_name_prefix}/${lookup(each.value, "name")}"
  policy     = <<EOF
  {
    "rules": [
      {
        "rulePriority": 1,
        "description": "keep last 15 release tagged images",
        "selection": {
          "tagStatus": "tagged",
          "tagPrefixList": ["release"],
          "countType": "imageCountMoreThan",
          "countNumber": 15
        },
        "action": {
          "type": "expire"
        }
      }
    ]
  }
  EOF
}

data "aws_caller_identity" "self" {}

resource "null_resource" "push_images" {
  for_each = aws_ecr_repository.web

  triggers = {
    repos_url         = md5("${each.value.repository_url}:latest")
    life_cycle_policy = md5(aws_ecr_lifecycle_policy.web[each.key].repository)
  }

  provisioner "local-exec" {
    command = "$PWD/${var.sh_path_prefix}push_image.sh"

    environment = {
      AWS_DEFAULT_REGION = var.repos_region
      AWS_ACCOUNT_ID     = data.aws_caller_identity.self.account_id
      REPOSITORY_URL     = "${each.value.repository_url}:latest"
      IMAGE_DIR          = var.repos[each.key].image_dir
      PJ_ROOT_PATH       = var.pj_root_path
    }
  }
}