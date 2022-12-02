locals {
  pj_name_kebab  = "work-manager"
  pj_name_snake  = "work_manager"
  pj_name_camel  = "WorkManager"
  pj_name_kana   = "ジョブサポ"
  domain_name    = "work-manager.site"
  default_region = "ap-northeast-1"
  env_types = {
    "prod" = {
      hosts : {
        app : "www",
        admin : "admin"
      },
      priority : 99
    }
    "stg" = {
      hosts : {
        app : "dev",
        admin : "admin"
      }
      priority : 100
    }
  }
}