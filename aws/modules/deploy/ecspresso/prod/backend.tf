terraform {
  # ここでapplyしたtfstateをecspressoで使う
  # config:
  #   url: s3://work-manager-tfstate/work-manager/prod/deploy/ecspresso/data.tfstate
  backend "s3" {
    bucket = "work-manager-tfstate"
    key    = "work-manager/prod/deploy/ecspresso/data.tfstate"
    region = "ap-northeast-1"
  }
}