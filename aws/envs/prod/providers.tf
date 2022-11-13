provider "aws" {
  region = "ap-northeast-1"

  default_tags {
    tags = {
      Env    = "prod"
      System = "work-manager"
    }
  }
}

terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "4.29.0"
      # version = "3.42.0"
    }

    # template, azure, and more...
  }

  required_version = "1.0.0"
}