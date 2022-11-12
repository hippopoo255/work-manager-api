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
      # conditions
    },
    {
      name              = "stg",
      priority          = 100
      target_group_name = "stg"
      # conditions
    }
  ]
}