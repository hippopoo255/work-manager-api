variable "vpc_tag_name" {
  type    = string
  default = "default"
}

variable "rtb_tag_name" {
  type    = string
  default = "default"
}

variable "vpc_cidr_block" {
  type    = string
  default = "10.0.0.0/16"
}

variable "public_subnets" {
  default = {
    0 = {
      cidr_block        = "10.0.1.0/24"
      availability_zone = "ap-northeast-1a"
      name              = "work-manager-public0"
    }
    1 = {
      cidr_block        = "10.0.2.0/24"
      availability_zone = "ap-northeast-1c"
      name              = "work-manager-public1"
    }
  }
}

variable "private_subnets" {
  default = {
    0 = {
      cidr_block        = "10.0.61.0/24"
      availability_zone = "ap-northeast-1a"
      name              = "work-manager-private0"
    }
    1 = {
      cidr_block        = "10.0.62.0/24"
      availability_zone = "ap-northeast-1c"
      name              = "work-manager-private1"
    }
  }
}