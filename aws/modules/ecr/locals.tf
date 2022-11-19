locals {
  repos = {
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
  max_image_count = 4
}