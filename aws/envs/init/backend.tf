terraform {
  backend "s3" {
    bucket = "work-manager-tfstate"
    key    = "work-manager/init/main.tfstate"
    region = "ap-northeast-1"
  }
}