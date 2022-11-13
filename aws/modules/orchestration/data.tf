data "terraform_remote_state" "init" {
  backend = "s3"

  config = {
    bucket = "${local.pj_name_kebab}-tfstate"
    key    = "${local.pj_name_kebab}/init/main.tfstate"
    region = "${local.default_region}"
  }
}