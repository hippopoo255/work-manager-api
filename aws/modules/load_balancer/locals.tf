locals {
  target_group_list = [
    {
      name = "prod"
    },
    {
      name = "stg",
    },
  ]
  listener_rules = [
    {
      name              = "prod",
      priority          = 99
      target_group_name = "prod"
      header_origin = [
        "https://www.${local.domain_name}",
        "https://admin.${local.domain_name}"
      ]
    },
    {
      name              = "stg",
      priority          = 100
      target_group_name = "stg"
      header_origin     = ["https://dev.${local.domain_name}"]
    }
  ]
}