provider "aws" {
  region = "ap-northeast-1"
  # alias  = "tokyo"
}

provider "aws" {
  alias  = "virginia"
  region = "us-east-1"
}

terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "4.39.0"
      # version = "3.42.0"
    }

    # template, azure, and more...
  }

  required_version = "1.0.0"
}