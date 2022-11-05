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

variable "repo_name_prefix" {
  type    = string
  default = "default"
}

variable "repos_region" {
  type    = string
  default = "ap-northeast-1"
}

variable "pj_root_path" {
  type = string
}